<?php

namespace Tests\Scyzoryck\ImapTransport;

use PhpImap\Mailbox;
use PHPUnit\Framework\TestCase;
use Scyzoryck\ImapTransport\ImapTransport;
use Scyzoryck\ImapTransport\ImapTransportFactory;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

/**
 * @internal
 *
 * @coversNothing
 */
class ImapTransportFactoryTest extends TestCase
{
    public function testSupportedSchemas(): void
    {
        $factory = new ImapTransportFactory();
        self::assertTrue($factory->supports('imap://user:pass@imap.gmail.com:993/imap/ssl#INBOX', []));
        self::assertFalse($factory->supports('amqp://localhost/%2f/messages?auto_setup=false', []));
    }

    public function testTransportCreation(): void
    {
        $factory = new ImapTransportFactory();
        $transport = $factory->createTransport(
            'imap://user:pass@imap.gmail.com:993/imap/ssl#INBOX',
            [],
            $this->createMock(SerializerInterface::class)
        );
        $this->assertEquals(new ImapTransport(new Mailbox('{imap.gmail.com:993/imap/ssl}INBOX', 'user', 'pass')), $transport);
    }
}
