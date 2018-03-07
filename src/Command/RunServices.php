<?php

namespace App\Command;

use App\Util\Process\BackgroundDetachedProcess;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * RunServices
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class RunServices extends Command
{
    public static $name = "has:run-services";

    /**
     * @var string
     */
    private $consolePath;

    /**
     * @var false|string
     */
    private $phpBinaryPath;

    /**
     * @param string $projectDir
     */
    public function __construct($projectDir)
    {
        $this->consolePath = $projectDir . "/bin/console";
        $this->phpBinaryPath = (new PhpExecutableFinder())->find();

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName(self::$name)
            ->setDescription('Runs all the services necessary to HAS.')
            ->setHidden(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->phpBinaryPath) {
            throw new Exception("The PHP executable wasn't found.");
        }

        foreach ($this->getServicesToRun() as $serviceName) {
            $process = new BackgroundDetachedProcess($this->phpBinaryPath . '  ' . $this->consolePath . ' ' . $serviceName);

            $process->start();
            $process->setTimeout(null);
        }
    }

    protected function getServicesToRun()
    {
        return array(
            ListenCampaignsStatus::$name,
            RunRegistrationRpcServer::$name,
            SendPendingCampaigns::$name,
        );
    }
}