<?php
// $Id: template.php,v 1.17.2.1 2009/02/13 06:47:44 johnalbin Exp $

define('SC_STATUS_MESSAGE', 'beta 1');

/**
 * @file
 * Contains theme override functions and preprocess functions for the theme.
 *
 * ABOUT THE TEMPLATE.PHP FILE
 *
 *   The template.php file is one of the most useful files when creating or
 *   modifying Drupal themes. You can add new regions for block content, modify
 *   or override Drupal's theme functions, intercept or make additional
 *   variables available to your theme, and create custom PHP logic. For more
 *   information, please visit the Theme Developer's Guide on Drupal.org:
 *   http://drupal.org/theme-guide
 *
 * OVERRIDING THEME FUNCTIONS
 *
 *   The Drupal theme system uses special theme functions to generate HTML
 *   output automatically. Often we wish to customize this HTML output. To do
 *   this, we have to override the theme function. You have to first find the
 *   theme function that generates the output, and then "catch" it and modify it
 *   here. The easiest way to do it is to copy the original function in its
 *   entirety and paste it here, changing the prefix from theme_ to stuartc_theme_.
 *   For example:
 *
 *     original: theme_breadcrumb()
 *     theme override: stuartc_theme_breadcrumb()
 *
 *   where stuartc_theme is the name of your sub-theme. For example, the
 *   zen_classic theme would define a zen_classic_breadcrumb() function.
 *
 *   If you would like to override any of the theme functions used in Zen core,
 *   you should first look at how Zen core implements those functions:
 *     theme_breadcrumbs()      in zen/template.php
 *     theme_menu_item_link()   in zen/template.php
 *     theme_menu_local_tasks() in zen/template.php
 *
 *   For more information, please visit the Theme Developer's Guide on
 *   Drupal.org: http://drupal.org/node/173880
 *
 * CREATE OR MODIFY VARIABLES FOR YOUR THEME
 *
 *   Each tpl.php template file has several variables which hold various pieces
 *   of content. You can modify those variables (or add new ones) before they
 *   are used in the template files by using preprocess functions.
 *
 *   This makes THEME_preprocess_HOOK() functions the most powerful functions
 *   available to themers.
 *
 *   It works by having one preprocess function for each template file or its
 *   derivatives (called template suggestions). For example:
 *     THEME_preprocess_page    alters the variables for page.tpl.php
 *     THEME_preprocess_node    alters the variables for node.tpl.php or
 *                              for node-forum.tpl.php
 *     THEME_preprocess_comment alters the variables for comment.tpl.php
 *     THEME_preprocess_block   alters the variables for block.tpl.php
 *
 *   For more information on preprocess functions and template suggestions,
 *   please visit the Theme Developer's Guide on Drupal.org:
 *   http://drupal.org/node/223440
 *   and http://drupal.org/node/190815#template-suggestions
 */


/*
 * Add any conditional stylesheets you will need for this sub-theme.
 *
 * To add stylesheets that ALWAYS need to be included, you should add them to
 * your .info file instead. Only use this section if you are including
 * stylesheets based on certain conditions.
 */
/* -- Delete this line if you want to use and modify this code
// Example: optionally add a fixed width CSS file.
if (theme_get_setting('stuartc_theme_fixed')) {
  drupal_add_css(path_to_theme() . '/layout-fixed.css', 'theme', 'all');
}
// */


/**
 * Implementation of HOOK_theme().
 */
function stuartc_theme_theme(&$existing, $type, $theme, $path) {
  $hooks = zen_theme($existing, $type, $theme, $path);
  // Add your theme hooks like this:
  /*
  $hooks['hook_name_here'] = array( // Details go here );
  */
  // @TODO: Needs detailed comments. Patches welcome!
  return $hooks;
}

/**
 * Override or insert variables into all templates.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered (name of the .tpl.php file.)
 */
/* -- Delete this line if you want to use this function
function stuartc_theme_preprocess(&$vars, $hook) {
  dpm($hook);
}
// */

/**
 * Override or insert variables into the page templates.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("page" in this case.)
 */
function stuartc_theme_preprocess_page(&$vars, $hook) {
  // Use textimage for logo.
  $vars['logo'] = base_path() . theme('textimage_image', 'logo', 'http://stuar.tc/lark', array(SC_STATUS_MESSAGE), 'png', '', '', array(), TRUE, FALSE);

  // Set <title> to lower case.
  $vars['head_title'] = drupal_strtolower($vars['head_title']);

  // Remove Tabs.
  unset($vars['tabs']);

  // Remove Title.
  unset($vars['title']);

  // Remove Messages.
  unset($vars['messages']);

  // Modify page variables for user profile
  if ($vars['template_files'][1] == 'page-user-1') {
    $vars['head_title'] = str_replace(drupal_strtolower($vars['title']), drupal_strtolower(t('Biography')), $vars['head_title']);
    $vars['title'] = t('Biography');
  }
}

/**
 * Override or insert variables into the node templates.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("node" in this case.)
 */
function stuartc_theme_preprocess_node(&$vars, $hook) {
  if ($vars['type'] == 'design') {
    drupal_goto('s/design/s', NULL, $vars['nid'] . $vars['created']);
  }

  if (!$vars['page']) {
    $vars['template_files'][] = 'node-'. $vars['type'] .'-teaser';
  }

  if ($vars['type'] == 'module') {
    // Replace H2s with Textimage header.
    $vars['node']->content['body']['#value'] = preg_replace('/\<h2\>(.*?)\<\/h2\>/e', '"<h2 class=\"section-title-red\">". theme("textimage_image", "section_title_red", "\\1") ."</h2>";', $vars['node']->content['body']['#value']);

    // Load downloads into Node object.
    $result = db_fetch_object(db_query(
      "SELECT * FROM {stuartc_module_module_downloads} WHERE module = '%s'",
      $vars['node']->field_project_id[0]['value']
    ));

    $vars['node']->downloads = array('stable' => array(), 'dev' => array());
    $downloads = unserialize($result->data);
    foreach ($downloads as $download) {
      if (!preg_match('/(\d+\.\d+\..?)/', $download['title'])) {
        if (preg_match('/(\d+\..?)-(\d+\.\d+)/', $download['title'], $match)) {
          $vars['node']->downloads['stable'][$match[1]][$match[2]] = $download;
        }
        elseif (preg_match('/(\d+\..?)-(\d+\..?)/', $download['title'], $match)) {
          $vars['node']->downloads['dev'][$match[1]][$match[2]] = $download;
        }
      }
    }
    foreach ($vars['node']->downloads as &$download_type) {
      krsort($download_type);
      foreach ($download_type as &$download_ver) {
        krsort($download_ver);
      }
    }

    // Process changelog.
    if (!empty($vars['field_changelog_entry'][0]['value'])) {
      $temp = explode("\r", $vars['field_changelog_entry'][0]['value']);
      unset($temp[1], $temp[2]);

      $vars['node']->changelog = array(
        'title' => array_shift($temp),
        'data' => implode("<br />\r", $temp)
      );

      $vars['node']->changelog['data'] = preg_replace('/(#)(\d*)/', '<a href="http://drupal.org/node/\\2">\\1\\2</a>', $vars['node']->changelog['data']);
    }
  }
}

/**
 * Override or insert variables into the comment templates.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("comment" in this case.)
 */
/* -- Delete this line if you want to use this function
function stuartc_theme_preprocess_comment(&$vars, $hook) {
  $vars['sample_variable'] = t('Lorem ipsum.');
}
// */

/**
 * Override or insert variables into the block templates.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("block" in this case.)
 */
function stuartc_theme_preprocess_block(&$vars, $hook) {
  if ($preset = variable_get('stuartc_block_'. $vars['block']->module .'-'. $vars['block']->delta .'_preset', 0)) {
    $vars['block']->subject = theme('textimage_image', $preset, $vars['block']->subject, array(), 'png', $vars['block']->subject, $vars['block']->subject);
    $vars['classes'] .= ' clear-block '. $preset;
  }
}

/**
 * Override or insert variables into the views_view_unformatted.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("views_view_unformatted" in this case.)
 */
function stuartc_theme_preprocess_views_view_unformatted(&$vars, $hook) {
  // 'Modules' view count.
  switch ($vars['view']->name) {
    case 'design':
      drupal_add_js(drupal_get_path('theme', 'stuartc_theme') .'/js/designs.js');
      break;

    case 'modules':
      $count = 0;
      foreach ($vars['classes'] as &$class) {
        $class .= ' views-row-pos-'. ($count % 3 + 1);
        $count++;
      }
      break;

    case 'photos':
      $count = 0;
      foreach ($vars['classes'] as &$class) {
        $class .= ' views-row-pos-'. ($count % 5 + 1);
        $count++;
      }
      break;
  }
}

/**
 * Override or insert variables into the user profile templates.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("user_profile" in this case.)
 */
function stuartc_theme_preprocess_user_profile(&$vars, $hook) {
  drupal_goto('<front>');
}

function stuartc_theme_links($links, $attributes = array('class' => 'links')) {
  global $language;
  $output = '';

  if (count($links) > 0) {
    $output = '<ul'. drupal_attributes($attributes) .'>';

    $num_links = count($links);
    $i = 1;

    foreach ($links as $key => $link) {
      $class = $key;

      // Add first, last and active classes to the list of links to help out themers.
      if ($i == 1) {
        $class .= ' first';
      }
      if ($i == $num_links) {
        $class .= ' last';
      }
      $active = FALSE;
      if (isset($link['href']) && ($link['href'] == $_GET['q'] || ($link['href'] == '<front>' && drupal_is_front_page()))
          && (empty($link['language']) || $link['language']->language == $language->language)) {
        $class .= ' active';
        $active = TRUE;
      }

      $output .= '<li'. drupal_attributes(array('class' => $class)) .'>';

      if (isset($link['href'])) {
        if (strstr($key, 'menu-')) {
          $preset = 'primary_links';
          $additional_text = array($link['title']);
          if ($active) {
            $preset .= '_active';
            $additional_text = array();
          }

          $link['title'] = drupal_strtolower($link['title']);
          $link['title'] = theme('textimage_image', $preset, $link['title'], $additional_text);
          $link['html'] = TRUE;
        }

        // Pass in $link as $options, they share the same keys.
        $output .= l($link['title'], $link['href'], $link);
      }
      else if (!empty($link['title'])) {
        // Some links are actually not links, but we wrap these in <span> for adding title and class attributes
        if (empty($link['html'])) {
          $link['title'] = check_plain($link['title']);
        }
        $span_attributes = '';
        if (isset($link['attributes'])) {
          $span_attributes = drupal_attributes($link['attributes']);
        }
        $output .= '<span'. $span_attributes .'>'. $link['title'] .'</span>';
      }

      $output .= "</li>\n";
      $i++;
    }

    $output .= '</ul>';
  }

  return $output;
}
