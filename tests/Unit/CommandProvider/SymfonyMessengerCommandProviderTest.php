<?php
declare(strict_types=1);

namespace Tests\Unit\CommandProvider;

use Digitalnoise\CommandLauncherBundle\CommandProvider\SymfonyMessengerCommandProvider;
use PHPUnit\Framework\TestCase;
use Tests\DummyMessages\BarMessage\BarMessage;
use Tests\DummyMessages\FooMessage\FooMessage;

/**
 * @covers \Digitalnoise\CommandLauncherBundle\CommandProvider\SymfonyMessengerCommandProvider
 */
final class SymfonyMessengerCommandProviderTest extends TestCase
{
    private SymfonyMessengerCommandProvider $subject;

    /**
     * @test
     */
    public function it_should_return_messages_for_symfony_message_handlers()
    {
        $result = $this->subject->all();

        self::assertEqualsCanonicalizing(
            [FooMessage::class, BarMessage::class],
            $result
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new SymfonyMessengerCommandProvider(__DIR__.'/../../');
    }
}
