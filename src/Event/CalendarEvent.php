<?php
namespace App\Event;

use App\Model\PeriodInterface;
use Endroid\Calendar\Model\Event as ModelEvent;
use Symfony\Contracts\EventDispatcher\Event;

class CalendarEvent extends Event
{
    public const NAME = 'calendar.event';

    protected $calendarEvent;

    protected $period;

    public function __construct(ModelEvent $calendarEvent, PeriodInterface $period)
    {
        $this->calendarEvent = $calendarEvent;
        $this->period = $period;
    }

    public function getCalendarEvent(): ModelEvent
    {
        return $this->calendarEvent;
    }

    public function getPeriod(): PeriodInterface
    {
        return $this->period;
    }
}
