<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * RecipientsController
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class RecipientsController extends Controller
{
    public function indexAction(Request $request, $search = null)
    {
        $recipients = $this->getDoctrine()->getManager()->getRepository('App\Entity\Recipient')->findPaginated($this->getParameter('paginator.items_per_page'), $request->get('page'), $search);

        if ($recipients->currentPageIsInvalid()) {
            throw new NotFoundHttpException();
        }

        return $this->render("recipients/index.html.twig", array(
            'recipients' => $recipients,
            'search'     => $search,
        ));
    }

    public function showAction(int $id)
    {
        // TODO @AS @CA Needs discussion

        return $this->render("recipients/show.html.twig");
    }
}