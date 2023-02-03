<?php
declare(strict_types=1);

namespace Digitalnoise\CommandLauncherBundle\CommandLauncher;

use Digitalnoise\CommandLauncher\CommandLauncher;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class SymfonyCommandLauncher implements CommandLauncher
{
    public function __construct(private readonly MessageBusInterface $messageBus)
    {
    }

    public function launch(object $command): void
    {
        $this->messageBus->dispatch(new Envelope($command));
    }
}
