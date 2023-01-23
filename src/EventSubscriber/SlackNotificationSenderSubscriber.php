<?php

namespace App\EventSubscriber;

use App\Event\CalendarEventIn3MonthsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackActionsBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackDividerBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackHeaderBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackImageBlockElement;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackSectionBlock;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\ChatterInterface;

class SlackNotificationSenderSubscriber implements EventSubscriberInterface
{
    public function __construct(public ChatterInterface $chatter) {}

    public function onCalendarEvent(CalendarEventIn3MonthsEvent $event): void
    {
        $eventCalendar = $event->getCalendarEvent();
        $message = (new ChatMessage($eventCalendar->getTitle()))
        // if not set explicitly, the message is sent to the
        // default transport (the first one configured)
        ->transport('slack');

        $slackOptions = (new SlackOptions())
        ->block(new SlackHeaderBlock($eventCalendar->getTitle()))
        ->block(
            (new SlackSectionBlock())
            ->field("*Début : *\n". $eventCalendar->getDateStart()->format('d/m/Y H:i'))
            ->field("*Fin : *\n". $eventCalendar->getDateEnd()->format('d/m/Y H:i'))
            ->text(':robot_face: : _Cet événement aura lieu dans 3 mois._')
            ->accessory(
                    new SlackImageBlockElement(
                        'https://media.giphy.com/media/LYteZEhAz0OkLV8HX0/giphy.gif',
                        '3 mois'
                    )
                )
        );
        if (trim($eventCalendar->getDescription())) {
            $slackOptions
            ->block(
                (new SlackSectionBlock())
                    ->text("*Description :*\n".trim($eventCalendar->getDescription()))
            );
        }

        // Add the custom options to the chat message and send the message
        $message->options($slackOptions);
        $sentMessage = $this->chatter->send($message);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CalendarEventIn3MonthsEvent::NAME => 'onCalendarEvent',
        ];
    }
}
