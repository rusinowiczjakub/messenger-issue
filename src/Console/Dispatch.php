<?php

declare(strict_types=1);

namespace App\Console;

use App\Message\EmailMessage;
use App\Message\WebsocketMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(name: 'app:dispatch')]
class Dispatch extends Command
{
    public function __construct(private readonly MessageBusInterface $messageBus)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        match ($input->getArgument('message')) {
            EmailMessage::class => $this->messageBus->dispatch(new EmailMessage(), [new AmqpStamp('email.message')]),
            WebsocketMessage::class => $this->messageBus->dispatch(new WebsocketMessage(), [new AmqpStamp('websocket.message')])
        };

        return Command::SUCCESS;
    }

        protected function configure(): void
        {
            $this
                ->addArgument('message', InputArgument::REQUIRED, 'Message classname');
        }
}