<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * RecipientsController
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class RecipientsController extends Controller
{
    public function indexAction(string $search = null) {
        // TODO @AS

        $this->render("templates/recipients/index.html.twig");
    }

    public function showAction(int $id) {
        // TODO @AS @CA Needs discussion

        $this->render("templates/recipients/show.html.twig");
    }
}