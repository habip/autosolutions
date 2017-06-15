<?php
namespace AppBundle\Notifier;

use AppBundle\Entity\CarOwnerRequest;
use AppBundle\Entity\Message\Message;
use AppBundle\Entity\Message\MessageStatus;
use AppBundle\Entity\User;
use Thrift\Transport\TMemoryBuffer;
use Thrift\Transport\THttpClient;
use Thrift\Protocol\TJSONProtocol;
use Thrift\Protocol\TBinaryProtocol;
use IIT\Intergrid\Thrift\IntergridClient;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use AppBundle\Serializer\Exclusion\IdOnlyExclusionStrategy;
use AppBundle\Entity\Company;
use AppBundle\Security\Signer\Signer;

class Notifier
{
    private $context;
    private $serializer;
    private $em;
    private $address;
    private $socket;
    private $signer;
    private $backendUri;
    private $phoneUtil;

    public function __construct($doctrine, Serializer $serializer, $zmqAddress, Signer $signer, $backendUri, $phoneUtil)
    {
        $this->em         = $doctrine->getManager();
        $this->serializer = $serializer;
        $this->address    = $zmqAddress;
        $this->signer     = $signer;
        $this->backendUri = $backendUri;
        $this->phoneUtil  = $phoneUtil;
    }

    /**
     * @return \ZMQSocket
     */
    private function getSocket()
    {
        if (null === $this->socket){
            $this->context = new \ZMQContext();
            $this->socket = $this->context->getSocket(\ZMQ::SOCKET_PUSH);
            $this->socket->connect($this->address);
        }

        return $this->socket;
    }

    public function send($to, $message, $changeId = null, $action = null)
    {
        $toJson = $this->serializer->serialize($to, 'json');

        $multi = array($toJson, $message);
        if ($changeId) {
            $multi[] = $changeId;
            if ($action) {
                $multi[] = $action;
            }
        }
        $this->getSocket()->sendMulti($multi);
    }


    public function sendOverThrift(Company $company, $type, $message)
    {
        if ($company->getDbHost()) {
            $transport = new THttpClient($company->getDbHost(), 80, '/app_dev.php/thrift/intergrid');
            $protocol = new TBinaryProtocol($transport);
            $client = new IntergridClient($protocol);
            $client->postEvent($this->signer->sign($message), $type, $message);
        }
    }

    public function notifyRequest(CarOwnerRequest $request)
    {
        $r = new \IIT\Intergrid\Thrift\Request();
        $r->id              = $request->getId();
        $carOwner           = $request->getCarOwner();
        $user               = $carOwner->getUser();
        $r->user            = new \IIT\Intergrid\Thrift\Client(array(
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => sprintf('%s %s', $carOwner->getFirstName(), $carOwner->getLastName()),
                'accountType' => \IIT\Intergrid\Thrift\AccountType::CAR_OWNER,
                'clientType' => \IIT\Intergrid\Thrift\ClientType::PERSON,
                'person' => new \IIT\Intergrid\Thrift\Person(array(
                        'firstName' => $carOwner->getFirstName(),
                        'lastName' => $carOwner->getLastName(),
                )),
                'phone' => $user->getPhone() ? $this->phoneUtil->format($user->getPhone(), \libphonenumber\PhoneNumberFormat::E164) : $request->getPhone(),
                'registrationDate' => $user->getRegistrationDate()->getTimestamp()
        ));
        $company            = $request->getCarService()->getCompany();
        $r->company         = new \IIT\Intergrid\Thrift\Client(array(
                'id' => $company->getUser()->getId(),
                'email' => $company->getUser()->getEmail(),
                'name' => $company->getServiceName(),
                'accountType' => \IIT\Intergrid\Thrift\AccountType::COMPANY,
                'phone' => $company->getPhone(),
                'registrationDate' => $user->getRegistrationDate()->getTimestamp()
        ));
        $r->carService      = new \IIT\Intergrid\Thrift\CarService(array(
                'id' => $request->getCarService()->getId(),
                'name' => $request->getCarService()->getName()));
        $r->createdDateTime = $request->getAddedTimestamp()->getTimestamp();
        $r->desiredDateTime = $request->getCarOwnerDate()->getTimestamp();
        $r->desiredTimePeriod = $request->getCarOwnerTimePeriod();
        $r->phone           = $request->getPhone();
        $r->email           = $request->getEmail();
        $r->description     = $request->getDescription();
        $r->car = new \IIT\Intergrid\Thrift\Car();
        $r->car->id    = $request->getCar()->getId();
        $r->car->brand = $request->getCar()->getBrand()->getName();
        $r->car->model = $request->getCar()->getModel()->getName();
        $r->car->number = $request->getCar()->getNumber();
        $r->car->year  = intval($request->getCar()->getYear());
        $r->car->mileage = intval($request->getCar()->getMileage());

        $r->services = array();
        foreach ($request->getServices() as $service) {
            $r->services[] = new \IIT\Intergrid\Thrift\Service(array('id' => $service->getId(), 'name' => $service->getName()));
        }

        $r->reasons = array();
        foreach ($request->getReasons() as $reason) {
            $r->reasons[] = new \IIT\Intergrid\Thrift\ServiceReason(array('id' => $reason->getId(), 'name' => $reason->getName()));
        }

        $transport = new TMemoryBuffer();
        $protocol = new TJSONProtocol($transport);
        $r->write($protocol);

        //$this->send('tcp://127.0.0.1:5551', $transport->getBuffer());
        $this->sendOverThrift($company, 'Request', $transport->getBuffer());
    }

    public function notifyUserOnlineChanged(User $user)
    {
        $userIds = array();

        $interlocutors = $this->em->getRepository('AppBundleMessage:Dialog')
            ->findInterlocutors($user, true /* onlineOnly */);

        foreach ($interlocutors as $interlocutor) {
            $userIds[] = $interlocutor->getId();
        }

        $userIds = array_unique($userIds);

        $scontext = new SerializationContext();
        $scontext->setGroups(array('Default'));

        $msgJson = $this->serializer->serialize(array('user' => $user), 'json', $scontext);

        $this->send($userIds, $msgJson);
    }

    public function notifyUserLogout(User $user, $sessionId)
    {
        $scontext = new SerializationContext();
        $msgJson = $this->serializer->serialize(array('user' => $user->getId(), 'session' => $sessionId), 'json', $scontext);

        $this->getSocket()->sendMulti(array('logout', $msgJson));
    }
}