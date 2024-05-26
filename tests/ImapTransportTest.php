<?php

namespace Tests\Scyzoryck\ImapTransport;

use PhpImap\IncomingMail;
use PhpImap\Mailbox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Scyzoryck\ImapTransport\ImapTransport;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;

/**
 * @internal
 *
 * @coversNothing
 */
class ImapTransportTest extends TestCase
{
    private ImapTransport $transport;

    private Mailbox&MockObject $mailbox;

    protected function setUp(): void
    {
        $this->transport = new ImapTransport(
            $this->mailbox = $this->createMock(Mailbox::class)
        );
    }

    public function testGettingMessages(): void
    {
        $this->mailbox->method('searchMailbox')->with('UNSEEN')->willReturn(['12345']);
        $this->mailbox->method('getMail')->with('12345', false)->willReturn(new IncomingMail());
        $messages = $this->transport->get();
        $this->assertEquals([
            new Envelope(new IncomingMail(), [new TransportMessageIdStamp('12345')]),
        ], $messages);
    }
}
