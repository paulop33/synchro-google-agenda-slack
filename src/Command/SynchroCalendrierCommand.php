<?php

namespace App\Command;

use App\Event\CalendarEvent;
use App\Model\Period1Day;
use App\Model\Period1Month;
use App\Model\Period1Week;
use App\Model\Period2Months;
use App\Model\Period2Weeks;
use App\Model\Period3Months;
use App\Model\PeriodInterface;
use DateInterval;
use Endroid\Calendar\Model\Calendar;
use Endroid\Calendar\Reader\IcalReader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsCommand(
    name: 'app:synchro-calendrier',
    description: 'Récupère le calendrier depuis GCal et le push sur Slack',
)]
class SynchroCalendrierCommand extends Command
{
    public function __construct(public ParameterBagInterface $parameterBag, public EventDispatcherInterface $dispatcher)
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
        
        $periods = [
            new Period1Day(),
            new Period1Week(),
            new Period2Weeks(),
            new Period1Month(),
            new Period2Months(),
            new Period3Months(),
        ];

        foreach ($periods as $period) {
            $events = $this->loadEvents($calendar, $period->getPeriodInterval());
            $this->dispatch($events, $period);
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }

    protected function loadEvents(Calendar $calendar, String $period): array
    {
        $dateStart = (new \DateTimeImmutable('now'))->setTime(0, 0, 0);
        $dateStart = $dateStart->add(new DateInterval($period));
        $dateEnd = $dateStart->setTime(23, 59, 59);

        return $calendar->getEvents($dateStart, $dateEnd);
    }

    protected function dispatch(array $events, PeriodInterface $period): void
    {
        foreach ($events as $event) {
            preg_match("/^\[(MAJEUR|MINEUR|MOYEN)\]/i", $event->getTitle(), $matches);
            if (
                ((!count($matches) || strtoupper($matches[1]) == 'MAJEUR') && $period->shouldNotifyForImportantEvent())
                || (count($matches) && strtoupper($matches[1]) == 'MOYEN' && $period->shouldNotifyForMediumEvent())
                || (count($matches) && strtoupper($matches[1]) == 'MINEUR' && $period->shouldNotifyForLowEvent())
            ) {
                $event = new CalendarEvent($event, $period);
                $this->dispatcher->dispatch($event, CalendarEvent::NAME);
            }
        }
    }
}
