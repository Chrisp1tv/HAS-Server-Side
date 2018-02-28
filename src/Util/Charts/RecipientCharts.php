<?php

namespace App\Util\Charts;

use App\Entity\Recipient;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * RecipientCharts
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class RecipientCharts
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getCampaignsStatisticsPieChart(Recipient $recipient, int $totalCampaigns, int $receivedCampaigns, int $readCampaigns)
    {
        if (0 === $totalCampaigns) {
            return null;
        }

        $pieChartTitle = $this->translator->trans('globalRepartitionStatisticsOfRecipient', array('%recipientName%' => $recipient->getName()));

        $pieChart = new Highchart();
        $pieChart->chart->type('pie');
        $pieChart->chart->renderTo('pieChart');
        $pieChart->title->text($pieChartTitle);
        $pieChart->plotOptions->pie(array(
            'colors'           => array(
                "#d8561f",
                "#f5832b",
                "#5fbe41",
            ),
            'allowPointSelect' => true,
            'cursor'           => 'pointer',
            'dataLabels'       => array('enabled' => false),
            'showInLegend'     => true,
        ));
        $pieChart->series(array(array(
            'type' => 'pie',
            'name' => $pieChartTitle,
            'data' => array(
                array($this->translator->trans('notReceived'), $totalCampaigns - $receivedCampaigns),
                array($this->translator->trans('receivedOnly'), $receivedCampaigns - $readCampaigns),
                array($this->translator->trans('receivedAndSeen'), $readCampaigns),
            ),
        )));

        return $pieChart;
    }
}