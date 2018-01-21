<?php

namespace App\Util;

use App\Entity\Campaign;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

/**
 * CampaignSender
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class CampaignSender
{
    /**
     * @var ProducerInterface
     */
    private $RQProducer;

    public function __construct(ProducerInterface $RQProducer)
    {
        $this->RQProducer = $RQProducer;
    }

    public function sendCampaign(Campaign $campaign)
    {

    }
}