<?php

namespace App\Controller;

use App\Form\RecipientType;
use App\Form\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * RecipientsController
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class RecipientsController extends Controller
{
    public function indexAction(Request $request, $search = null)
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            return $this->redirectToRoute('recipients_index', array(
                'search' => $form->getData()['search'],
            ));
        }

        $recipients = $this->getDoctrine()->getManager()->getRepository('App\Entity\Recipient')->findPaginated($this->getParameter('paginator.items_per_page'), $request->get('page'), $search);

        if ($recipients->currentPageIsInvalid()) {
            throw new NotFoundHttpException();
        }

        return $this->render("recipients/index.html.twig", array(
            'recipients' => $recipients,
            'search'     => $search,
            'form'       => $form->createView(),
        ));
    }

    public function modifyAction(Request $request, int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $recipient = $entityManager->getRepository('App\Entity\Recipient')->find($id);

        if (null == $recipient) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(RecipientType::class, $recipient);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', $this->get('translator.default')->trans('flash.recipientModified'));
        }

        return $this->render('recipients/modify.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function showAction(int $id)
    {
        // TODO @AS @CA Needs discussion

        return $this->render("recipients/show.html.twig");
    }
}