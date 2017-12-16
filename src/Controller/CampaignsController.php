<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * CampaignsController
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class CampaignsController extends Controller
{
    public function indexAction() {


        $this->render("templates/campaigns/index.html.twig");
    }

    public function newAction() {


        $this->render("templates/campaigns/new.html.twig");
    }

    public function modifyAction(int $id) {


        $this->render("templates/campaigns/modify.html.twig");
    }

    public function removeAction(int $id) {

    }

    public function duplicateAction(int $id) {


        $this->render("templates/campaigns/duplicate.html.twig");
    }

    public function showAction(int $id) {


        $this->render("templates/campaigns/show.html.twig");
    }
}