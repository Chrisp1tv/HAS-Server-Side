<?php

namespace App\Command;

use App\Util\Process\BackgroundDetachedProcess;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * RunServices - This command runs all the others commands that must be started to make HAS work correctly.
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class RunServices extends Command
{
    const name = "has:run-services";

    /**
     * @var string
     */
    private $consolePath;

    /**
     * @var false|string
     */
    private $phpBinaryPath;

    /**
     * @param string $projectDir The project dir
     */
    public function __construct($projectDir)
    {
        $this->consolePath = $projectDir . "/bin/console";
        $this->phpBinaryPath = (new PhpExecutableFinder())->find();

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName(self::name)
            ->setDescription('Runs all the services necessary to HAS.')
            ->setHidden(true);
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * @return array The services to run
     */
    protected function getServicesToRun()
    {
        return array(
            ListenCampaignsStatus::name,
            RunRegistrationRpcServer::name,
            SendPendingCampaigns::name,
        );
    }
}
