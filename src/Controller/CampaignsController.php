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
        // TODO @AS

        $this->render("templates/campaigns/index.html.twig");
    }

    public function newAction() {
        // TODO @AS

        $this->render("templates/campaigns/new.html.twig");
    }

    public function modifyAction(int $id) {
        // TODO @AS

        $this->render("templates/campaigns/modify.html.twig");
    }

    public function removeAction(int $id) {
        // TODO @AS
    }

    public function duplicateAction(int $id) {
        // TODO @CA

        $this->render("templates/campaigns/duplicate.html.twig");
    }

    public function showAction(int $id) {
        // TODO @CA

        $this->render("templates/campaigns/show.html.twig");
    }
}