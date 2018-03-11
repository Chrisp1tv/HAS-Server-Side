<?php

namespace App\Controller;

use App\Entity\Administrator;
use App\Form\AdministratorType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * AdministratorsController
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class AdministratorsController extends Controller
{
    /**
     * @var int The maximal number of connection logs per page in showAction
     */
    const MAX_RESULTS_CONNECTION_LOGS = 5;

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $administrators = $this->getDoctrine()->getRepository('App\Entity\Administrator')->findAllPaginated($this->getParameter('paginator.items_per_page'), $request->get('page'));

        if ($administrators->currentPageIsInvalid()) {
            throw $this->createNotFoundException();
        }

        return $this->render("administrators/index.html.twig", array(
            'administrators' => $administrators,
        ));
    }

    /**
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return RedirectResponse|Response
     */
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

    /**
     * @param int     $id The id of the administrator who needs to change his status.
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function toggleStatusAction(int $id, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $administrator = $entityManager->getRepository('App\Entity\Administrator')->find($id);

        if (null == $administrator) {
            throw $this->createNotFoundException();
        }

        $administrator->setDisabled(!$administrator->isDisabled());
        $entityManager->flush();

        $this->addFlash('success', $this->get('translator')->trans($administrator->isDisabled() ? 'flash.administratorDisabled' : 'flash.administratorEnabled'));

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param int     $id The id of the administrator
     * @param Request $request
     *
     * @return Response
     */
    public function showAction(int $id, Request $request)
    {
        $administrator = $this->getDoctrine()->getRepository('App\Entity\Administrator')->find($id);

        if (null == $administrator) {
            throw $this->createNotFoundException();
        }

        $doctrine = $this->getDoctrine();
        $lastConnectionLogs = $doctrine->getRepository('App\Entity\ConnectionLogs')->findConnectionLogsByAdministrator($administrator, self::MAX_RESULTS_CONNECTION_LOGS);
        $sentCampaigns = $doctrine->getRepository('App\Entity\Campaign')->findPaginatedByAdministrator($administrator, $this->getParameter('paginator.items_per_page'), $request->get('page'));

        if ($sentCampaigns->currentPageIsInvalid()) {
            $this->createNotFoundException();
        }

        return $this->render("administrators/show.html.twig", array(
            'administrator'  => $administrator,
            'connectionLogs' => $lastConnectionLogs,
            'sentCampaigns'  => $sentCampaigns,
        ));
    }

    /**
     * @param int|null $administratorId The id of the administrator. If not set, the id of the current logged in administrator is used.
     * @param Request  $request
     *
     * @return Response
     */
    public function showConnectionLogsAction(?int $administratorId, Request $request)
    {
        $connectionLogsRepository = $this->getDoctrine()->getRepository('App\Entity\ConnectionLogs');
        $itemsPerPage = $this->getParameter('paginator.items_per_page');
        $page = $request->get('page');
        $user = null === $administratorId ? $this->getUser() : $this->getDoctrine()->getRepository('App\Entity\Administrator')->find($administratorId);

        if (null === $user) {
            throw $this->createNotFoundException();
        }

        $connectionLogs = $connectionLogsRepository->findPaginatedConnectionLogsByAdministrator($user, $itemsPerPage, $page);

        if ($connectionLogs->currentPageIsInvalid()) {
            throw $this->createNotFoundException();
        }

        return $this->render("administrators/show-connection-logs.html.twig", array(
            'connectionLogs' => $connectionLogs,
        ));
    }
}
