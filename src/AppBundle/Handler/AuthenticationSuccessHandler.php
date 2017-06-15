<?php
namespace AppBundle\Handler;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\Cookie;
use AppBundle\Entity\User;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * Custom authentication success handler
 */
class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{

    private $router;
    private $em;

    public function __construct(HttpUtils $httpUtils, array $options, RouterInterface $router, EntityManager $em){
        parent::__construct($httpUtils, $options);
        $this->router = $router;
        $this->em = $em;
    }

    function onAuthenticationSuccess(Request $request, TokenInterface $token){
        $user =  $token->getUser();
        if ($user instanceof User) {
            $args = array();
            if ($user->getType()=='company') {
                $route = '_company_main';
            } else if ($user->getType()=='agent') {
                $route = '_agent_mainpage';
            } else if ($user->getType()=='employee') {
                $route = '_schedule_get';
                $args['carServiceId'] = $user->getEmployee()->getCarService()->getId();
                $date = new \DateTime();
                $date = $date->format('Y-m-d');
                $args['date'] = $date;
            } else if ($user->getType() == User::TYPE_CAR_OWNER) {
                if ($request->getSession()->has('carOwnerRequest')) {
                    $route = '_car_owner_request_create';
                } else {
                    $route = '_car_owner_requests';
                }
            } else {
                $route = '_main';
            }
            $response = new RedirectResponse($this->router->generate($route, $args));
            //$response->headers->setCookie(new Cookie('Set-Cookie', $this->get("security.context")->getToken(), time() +(3600 * 24 * 31), '/', null, false, false));
        } else {
            $response = parent::onAuthenticationSuccess($request, $token);
        }

        return $response;
    }
}
