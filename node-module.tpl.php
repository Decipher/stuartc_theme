<?php
// $Id: node.tpl.php,v 1.4 2008/09/15 08:11:49 johnalbin Exp $

/**
 * @file node.tpl.php
 *
 * Theme implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: Node body or teaser depending on $teaser flag.
 * - $picture: The authors picture of the node output from
 *   theme_user_picture().
 * - $date: Formatted creation date (use $created to reformat with
 *   format_date()).
 * - $links: Themed links like "Read more", "Add new comment", etc. output
 *   from theme_links().
 * - $name: Themed username of node author output from theme_user().
 * - $node_url: Direct url of the current node.
 * - $terms: the themed list of taxonomy term links output from theme_links().
 * - $submitted: themed submission information output from
 *   theme_node_submitted().
 *
 * Other variables:
 * - $node: Full node object. Contains data that may not be safe.
 * - $type: Node type, i.e. story, page, blog, etc.
 * - $comment_count: Number of comments attached to the node.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 *
 * Node status variables:
 * - $teaser: Flag for the teaser state.
 * - $page: Flag for the full page state.
 * - $promote: Flag for front page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 */
?>
<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?>"><div class="node-inner">

  <?php print $picture; ?>

  <?php if ($unpublished): ?>
    <div class="unpublished"><?php print t('Unpublished'); ?></div>
  <?php endif; ?>

  <div class="content">
    <div class="title">
      <?php print theme('textimage_image', 'page_title', $title, array(), 'png', $title, $title); ?>
    </div>
    <div class="details">

      <div class="description">
        <?php print $node->content['body']['#value']; ?>
      </div>

      <div class="downloads">
        <?php print theme('textimage_image', 'section_title_red', 'Downloads', array(), 'png', 'Downloads', 'Downloads'); ?>
        <?php print l(t('View all downloads'), 'http://drupal.org/node/'. $node->field_do_nid[0]['value'] .'/release', array('attributes' => array('class' => 'more external'))); ?></li>
        <div class="entries">
          <?php
            foreach ($node->downloads as $type => $type_data) :
              if (count($type_data) > 0) :
                $rows[] = array('data' => array(array('data' => $type .' releases', 'colspan' => 5, 'class' => 'type')));

                foreach ($type_data as $version => $version_data) : 
                  $download = array_shift($version_data);
                  $rows[] = array(
                    'data' => array(
                      $version,
                      str_replace($node->field_project_id[0]['value'] .' '. $version .'-', '', $download['title']),
                      l(str_replace('http://ftp.drupal.org/files/projects/', '', $download['url']), $download['url']),
                      $download['size'],
                      format_date($download['date'], 'custom', 'd-M-Y - H:i:s')
                    ),
                  );
                endforeach;
              endif;
            endforeach;

            print theme('table', array('drupal', 'version', 'download', 'size', 'released'), $rows);
          ?>
        </div>
      </div>

      <?php if (isset($node->changelog)) : ?>
      <div class="changelog">
        <?php print theme('textimage_image', 'section_title_red', 'Changelog - '. $node->changelog['title'], array(), 'png', 'Changelog - '. $node->changelog['title'], 'Changelog - '. $node->changelog['title']); ?>
        <?php print l(t('View full changelog'), $node->path .'/changelog', array('attributes' => array('class' => 'more'))); ?></li>
        <div class="entries">
          <?php print $node->changelog['data']; ?>
        </div>
      </div>
      <?php endif; ?>
      <?php print theme('sexybookmarks', $node); ?>
    </div>
    <div class="resources">

      <div class="screenshot">
        <?php print theme('textimage_image', 'image_title_grey', 'screenshot', array(), 'png', 'screenshot', 'screenshot'); ?>
        <?php if ($node->field_screenshot[0]['view']) : ?>
          <?php print $node->field_screenshot[0]['view']; ?>
        <?php else: ?>
          <?php print theme('textimage_image', 'no_screenshot', 'No screenshot available', array(), 'png', $node->title, $node->title, array('class' => 'no-screenshot')); ?>
        <?php endif; ?>
      </div>

      <?php if (file_exists(file_directory_path() .'/usage/'. $node->field_project_id[0]['value'] .'.png')) : ?>
      <div class="usage">
        <?php print theme('textimage_image', 'image_title_grey', 'usage', array(), 'png', 'usage', 'usage'); ?>
        <?php print l(theme('image', file_directory_path() .'/usage/'. $node->field_project_id[0]['value'] .'.png', $node->title .' usage', $node->title .' usage', array('class' => 'chart')), 'http://drupal.org/project/usage/'. $node->field_project_id[0]['value'], array('html' => true)); ?>
      </div>
      <?php endif; ?>

      <div class="links">
        <?php print theme('textimage_image', 'image_title_grey', 'links', array(), 'png', 'links', 'links'); ?>
        <ul>
          <li><?php print l('Project page', 'http://drupal.org/project/'. $node->field_project_id[0]['value'], array('attributes' => array('class' => 'external'))); ?></li>
          <?php if (isset($node->changelog)) : ?>
          <li><?php print l('Changelog', $node->path .'/changelog'); ?></li>
          <?php endif; ?>
          <li><?php print l('Issue queue', 'http://drupal.org/project/issues/'. $node->field_project_id[0]['value'], array('attributes' => array('class' => 'external'))); ?></li>
          <li><?php print l('Usage statistics', 'http://drupal.org/project/usage/'. $node->field_project_id[0]['value'], array('attributes' => array('class' => 'external'))); ?></li>
          <li><?php print l('Reviews & Ratings', 'http://drupalmodules.com/module/'. str_replace('_', '-', $node->field_project_id[0]['value']), array('attributes' => array('class' => 'external'))); ?></li>
        </ul>
      </div>

    </div>
  </div>

  <?php print $links; ?>

</div></div> <!-- /node-inner, /node -->
