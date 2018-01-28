<?php

namespace App\Controller;

use App\Entity\RecipientGroup;
use App\Form\RecipientGroupType;
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

    public function newAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $recipientGroup = new RecipientGroup();
        $form = $this->createForm(RecipientGroupType::class, $recipientGroup);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $entityManager->persist($recipientGroup);
            $entityManager->flush();

            $this->addFlash('success', $this->get('translator.default')->trans('flash.recipientGroupCreated'));
            $this->redirectToRoute('recipient_groups_modify', array(
                'id' => $recipientGroup->getId(),
            ));
        }

        return $this->render("recipient-groups/new.html.twig", array(
            'recipientGroup' => $recipientGroup,
            'form'           => $form->createView(),
        ));
    }

    public function modifyAction(Request $request, int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $recipientGroup = $this->getDoctrine()->getRepository('App\Entity\RecipientGroup')->find($id);

        if (null == $recipientGroup) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(RecipientGroupType::class, $recipientGroup);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', $this->get('translator.default')->trans('flash.recipientGroupModified'));
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

        $this->addFlash('success', $this->get('translator.default')->trans('flash.recipientGroupRemoved'));
    }

    public function showAction(int $id)
    {
        // TODO @AS @CA Needs discussion

        return $this->render("recipient-groups/show.html.twig");
    }
}