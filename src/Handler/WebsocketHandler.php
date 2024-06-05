<?php

declare(strict_types=1);

namespace App\Handler;

use App\Message\BaseMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

#[AsMessageHandler(fromTransport: 'event_websocket')]
class WebsocketHandler
//    implements MessageSubscriberInterface
{
    public function __invoke(BaseMessage $message): void
    {
        dump('Websocket Handler has run');
    }

//    public static function getHandledMessages(): iterable
//    {
//        yield BaseMessage::class => [
//            'from_transport' => 'event_websocket'
//        ];
//    }

}