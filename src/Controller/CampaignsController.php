<?php

namespace App\Controller;

use App\Entity\Campaign;
use App\Form\CampaignType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * CampaignsController
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class CampaignsController extends Controller
{
    public function indexAction(Request $request)
    {
        $campaigns = $this->getDoctrine()->getRepository('App\Entity\Campaign')->findAllPaginated($this->getParameter('paginator.items_per_page'), $request->get('page'));

        if ($campaigns->currentPageIsInvalid()) {
            throw new NotFoundHttpException();
        }

        return $this->render("campaigns/index.html.twig", array(
            'campaigns' => $campaigns,
        ));
    }

    public function newAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $campaign = new Campaign();
        $form = $this->createForm(CampaignType::class, $campaign);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $entityManager->persist($campaign);
            $entityManager->flush();

            $this->addFlash('success', $this->get('translator.default')->trans('flash.campaignCreated'));
            $this->redirectToRoute('campaigns_modify', array('id' => $campaign->getId()));
        }

        return $this->render("campaigns/new.html.twig", array(
            'form' => $form->createView(),
        ));
    }

    public function modifyAction(int $id, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $campaign = $entityManager->getRepository('App\Entity\Campaign')->find($id);

        if (null == $campaign) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(CampaignType::class, $campaign);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', $this->get('translator.default')->trans('flash.campaignModified'));
        }

        return $this->render("campaigns/modify.html.twig", array(
            'campaign' => $campaign,
            'form'     => $form->createView(),
        ));
    }

    public function removeAction(int $id, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $campaign = $entityManager->getRepository('App\Entity\Campaign')->find($id);

        if (null == $campaign) {
            throw new NotFoundHttpException();
        }

        // TODO: Change condition once we'll be using a new method to differentiate unsent/sent campaigns
        if ($campaign->getSendingDate() < new \DateTime()) {
            $entityManager->remove($campaign);
            $entityManager->flush();

            $this->addFlash('success', $this->get('translator.default')->trans('flash.campaignRemoved'));
        } else {
            $this->addFlash('error', $this->get('translator.default')->trans('flash.unableToRemoveCampaign'));
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function duplicateAction(Request $request, int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $campaign = $this->getDoctrine()->getRepository('App\Entity\Campaign')->find($id);

        if (null == $campaign) {
            throw new NotFoundHttpException();
        }

        $campaign = clone $campaign;
        $form = $this->createForm(CampaignType::class, $campaign);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $entityManager->persist($campaign);
            $entityManager->flush();

            $this->addFlash('success', $this->get('translator.default')->trans('flash.campaignDuplicated'));
            $this->redirectToRoute('campaigns_modify', array('id' => $campaign->getId()));
        }

        return $this->render("campaigns/duplicate.html.twig", array(
            'campaign' => $campaign,
            'form'     => $form->createView(),
        ));
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(int $id)
    {
        $campaign = $this->getDoctrine()->getRepository('App\Entity\Campaign')->find($id);

        if (null == $campaign) {
            throw new NotFoundHttpException();
        }

        return $this->render("campaigns/show.html.twig", array(
            'campaign' => $campaign,
        ));
    }
}