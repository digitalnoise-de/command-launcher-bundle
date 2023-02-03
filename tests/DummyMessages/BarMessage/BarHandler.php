<?php
declare(strict_types=1);

namespace Tests\DummyMessages\BarMessage;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class BarHandler
{
    public function __invoke(BarMessage $message): void
    {
    }
}
