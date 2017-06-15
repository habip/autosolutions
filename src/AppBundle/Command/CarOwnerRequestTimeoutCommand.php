<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use AppBundle\Entity\User;
use AppBundle\Entity\CarOwnerRequest;
use AppBundle\Entity\CarServiceSchedule;

class CarOwnerRequestTimeoutCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:car-owner:request-timeout')
            ->setDescription('Time out requests')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getContainer()->get('doctrine')->getManager();

        $timeout = (new \DateTime())->sub(new \DateInterval('PT10M'));
        $yesterday = (new \DateTime())->sub(new \DateInterval('P1D'))->setTime(0, 0, 0);

        $requests = $em->createQuery('
                select r, c, u
                from AppBundle:CarOwnerRequest r
                    join r.carOwner c
                    join c.user u
                where (r.carOwnerDate = :day and r.carOwnerTimePeriod is not null
                    or r.carOwnerDate <= :date and r.carOwnerTimePeriod is null) and r.status = :status')
            ->setParameter('day', $yesterday->format('Y-m-d H:i:s'))
            ->setParameter('date', $timeout->format('Y-m-d H:i:s'))
            ->setParameter('status', CarOwnerRequest::STATUS_NEW)
            ->getResult();

        $output->writeln('timedout request count ' . sizeof($requests));

        foreach ($requests as $request) {
            /* @var $request \AppBundle\Entity\CarOwnerRequest */
            $output->writeln(
                    sprintf(
                            '<info>Request #%s (created at %s to %s) from %s %s - %s timed out</info>',
                            $request->getId(),
                            $request->getAddedTimestamp()->format('Y-m-d H:i:s'),
                            $request->getCarOwnerDate()->format('Y-m-d H:i:s'),
                            $request->getCarOwner()->getFirstName(),
                            $request->getCarOwner()->getLastName(),
                            $request->getCarOwner()->getUser()->getEmail()
                            )
                    );

            $request->setStatus(CarOwnerRequest::STATUS_TIMEOUT);
            $em->persist($request);
            $em->flush();

            $this->sendMessage($request->getCarOwner()->getUser(), $request);
        }

        $requests = $em->createQuery('
                select r, c, u
                from AppBundle:CarOwnerRequest r
                    join r.carOwner c
                    left join c.user u
                where (r.carOwnerDate = :day and r.carOwnerTimePeriod is not null
                    or r.exitTime <= :date) and r.status = :status')
            ->setParameter('day', $yesterday->format('Y-m-d H:i:s'))
            ->setParameter('date', $timeout->format('Y-m-d H:i:s'))
            ->setParameter('status', CarOwnerRequest::STATUS_ASSIGN)
            ->getResult();

        foreach ($requests as $request) {
            /* @var $request \AppBundle\Entity\CarOwnerRequest */
            $output->writeln(
                    sprintf(
                            '<info>Request #%s (created at %s to %s) from %s %s - %s moving to done</info>',
                            $request->getId(),
                            $request->getAddedTimestamp()->format('Y-m-d H:i:s'),
                            $request->getCarOwnerDate()->format('Y-m-d H:i:s'),
                            $request->getCarOwner()->getFirstName(),
                            $request->getCarOwner()->getLastName(),
                            $request->getCarOwner()->getUser() ? $request->getCarOwner()->getUser()->getEmail() : ''
                            )
                    );

            $request->setStatus(CarOwnerRequest::STATUS_DONE);
            $em->persist($request);
            $em->flush();
        }

        $schedules = $em->createQuery('
                select s, r, c, u
                from AppBundle:CarServiceSchedule s
                    join s.carOwnerRequest r
                    join r.carOwner c
                    left join c.user u
                where s.type = :type and r.status = :status
                    and s.endTime <= :date
                ')
                ->setParameter('type', CarServiceSchedule::TYPE_REQUEST_SCHEDULED)
                ->setParameter('date', $timeout->format('Y-m-d H:i:s'))
                ->setParameter('status', CarOwnerRequest::STATUS_ASSIGN)
                ->getResult();

        foreach ($schedules as $schedule) {
            /* @var $request \AppBundle\Entity\CarOwnerRequest */
            /* @var $schedule \AppBundle\Entity\CarServiceSchedule */
            $request = $schedule->getCarOwnerRequest();
            $output->writeln(
                    sprintf(
                            '<info>Request #%s (created at %s to %s) from %s %s - %s moving to done</info>',
                            $request->getId(),
                            $request->getAddedTimestamp()->format('Y-m-d H:i:s'),
                            $request->getCarOwnerDate()->format('Y-m-d H:i:s'),
                            $request->getCarOwner()->getFirstName(),
                            $request->getCarOwner()->getLastName(),
                            $request->getCarOwner()->getUser() ? $request->getCarOwner()->getUser()->getEmail() : ''
                            )
                    );

            $request->setStatus(CarOwnerRequest::STATUS_DONE);
            $em->persist($request);
            $em->flush();
        }

        $output->writeln('<info>Finished</info>');
    }

    private function sendMessage(User $user, CarOwnerRequest $request)
    {
        $context = array('user' => $user, 'request' => $request, 'carOwner' => $request->getCarOwner());
        $twig = $this->getContainer()->get('twig');
        $context = $twig->mergeGlobals($context);
        $template = $twig->loadTemplate(':CarOwner:requestTimeoutEmail.html.twig');
        $subject = $template->renderBlock('subject', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom('admin@localhost')
            ->setTo($user->getEmail());

        $message->setBody($htmlBody, 'text/html');

        $this->getContainer()->get('mailer')->send($message);
    }
}