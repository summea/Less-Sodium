<?php
/*
Plugin Name: Less Sodium
Plugin URI: https://github.com/summea/less-sodium
Description: Simple WordPress plugin to catch some spam.
Version: 0.0.5
Author: summea
License: GPL2

Copyright 2012  summea

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define("BASE_DIR", "/less-sodium");
define("DATA_DIR", BASE_DIR . "/data");

/**
 * Check comment form for possible spam before saving to database.
 * @return int 
 */
function check_for_spam() {
  $error_message =  '<b>We could be wrong, but...</b><br />' .
                    'Your comment looks like spam.<br />' .
                    'Please find another way to contact us...!' . 
                    '<br /><br />Sincerely,<br />Less Sodium';

  # check email address for clues
  if (preg_match("/^[A-Z]/", $_POST['email'])) {
    log_spam();
    wp_die($error_message); # probably spam
    exit;
  }

  # look through wordlist for common spam keywords
  if (file_exists(WP_PLUGIN_DIR . DATA_DIR . '/wordlist.txt')) {
    $data = file_get_contents(WP_PLUGIN_DIR . DATA_DIR . '/wordlist.txt', true);
    $spam_keywords = split("\n", $data);
    array_pop($spam_keywords); # remove the last newline

    foreach($spam_keywords as $keyword) {
      if (preg_match("/$keyword/i", $_POST['author']) ||
          preg_match("/$keyword/i", $_POST['email']) ||
          preg_match("/$keyword/i", $_POST['url']) ||
          preg_match("/$keyword/i", $_POST['comment']))
      {
        log_spam();
        wp_die($error_message); # keyword found... probably spam
        exit;
      }
    }
  } else {
    # create wordlist.txt
    $file = fopen(WP_PLUGIN_DIR . DATA_DIR . '/wordlist.txt', 'w');
    fclose($file);
  }
  return 1;
}

/**
 * Log spam to text file.
 * @return int count
 */
function log_spam() {
  $count = 0;
  if (file_exists(WP_PLUGIN_DIR . DATA_DIR . '/spamcount.txt')) {
    $data = file_get_contents(WP_PLUGIN_DIR . DATA_DIR . 
      '/spamcount.txt', true);
    $filtered_data = preg_replace('/[^0-9]/', '', $data);
    $count += (int)$filtered_data + 1;
  }
  $file = fopen(WP_PLUGIN_DIR . DATA_DIR . '/spamcount.txt', 'w');
  fwrite($file, $count);
  fclose($file);
  return $count;
}

/**
 * Get spam count from spamcount.txt
 * @return int count
 */
function get_spam_count() {
  $count = 0;
  if (file_exists(WP_PLUGIN_DIR . DATA_DIR . '/spamcount.txt')) {
    $data = file_get_contents(WP_PLUGIN_DIR . DATA_DIR . 
      '/spamcount.txt', true);
    $filtered_data = preg_replace('/[^0-9]/', '', $data);
    if ((int)$filtered_data > 0)
      $count = (int)$filtered_data;
  }
  return $count;
}

add_action('check_comment_flood', 'check_for_spam');

?>
