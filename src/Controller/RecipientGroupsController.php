<?php

namespace App\Controller;

use App\Entity\RecipientGroup;
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
    public function indexAction() {
        // TODO @AS

        return $this->render("recipient-groups/index.html.twig");
    }

    public function newAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $recipientGroup = new RecipientGroup();
        $form = $this->createForm(RecipientGroup::class, $recipientGroup);

        $form->handleRequest($request);
        if ($form->isValid()) {
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

        $form = $this->createForm(RecipientGroup::class, $recipientGroup);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', $this->get('translator.default')->trans('flash.recipientGroupModified'));
        }

        return $this->render("recipient-groups/modify.html.twig", array(
            'recipientGroup' => $recipientGroup,
            'form'           => $form->createView(),
        ));
    }

    public function removeAction(int $id) {
        // TODO @AS
    }

    public function showAction(int $id) {
        // TODO @AS @CA Needs discussion

        return $this->render("recipient-groups/show.html.twig");
    }
}