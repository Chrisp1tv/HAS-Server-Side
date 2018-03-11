<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * HomeController
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class HomeController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $doctrine = $this->getDoctrine();

        $lastConnection = $doctrine->getRepository('App\Entity\ConnectionLogs')->findPenultimateConnectionByAdministrator($this->getUser());
        $numberOfCampaigns = $doctrine->getRepository('App\Entity\Campaign')->countAll();
        $numberOfUnsentCampaigns = $doctrine->getRepository('App\Entity\Campaign')->countUnsent();
        $numberOfLinkedRecipients = $doctrine->getRepository('App\Entity\Recipient')->countAll();

        return $this->render("home/index.html.twig", array(
            'lastConnection'           => $lastConnection,
            'numberOfCampaigns'        => $numberOfCampaigns,
            'numberOfUnsentCampaigns'  => $numberOfUnsentCampaigns,
            'numberOfLinkedRecipients' => $numberOfLinkedRecipients,
        ));
    }
}
