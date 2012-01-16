<?php
/*
Plugin Name: Less Sodium
Plugin URI: ...
Description: Simple WordPress plugin to catch some spam.
Version: 0.0.1
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

function check_spam() {
  /* For now, spam checking is done by just checking
     for common spam words in the comment meta data. */
  $spam_list = array(
    'ugg',
    'uggs',
    'nike',
    'louisvuitton',
    'louis vuitton',
    'gucci',
    'index.php',
    '.php',
    'nba',
    'pharmacy',
    'hermes',
    'store',
    'backlinks',
    'xrumer',
    'cash',
    'survey',
    'calculator',
    'clothes',
    'reddit',
    'singles',
    'loan'
  );
  foreach($spam_list as $keyword) {
    if (preg_match("/$keyword/i", $_POST['author']) ||
        preg_match("/$keyword/i", $_POST['email']) ||
        preg_match("/$keyword/i", $_POST['url'])) {
      # keyword found... probably spam
      echo '<h1>We could be wrong, but...</h1>Sorry, your comment looks like spam.  If you are a real person, please try contacting my email address if you would like to leave a comment!  Thanks.<br /><br />Sincerely,<br />Less Sodium';
      exit;
    }
  }
}

add_action('comment_post', 'check_spam');

?>
