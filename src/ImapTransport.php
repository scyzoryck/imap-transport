<?php

namespace Scyzoryck\ImapTransport;

use PhpImap\Mailbox;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\TransportInterface;

class ImapTransport implements TransportInterface
{
    public function __construct(
        private Mailbox $mailbox,
    ) {}

    public function get(): iterable
    {
        $unreadIds = $this->mailbox->searchMailbox('UNSEEN');
        $messages = [];
        foreach ($unreadIds as $id) {
            $messages[] = (new Envelope($this->mailbox->getMail($id, false)))->with(new TransportMessageIdStamp($id));
        }

        return $messages;
    }

    public function ack(Envelope $envelope): void
    {
        $stamp = $envelope->last(TransportMessageIdStamp::class);
        if (!$stamp instanceof TransportMessageIdStamp) {
            throw new \LogicException('No TransportMessageIdStamp found on the Envelope.');
        }

        $this->mailbox->markMailAsRead($stamp->getId());
    }

    public function reject(Envelope $envelope): void
    {
        $stamp = $envelope->last(TransportMessageIdStamp::class);
        if (!$stamp instanceof TransportMessageIdStamp) {
            throw new \LogicException('No TransportMessageIdStamp found on the Envelope.');
        }

        $this->mailbox->deleteMail($stamp->getId());
    }

    public function send(Envelope $envelope): Envelope
    {
        throw new \RuntimeException('Sending messages via transport is not supported now');
    }
}
