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


        $this->render("templates/recipient-groups/index.html.twig");
    }

    public function newAction() {


        $this->render("templates/recipient-groups/new.html.twig");
    }

    public function modifyAction(int $id) {


        $this->render("templates/recipient-groups/modify.html.twig");
    }

    public function removeAction(int $id) {

    }

    public function showAction(int $id) {


        $this->render("templates/recipient-groups/show.html.twig");
    }
}