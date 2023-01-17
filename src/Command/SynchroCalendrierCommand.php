<?php

namespace App\Command;

use DateInterval;
use Endroid\Calendar\Reader\IcalReader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

#[AsCommand(
    name: 'app:synchro-calendrier',
    description: 'Récupère le calendrier depuis GCal et le push sur Slack',
)]
class SynchroCalendrierCommand extends Command
{
    public function __construct(public ParameterBagInterface $parameterBag, public ChatterInterface $chatter)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('date', InputArgument::OPTIONAL, 'Date à vérifier')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $date = $input->getArgument('date');

        if ($date) {
            $io->note(sprintf('You passed an argument: %s', $date));
        }

        $reader = new IcalReader();

        // Read from URL or path
        $calendar = $reader->readFromUrl($this->parameterBag->get('url_calendrier'));

        $dateStart = new \DateTimeImmutable('now');
        $dateEnd = new \DateTimeImmutable('now');
        $dateEnd = $dateEnd->add(new DateInterval('P1D'));

        $events = $calendar->getEvents($dateStart, $dateEnd);

        foreach ($events as $event) {
            $message = (new ChatMessage($event->getTitle()))
            // if not set explicitly, the message is sent to the
            // default transport (the first one configured)
            ->transport('slack');

            $sentMessage = $this->chatter->send($message);
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
