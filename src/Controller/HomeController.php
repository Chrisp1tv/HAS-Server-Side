<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * HomeController
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class HomeController extends Controller
{
    public function indexAction() {
        // TODO @CA

        $this->render("templates/home/index.html.twig");
    }
}