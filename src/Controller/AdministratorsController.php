<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * AdministratorsController
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class AdministratorsController extends Controller
{
    public function indexAction() {


        $this->render("templates/administrators/index.html.twig");
    }

    public function newAction() {


        $this->render("templates/administrators/new.html.twig");
    }

    public function removeAction(int $id) {

    }

    public function showAction(int $id) {


        $this->render("templates/administrators/show.html.twig");
    }

    public function showConnectionLogsAction() {


        $this->render("templates/administrators/show-connection-logs.html.twig");
    }
}