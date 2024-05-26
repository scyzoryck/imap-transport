<?php

namespace Scyzoryck\ImapTransport;

use PhpImap\Mailbox;
use Symfony\Component\Messenger\Exception\InvalidArgumentException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

/**
 * @implements TransportFactoryInterface<ImapTransport>
 */
class ImapTransportFactory implements TransportFactoryInterface
{
    public function createTransport(string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        $params = parse_url($dsn);
        if ('imap' !== $params['scheme']) {
            throw new InvalidArgumentException('The given IMAP DSN is not invalid.');
        }
        $imapPath = '{'.$params['host'].':'.$params['port'].$params['path'].'}'.$params['fragment'];

        return new ImapTransport(new Mailbox(
            $imapPath,
            $params['user'],
            $params['pass'],
        ));
    }

    public function supports(string $dsn, array $options): bool
    {
        return 0 === strpos($dsn, 'imap://');
    }
}
