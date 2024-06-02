<?php

use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;

$json = json_decode(file_get_contents(__DIR__ . '/daily.json'), TRUE);
$emails = $json['emails'];

foreach ($emails as $email) {
  $title = $email['title'];
  $title = str_replace(search: '&#039;', replace: '\'', subject: $title);
  $title = str_replace(search: '&quot;', replace: '`', subject: $title);

  /** @var NodeInterface */
  $node = Node::create(['type' => 'daily_email']);
  $node->setTitle($title);
  $node->setCreatedTime($email['date']);
  $node->setChangedTime($email['date']);
  $node->setOwnerId(1);
  $node->set('body', [
    'format' => 'basic_html',
    'value' => $email['text'],
  ]);
  $node->set('path', $email['permalink']);
  $node->save();
}
