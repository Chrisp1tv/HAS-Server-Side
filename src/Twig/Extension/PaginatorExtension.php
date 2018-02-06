<?php

namespace App\Twig\Extension;

use App\Util\Paginator;

/**
 * PaginatorExtension
 */
class PaginatorExtension extends \Twig_Extension
{
    const DEFAULT_NUMBER_PAGES_TO_SHOW = 5;

    /**
     * @var int
     */
    protected $numberPagesToShow;

    /**
     * @var Paginator
     */
    protected $paginator;

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('pagination', array($this, 'renderPagination'), array('needs_environment' => true, 'is_safe' => array("html"))),
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Paginator_extension';
    }

    /**
     * @param \Twig_Environment $twig
     * @param Paginator         $paginator
     * @param int|null          $numberPagesToShow
     *
     * @return string|null
     */
    public function renderPagination(\Twig_Environment $twig, Paginator $paginator, $numberPagesToShow = null)
    {
        $this->paginator = $paginator;
        $this->numberPagesToShow = $numberPagesToShow ?: self::DEFAULT_NUMBER_PAGES_TO_SHOW;

        if (!$this->paginator->isEmpty() and $this->paginator->getNumberOfPages() > 1) {
            return $twig->render('common/paginator/pagination.html.twig', array(
                'numberPages' => $this->paginator->getNumberOfPages(),
                'currentPage' => $this->paginator->getPage(),
                'startPage'   => $this->getStartPage(),
                'endPage'     => $this->getEndPage(),
            ));
        }

        return null;
    }

    /**
     * Sets the page where start the displaying of the buttons
     *
     * @return int
     */
    private function getStartPage()
    {
        $radius = $this->getRadius($this->numberPagesToShow);
        $pageToStart = $this->paginator->getPage() - $radius;

        if (($this->paginator->getNumberOfPages() - $this->paginator->getPage()) < $radius) {
            $pageToStart = $this->paginator->getNumberOfPages() - $this->numberPagesToShow + 1;
        }

        if ($pageToStart < 1) {
            $pageToStart = 1;
        }

        return $pageToStart;
    }

    /**
     * Calculates the "radius" : the number of pages we want to display (if possible) before / after the actual page
     *
     * @param $numberPagesToShow
     *
     * @return int
     */
    private function getRadius($numberPagesToShow)
    {
        return floor($numberPagesToShow / 2);
    }

    /**
     * Sets the page where to end the displaying of the buttons
     *
     * @return int
     */
    private function getEndPage()
    {
        $radius = $this->getRadius($this->numberPagesToShow);

        $pageToEnd = $this->paginator->getPage() + $radius;

        if ($pageToEnd > $this->paginator->getNumberOfPages()) {
            $pageToEnd = $this->paginator->getNumberOfPages();
        }

        return $pageToEnd;
    }
}
