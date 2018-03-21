<?php

namespace App\Tests\Entity;

use App\Entity\Administrator;
use PHPUnit\Framework\TestCase;

class AdministratorTest extends TestCase
{
    /**
     * @var Administrator
     */
    protected $administrator;

    /**
     * @var Administrator
     */
    protected $anotherAdministrator;

    public function testAutoEnabling()
    {
        $this->assertFalse($this->administrator->isDisabled());
    }

    public function testEquatableContract()
    {
        $this->assertTrue($this->administrator->isEqualTo($this->administrator));
        $this->assertFalse($this->administrator->isEqualTo($this->anotherAdministrator));
    }

    protected function setUp()
    {
        $this->administrator = new Administrator();
        $this->administrator->setUsername("Christopher Anciaux");

        $this->anotherAdministrator = new Administrator();
        $this->anotherAdministrator->setUsername("Huko");
    }
}