<?php

namespace App\Controller;

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
    public function indexAction()
    {
        // TODO @AS

        $this->render("campaigns/index.html.twig");
    }

    public function newAction()
    {
        // TODO @AS

        $this->render("campaigns/new.html.twig");
    }

    public function modifyAction(int $id)
    {
        // TODO @AS

        $this->render("campaigns/modify.html.twig");
    }

    public function removeAction(int $id)
    {
        // TODO @AS
    }

    /**
     * Clones a campaign and allows user to modify this clone to save it afterwards
     *
     * @param Request $request The request
     * @param int     $id      The id of the campaign
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

        if ($form->isValid()) {
            $entityManager->persist($campaign);
            $entityManager->flush();

            $this->addFlash('success', $this->get('translator.default')->trans('flash.campaignDuplicated'));
            $this->redirectToRoute('campaigns_modify', array('id' => $campaign->getId()));
        }

        $this->render("campaigns/duplicate.html.twig", array(
            'campaign' => $campaign,
            'form'     => $form->createView(),
        ));
    }

    /**
     * Shows a campaign and its statistics
     *
     * @param int $id The id of the campaign
     */
    public function showAction(int $id)
    {
        $campaign = $this->getDoctrine()->getRepository('App\Entity\Campaign')->find($id);

        if (null == $campaign) {
            throw new NotFoundHttpException();
        }

        $this->render("campaigns/show.html.twig", array(
            'campaign' => $campaign,
        ));
    }
}