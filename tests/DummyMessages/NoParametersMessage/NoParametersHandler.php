<?php
declare(strict_types=1);

namespace Tests\DummyMessages\NoParametersMessage;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class NoParametersHandler
{
    public function __invoke(): void
    {
    }
}
