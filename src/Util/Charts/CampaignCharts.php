<?php

namespace App\Util\Charts;

use App\Entity\Campaign;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * CampaignCharts
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class CampaignCharts
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param Campaign $campaign
     *
     * @return null|Highchart
     */
    public function getGeneralStatisticsPieChart(Campaign $campaign)
    {
        if (!$campaign->isSent()) {
            return null;
        }

        $globalConsultationStatistics = $campaign->getGlobalConsultationStatistics();

        $pieChart = new Highchart();
        $pieChart->chart->type('pie');
        $pieChart->chart->renderTo('pieChart');
        $pieChart->title->text($this->translator->trans('globalRepartitionStatistics'));
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
            'name' => $this->translator->trans('globalRepartitionStatistics'),
            'data' => array(
                array($this->translator->trans('notReceived'), $globalConsultationStatistics['notReceived']),
                array($this->translator->trans('receivedOnly'), $globalConsultationStatistics['receivedOnly']),
                array($this->translator->trans('receivedAndSeen'), $globalConsultationStatistics['receivedAndSeen']),
            ),
        )));

        return $pieChart;
    }

    /**
     * @param Campaign $campaign
     *
     * @return null|Highchart
     */
    public function getGroupedStatisticsBarChart(Campaign $campaign)
    {
        if ($campaign->getRecipientGroups()->isEmpty()) {
            return null;
        }

        $barChart = new Highchart();
        $barChart->chart->type('bar');
        $barChart->chart->renderTo('barChart');
        $barChart->title->text($this->translator->trans('globalGroupedRepartitionStatistics'));
        $barChart->plotOptions->series(array('stacking' => 'normal'));
        $barChart->xAxis->categories($this->getCategoriesNames($campaign));
        $barChart->series($this->getGroupedStatisticsBarChartSeries($campaign));

        return $barChart;
    }

    private function getCategoriesNames(Campaign $campaign)
    {
        $categoriesNames = array();

        foreach ($campaign->getRecipientGroups()->toArray() as $recipientGroup) {
            $categoriesNames[] = (string)$recipientGroup;
        }

        if (!$campaign->getRecipients()->isEmpty()) {
            $categoriesNames[] = $this->translator->trans('directRecipients');
        }

        return $categoriesNames;
    }

    private function getGroupedStatisticsBarChartSeries(Campaign $campaign)
    {
        $dirtySeries = array(
            'notReceived'     => array(),
            'receivedOnly'    => array(),
            'receivedAndSeen' => array(),
        );

        $recipientGroups = $campaign->getRecipientGroups()->toArray();
        if (!$campaign->getRecipients()->isEmpty()) {
            // We add the null key to add the direct recipients to the statistics
            $recipientGroups[] = null;
        }

        foreach ($recipientGroups as $recipientGroup) {
            $groupStatistics = $campaign->getConsultationStatisticsForGroup($recipientGroup);

            $dirtySeries['notReceived'][] = $groupStatistics['notReceived'];
            $dirtySeries['receivedOnly'][] = $groupStatistics['receivedOnly'];
            $dirtySeries['receivedAndSeen'][] = $groupStatistics['receivedAndSeen'];
        }

        return array(
            array('name' => $this->translator->trans('notReceived'), 'data' => $dirtySeries['notReceived'], 'color' => "#d8561f"),
            array('name' => $this->translator->trans('receivedOnly'), 'data' => $dirtySeries['receivedOnly'], 'color' => "#f5832b"),
            array('name' => $this->translator->trans('receivedAndSeen'), 'data' => $dirtySeries['receivedAndSeen'], 'color' => "#5fbe41"),
        );
    }
}