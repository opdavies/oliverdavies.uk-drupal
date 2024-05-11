<?php

declare(strict_types=1);

namespace Drupal\opdavies_talks\Service;

interface TalkCounterInterface {

  public function getCount(): int;

}
