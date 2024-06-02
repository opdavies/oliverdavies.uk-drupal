<?php

$entityTypeManager = \Drupal::entityTypeManager();
$nodeStorage = $entityTypeManager->getStorage('node');

$emails = $nodeStorage->loadByProperties(['type' => 'daily_email']);
$nodeStorage->delete($emails);
