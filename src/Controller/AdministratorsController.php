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
    private static $MAX_RESULTS_CONNECTION_LOGS = 5;

    public function indexAction(Request $request)
    {
        $administrators = $this->getDoctrine()->getRepository('App\Entity\Administrator')->findAllPaginated($this->getParameter('paginator.items_per_page'), $request->get('page'));

        if ($administrators->currentPageIsInvalid()) {
            throw new NotFoundHttpException();
        }

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

            $administrator
                ->setPassword($password)
                ->addRole('ROLE_USER');

            $entityManager->persist($administrator);
            $entityManager->flush();

            $this->addFlash('success', $this->get('translator')->trans('flash.administratorCreated'));

            return $this->redirectToRoute('administrators_show', array(
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

    public function showAction(int $id, Request $request)
    {
        $administrator = $this->getDoctrine()->getRepository('App\Entity\Administrator')->find($id);

        if (null == $administrator) {
            throw new NotFoundHttpException();
        }

        $doctrine = $this->getDoctrine();
        $lastConnectionLogs = $doctrine->getRepository('App\Entity\ConnectionLogs')->findConnectionLogsByAdministrator($administrator, self::$MAX_RESULTS_CONNECTION_LOGS);
        $sentCampaigns = $doctrine->getRepository('App\Entity\Campaign')->findPaginatedByAdministrator($administrator, $this->getParameter('paginator.items_per_page'), $request->get('page'));

        if ($sentCampaigns->currentPageIsInvalid()) {
            throw new NotFoundHttpException();
        }

        return $this->render("administrators/show.html.twig", array(
            'administrator'  => $administrator,
            'connectionLogs' => $lastConnectionLogs,
            'sentCampaigns'  => $sentCampaigns,
        ));
    }

    public function showConnectionLogsAction($administratorId, Request $request)
    {
        $connectionLogsRepository = $this->getDoctrine()->getRepository('App\Entity\ConnectionLogs');
        $itemsPerPage = $this->getParameter('paginator.items_per_page');
        $page = $request->get('page');

        if (null === $administratorId) {
            $connectionLogs = $connectionLogsRepository->findPaginatedConnectionLogsByAdministrator($this->getUser(), $itemsPerPage, $page);
        } elseif (null === $user = $this->getDoctrine()->getRepository('App\Entity\Administrator')->find($administratorId)) {
            throw new NotFoundHttpException();
        } else {
            $connectionLogs = $connectionLogsRepository->findPaginatedConnectionLogsByAdministrator($user, $itemsPerPage, $page);
        }

        if ($connectionLogs->currentPageIsInvalid()) {
            throw new NotFoundHttpException();
        }

        return $this->render("administrators/show-connection-logs.html.twig", array(
            'connectionLogs' => $connectionLogs,
        ));
    }
}