<?php

namespace App\EventSubscriber;

use App\Event\AbstractCalendarEvent;
use App\Event\CalendarEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackActionsBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackDividerBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackHeaderBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackImageBlockElement;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackSectionBlock;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SlackNotificationSenderSubscriber implements EventSubscriberInterface
{
    public function __construct(public ChatterInterface $chatter, public TranslatorInterface $translator) {}

    public function onCalendarEvent(CalendarEvent $event): void
    {
        $eventCalendar = $event->getCalendarEvent();
        $period = $event->getPeriod();

        $message = (new ChatMessage($this->cleanTitle($eventCalendar->getTitle())))
        // if not set explicitly, the message is sent to the
        // default transport (the first one configured)
        ->transport('slack');

        $slackOptions = (new SlackOptions())

        ->block(new SlackHeaderBlock($this->cleanTitle($eventCalendar->getTitle())))
        ->block(
            (new SlackSectionBlock())
            ->field("*Début : *\n". $eventCalendar->getDateStart()->format('d/m/Y H:i'))
            ->field("*Fin : *\n". $eventCalendar->getDateEnd()->format('d/m/Y H:i'))
            ->text($this->translator->trans('slack_message.period.'.$period->getPeriodInterval().'.textDelai'))
            ->accessory(
                    new SlackImageBlockElement(
                        $this->translator->trans('slack_message.period.'.$period->getPeriodInterval().'.gif'),
                        $this->translator->trans('slack_message.period.'.$period->getPeriodInterval().'.gifAlt')
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
            CalendarEvent::NAME => 'onCalendarEvent',
        ];
    }

    /**
     * @param string $title
     * @return string
     */
    public function cleanTitle(string $title): string
    {
        return preg_replace("/^\[(MAJEUR|MINEUR|MOYEN)\] ?/i", '', $title);
    }
}
