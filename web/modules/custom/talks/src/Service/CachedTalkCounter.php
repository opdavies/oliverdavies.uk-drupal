<?php

declare(strict_types=1);

namespace Drupal\opdavies_talks\Service;

use Drupal\Core\Cache\CacheBackendInterface;

final class CachedTalkCounter {

  public function __construct(
    private TalkCounter $talkCounter,
    private CacheBackendInterface $cache,
  ) {}

  public function getCount(): int {
    if ($cacheData = $this->cache->get(cid: 'talk_count')) {
      return $cacheData->data;
    }

    $count = $this->talkCounter->getCount();

    $this->cache->set(
      cid: 'talk_count',
      data: $count,
      expire: strtotime('tomorrow'),
    );

    return $count;
  }

}
