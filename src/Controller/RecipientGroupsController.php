<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * RecipientGroupsController
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class RecipientGroupsController extends Controller
{
    public function indexAction() {
        // TODO @AS

        $this->render("templates/recipient-groups/index.html.twig");
    }

    public function newAction() {
        // TODO @CA

        $this->render("templates/recipient-groups/new.html.twig");
    }

    public function modifyAction(int $id) {
        // TODO @CA

        $this->render("templates/recipient-groups/modify.html.twig");
    }

    public function removeAction(int $id) {
        // TODO @AS
    }

    public function showAction(int $id) {
        // TODO @AS @CA Needs discussion

        $this->render("templates/recipient-groups/show.html.twig");
    }
}