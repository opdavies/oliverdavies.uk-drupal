<?php

declare(strict_types=1);

namespace Drupal\opdavies_talks\EventSubscriber;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\AbstractEntityEvent;
use Drupal\opdavies_talks\Entity\Node\Talk;
use Drupal\paragraphs\ParagraphInterface;
use Illuminate\Support\Collection;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Update a talk node before it's saved.
 */
final class UpdateTalkNodeBeforeSave implements EventSubscriberInterface {

  private LoggerInterface $logger;

  public function __construct(LoggerChannelFactoryInterface $logger) {
    $this->logger = $logger->get('opdavies_talks');
  }

  public static function getSubscribedEvents() {
    return [
      EntityHookEvents::ENTITY_PRE_SAVE => 'onEntityPreSave',
    ];
  }

  public function onEntityPreSave(AbstractEntityEvent $event): void {
    if ($event->getEntity()->getEntityTypeId() != 'node') {
      return;
    }

    if ($event->getEntity()->bundle() != 'talk') {
      return;
    }

    $node = $event->getEntity();
    $talk = Talk::createFromNode($node);

    $this->reorderEvents($talk);
    $this->updateMostRecentEventDate($talk);
  }

  private function reorderEvents(Talk $talk): void {
    $events = $talk->getEvents();
    if ($events->count() === 1) {
      return;
    };

    $this->logger->debug(sprintf('Sorting events for talk "%s"', $talk->label()));

    $eventsByDate = $this->sortEventsByDate($events);

    // If the original event IDs don't match the sorted event IDs, update the event field to use the sorted ones.
    if ($events->map->id() != $eventsByDate->map->id()) {
      $talk->setEvents($eventsByDate->toArray());
    }
  }

  private function sortEventsByDate(Collection $events): Collection {
    return $events
      ->sortBy(fn(ParagraphInterface $event) => $event->get('field_date')
        ->getString())
      ->values();
  }

  private function updateMostRecentEventDate(Talk $talk): void {
    $this->logger->debug(sprintf('Updating the most recent event date for talk "%s"', $talk->label()));

    $mostRecentEventDate = $talk->findLatestEventDate();

    $talk->set('field_event_date', $mostRecentEventDate);
  }

}
