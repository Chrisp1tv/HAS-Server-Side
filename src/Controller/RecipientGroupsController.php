<?php

namespace App\Controller;

use App\Entity\RecipientGroup;
use App\Form\RecipientGroupType;
use App\Util\RabbitMQManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * RecipientGroupsController
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class RecipientGroupsController extends Controller
{
    public function indexAction(Request $request)
    {
        $recipientGroups = $this->getDoctrine()->getManager()->getRepository('App\Entity\RecipientGroup')->findAllPaginated($this->getParameter('paginator.items_per_page'), $request->get('page'));

        if ($recipientGroups->currentPageIsInvalid()) {
            throw new NotFoundHttpException();
        }

        return $this->render("recipient-groups/index.html.twig", array(
            'recipientGroups' => $recipientGroups,
        ));
    }

    public function newAction(Request $request, RabbitMQManager $RabbitMQManager)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $recipientGroup = new RecipientGroup();
        $form = $this->createForm(RecipientGroupType::class, $recipientGroup);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $entityManager->persist($recipientGroup);
            $RabbitMQManager->updateRecipientGroupBindings($recipientGroup, array(), $recipientGroup->getRecipients()->toArray());
            $entityManager->flush();

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

    public function modifyAction(Request $request, RabbitMQManager $RabbitMQManager, int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $recipientGroup = $this->getDoctrine()->getRepository('App\Entity\RecipientGroup')->find($id);

        if (null == $recipientGroup) {
            throw new NotFoundHttpException();
        }

        $oldRecipients = $recipientGroup->getRecipients()->toArray();
        $form = $this->createForm(RecipientGroupType::class, $recipientGroup);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $entityManager->flush();
            $RabbitMQManager->updateRecipientGroupBindings($recipientGroup, $oldRecipients, $recipientGroup->getRecipients()->toArray());

            $this->addFlash('success', $this->get('translator')->trans('flash.recipientGroupModified'));
        }

        return $this->render("recipient-groups/modify.html.twig", array(
            'recipientGroup' => $recipientGroup,
            'form'           => $form->createView(),
        ));
    }

    public function removeAction(int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $recipientGroup = $this->getDoctrine()->getRepository('App\Entity\RecipientGroup')->find($id);

        if (null == $recipientGroup) {
            throw new NotFoundHttpException();
        }

        $entityManager->remove($recipientGroup);
        $entityManager->flush();

        $this->addFlash('success', $this->get('translator')->trans('flash.recipientGroupRemoved'));
    }

    public function showAction(Request $request, int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $recipientGroup = $entityManager->getRepository('App\Entity\RecipientGroup')->find($id);

        if (null == $recipientGroup) {
            throw new NotFoundHttpException();
        }

        $recipients = $entityManager->getRepository('App\Entity\Recipient')->findPaginatedByRecipientGroup($recipientGroup, $this->getParameter('paginator.items_per_page'), $request->get('page'));

        if ($recipients->currentPageIsInvalid()) {
            throw new NotFoundHttpException();
        }

        return $this->render("recipient-groups/show.html.twig", array(
            'recipientGroup' => $recipientGroup,
            'recipients'     => $recipients,
        ));
    }
}