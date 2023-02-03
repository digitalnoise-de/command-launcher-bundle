<?php
declare(strict_types=1);

namespace Tests\DummyMessages\FooMessage;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class FooHandler
{
    public function __invoke(FooMessage $message): void
    {
    }
}
