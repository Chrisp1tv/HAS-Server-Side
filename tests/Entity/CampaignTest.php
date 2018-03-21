<?php

namespace App\Tests\Entity;

use App\Entity\Campaign;
use PHPUnit\Framework\TestCase;

class CampaignTest extends TestCase
{
    /**
     * @var Campaign
     */
    protected $campaign;

    public function testCampaignState()
    {
        $this->assertFalse($this->campaign->shouldBeSent());
        $this->assertFalse($this->campaign->isSent());
        $this->assertTrue($this->campaign->isModifiable());

        $this->campaign->setSendingDate(new \DateTime("now - 5 minutes"));

        $this->assertTrue($this->campaign->shouldBeSent());
        $this->assertFalse($this->campaign->isModifiable());

        $this->campaign->makeSent();

        $this->assertNotNull($this->campaign->getEffectiveSendingDate());
        $this->assertFalse($this->campaign->shouldBeSent());
        $this->assertTrue($this->campaign->isSent());
    }

    protected function setUp()
    {
        $this->campaign = new Campaign();
    }
}