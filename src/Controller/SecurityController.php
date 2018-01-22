<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

/**
 * SecurityController
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class SecurityController extends Controller
{
    public function loginAction(Request $request)
    {
        $session = $request->getSession();
        $authenticationError = Security::AUTHENTICATION_ERROR;
        $lastUsername = Security::LAST_USERNAME;

        if ($request->attributes->has($authenticationError)) {
            $error = $request->attributes->get($authenticationError);
        } else {
            $error = $session->get($authenticationError);
            $session->remove($authenticationError);
        }

        return $this->render('security/login.html.twig', array(
            'last_username' => $session->get($lastUsername),
            'error'         => $error,
        ));
    }

    public function loginCheckAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }

    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }
}