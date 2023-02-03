<?php
declare(strict_types=1);

namespace Digitalnoise\CommandLauncherBundle\Command;

use Digitalnoise\CommandLauncher\CommandLauncher;
use Digitalnoise\CommandLauncher\CommandProvider;
use Digitalnoise\CommandLauncher\LaunchCommand;
use Digitalnoise\CommandLauncher\ParameterResolver;
use ReflectionException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CommandLauncherCommand extends Command
{
    private LaunchCommand $command;

    public function __construct(
        private readonly CommandProvider $commandProvider,
        private readonly CommandLauncher $commandLauncher,
        iterable $parameterResolvers = []
    ) {
        parent::__construct();

        $resolvers = [];
        foreach ($parameterResolvers as $parameterResolver) {
            $resolvers[] = $parameterResolver;
        }
        $this->command = new LaunchCommand($this->commandProvider, $this->commandLauncher, $resolvers);
    }

    protected function configure(): void
    {
        $this->setName('digitalnoise:command:launch');
    }

    /**
     * @throws ReflectionException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->command->execute($input, $output);
    }
}
