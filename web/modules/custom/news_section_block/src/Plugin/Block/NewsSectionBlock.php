<?php

namespace Drupal\news_section_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Database\Connection;

/**
 * Provides a 'News Section' Block.
 *
 * @Block(
 *   id = "news_section_block",
 *   admin_label = @Translation("News Section Block"),
 * )
 */
class NewsSectionBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $current_user = \Drupal::currentUser();
    $uid = $current_user->id();
    $user = \Drupal\user\Entity\User::load($uid);

    // Get the genre of the current user.
    $user_genre_tid = $user->get('field_genre')->target_id;
    $query = \Drupal::database()->select('node_field_data', 'n')
      ->fields('n')
      ->condition('n.type', 'news')
      ->condition('n.status', 1)
      ->condition('n.uid', $uid, '<>')
      ->join('node__field_genre', 'fg', 'n.nid = fg.entity_id');
      ->condition('fg.field_genre_target_id', $user_genre_tid)
      ->orderBy('n.created', 'DESC')
      ->range(0, 5);

    $results = $query->execute()->fetchAllAssoc('nid');

    $items = [];
    foreach ($results as $node) {
      $node = Node::load($node->nid);
      $items[] = [
        '#theme' => 'item_list',
        '#items' => [
          '#markup' => '<a href="' . $node->toUrl()->toString() . '">' . $node->getTitle() . '</a>',
        ],
      ];
    }

    return [
      '#theme' => 'item_list',
      '#items' => $items,
      '#attached' => [
        'library' => [
          'news_section_block/news_section_block',
        ],
      ],
    ];
  }
}
