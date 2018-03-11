<?php

namespace App\Controller;

use App\Form\RecipientType;
use App\Form\SearchType;
use App\Util\Charts\RecipientCharts;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RecipientsController
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class RecipientsController extends Controller
{
    /**
     * @param Request     $request
     * @param null|string $search The search done by the user, or null if no search has been done
     *
     * @return RedirectResponse|Response
     */
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
            throw $this->createNotFoundException();
        }

        return $this->render("recipients/index.html.twig", array(
            'recipients' => $recipients,
            'search'     => $search,
            'form'       => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @param int     $id The id of the recipient to modify
     *
     * @return Response
     */
    public function modifyAction(Request $request, int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $recipient = $entityManager->getRepository('App\Entity\Recipient')->find($id);

        if (null == $recipient) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(RecipientType::class, $recipient);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', $this->get('translator')->trans('flash.recipientModified'));
        }

        return $this->render('recipients/modify.html.twig', array(
            'recipient' => $recipient,
            'form'      => $form->createView(),
        ));
    }

    /**
     * @param int             $id The id of the recipient to show
     * @param RecipientCharts $recipientCharts
     *
     * @return Response
     */
    public function showAction(int $id, RecipientCharts $recipientCharts)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $recipient = $entityManager->getRepository('App\Entity\Recipient')->find($id);

        if (null === $recipient) {
            throw $this->createNotFoundException();
        }

        $campaignRepository = $entityManager->getRepository('App\Entity\Campaign');
        $totalCampaigns = $campaignRepository->countByRecipient($recipient);
        $receivedCampaigns = 0 < $totalCampaigns ? $campaignRepository->countReceivedByRecipient($recipient) : 0;
        $readCampaigns = 0 < $receivedCampaigns ? $campaignRepository->countReadByRecipient($recipient) : 0;
        $campaigns = 0 < $totalCampaigns ? $campaignRepository->findByRecipient($recipient) : null;

        $pieChart = $recipientCharts->getCampaignsStatisticsPieChart($recipient, $totalCampaigns, $receivedCampaigns, $readCampaigns);

        return $this->render("recipients/show.html.twig", array(
            'recipient'      => $recipient,
            'totalCampaigns' => $totalCampaigns,
            'campaigns'      => $campaigns,
            'pieChart'       => $pieChart,
        ));
    }
}
