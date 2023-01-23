<?php
namespace App\Event;

use Endroid\Calendar\Model\Event as ModelEvent;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * The calendarevent.in3months event is dispatched each time an event is in 3 months
 * in the system.
 */
class CalendarEventIn3MonthsEvent extends Event
{
    public const NAME = 'calendarevent.in3months';

    protected $calendarEvent;

    public function __construct(ModelEvent $calendarEvent)
    {
        $this->calendarEvent = $calendarEvent;
    }

    public function getCalendarEvent(): ModelEvent
    {
        return $this->calendarEvent;
    }
}