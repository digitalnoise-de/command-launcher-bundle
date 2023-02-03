<?php
declare(strict_types=1);

namespace Tests\DummyMessages\InvalidMessage;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class InvalidHandler
{
    public function __invoke(string $unexpected): void
    {
    }
}
