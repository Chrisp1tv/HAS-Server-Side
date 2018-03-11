<?php

namespace App\Controller;

use App\Entity\RecipientGroup;
use App\Form\RecipientGroupType;
use App\Util\RabbitMQ\RecipientGroupsManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RecipientGroupsController
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class RecipientGroupsController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $recipientGroups = $this->getDoctrine()->getManager()->getRepository('App\Entity\RecipientGroup')->findAllPaginated($this->getParameter('paginator.items_per_page'), $request->get('page'));

        if ($recipientGroups->currentPageIsInvalid()) {
            throw $this->createNotFoundException();
        }

        return $this->render("recipient-groups/index.html.twig", array(
            'recipientGroups' => $recipientGroups,
        ));
    }

    /**
     * @param Request                $request
     * @param RecipientGroupsManager $recipientGroupsManager
     *
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request, RecipientGroupsManager $recipientGroupsManager)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $recipientGroup = new RecipientGroup();
        $form = $this->createForm(RecipientGroupType::class, $recipientGroup);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $entityManager->persist($recipientGroup);
            $entityManager->flush();

            $recipientGroupsManager->updateRecipientGroupBindings($recipientGroup, array(), $recipientGroup->getRecipients()->toArray());

            $this->addFlash('success', $this->get('translator')->trans('flash.recipientGroupCreated'));

            return $this->redirectToRoute('recipient_groups_modify', array(
                'id' => $recipientGroup->getId(),
            ));
        }

        return $this->render("recipient-groups/new.html.twig", array(
            'recipientGroup' => $recipientGroup,
            'form'           => $form->createView(),
        ));
    }

    /**
     * @param Request                $request
     * @param RecipientGroupsManager $recipientGroupsManager
     * @param int                    $id The id of the recipients group
     *
     * @return Response
     */
    public function modifyAction(Request $request, RecipientGroupsManager $recipientGroupsManager, int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $recipientGroup = $this->getDoctrine()->getRepository('App\Entity\RecipientGroup')->find($id);

        if (null == $recipientGroup) {
            throw $this->createNotFoundException();
        }

        $oldRecipients = $recipientGroup->getRecipients()->toArray();
        $form = $this->createForm(RecipientGroupType::class, $recipientGroup);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $entityManager->flush();
            $recipientGroupsManager->updateRecipientGroupBindings($recipientGroup, $oldRecipients, $recipientGroup->getRecipients()->toArray());

            $this->addFlash('success', $this->get('translator')->trans('flash.recipientGroupModified'));
        }

        return $this->render("recipient-groups/modify.html.twig", array(
            'recipientGroup' => $recipientGroup,
            'form'           => $form->createView(),
        ));
    }

    /**
     * @param int $id The id of the recipients group we want to remove
     */
    public function removeAction(int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $recipientGroup = $this->getDoctrine()->getRepository('App\Entity\RecipientGroup')->find($id);

        if (null == $recipientGroup) {
            throw $this->createNotFoundException();
        }

        $entityManager->remove($recipientGroup);
        $entityManager->flush();

        $this->addFlash('success', $this->get('translator')->trans('flash.recipientGroupRemoved'));
    }

    /**
     * @param Request $request
     * @param int     $id The id of the recipients group we want to show
     *
     * @return Response
     */
    public function showAction(Request $request, int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $recipientGroup = $entityManager->getRepository('App\Entity\RecipientGroup')->find($id);

        if (null == $recipientGroup) {
            throw $this->createNotFoundException();
        }

        $recipients = $entityManager->getRepository('App\Entity\Recipient')->findPaginatedByRecipientGroup($recipientGroup, $this->getParameter('paginator.items_per_page'), $request->get('page'));

        if ($recipients->currentPageIsInvalid()) {
            throw $this->createNotFoundException();
        }

        return $this->render("recipient-groups/show.html.twig", array(
            'recipientGroup' => $recipientGroup,
            'recipients'     => $recipients,
        ));
    }
}
