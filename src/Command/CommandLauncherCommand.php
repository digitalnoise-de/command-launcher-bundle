<?php
declare(strict_types=1);

namespace Digitalnoise\CommandLauncherBundle\Command;

use Digitalnoise\CommandLauncher\LaunchCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CommandLauncherCommand extends Command
{
    public function __construct(private readonly LaunchCommand $command)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('digitalnoise:command:launch');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->command->execute($input, $output);
    }
}
