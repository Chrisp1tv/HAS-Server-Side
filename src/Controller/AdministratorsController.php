<?php

namespace App\Controller;

use App\Entity\Administrator;
use App\Form\AdministratorType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * AdministratorsController
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class AdministratorsController extends Controller
{
    public function indexAction()
    {
        $administrators = $this->getDoctrine()->getRepository('App\Entity\Administrator')->findAll();

        return $this->render("administrators/index.html.twig", array(
            'administrators' => $administrators,
        ));
    }

    public function newAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $administrator = new Administrator();
        $form = $this->createForm(AdministratorType::class, $administrator);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $password = $passwordEncoder->encodePassword($administrator, $administrator->getPassword());
            $administrator->setPassword($password);
            $administrator->addRole('ROLE_USER');

            $entityManager->persist($administrator);
            $entityManager->flush();

            $this->addFlash('success', $this->get('translator.default')->trans('flash.administratorCreated'));
            $this->redirectToRoute('administrators_show', array(
                'id' => $administrator->getId(),
            ));
        }

        return $this->render("administrators/new.html.twig", array(
            'form' => $form->createView(),
        ));
    }

    public function removeAction(int $id)
    {
        // TODO @CA
    }

    public function showAction(int $id)
    {
        $administrator = $this->getDoctrine()->getRepository('App\Entity\Administrator')->find($id);

        if (null == $administrator) {
            throw new NotFoundHttpException();
        }

        $doctrine = $this->getDoctrine();
        $connectionLogs = $doctrine->getRepository('App\Entity\ConnectionLogs')->findConnectionLogsByAdministrator($administrator);
        $sentCampaigns = $doctrine->getRepository('App\Entity\Campaign')->findBySender($administrator);

        return $this->render("administrators/show.html.twig", array(
            'administrator'  => $administrator,
            'connectionLogs' => $connectionLogs,
            'sentCampaigns'  => $sentCampaigns,
        ));
    }

    public function showConnectionLogsAction()
    {
        $connectionLogs = $this->getDoctrine()->getRepository('App\Entity\ConnectionLogs')->findConnectionLogsByAdministrator($this->getUser());

        return $this->render("administrators/show-connection-logs.html.twig", array(
            'connectionLogs' => $connectionLogs,
        ));
    }
}