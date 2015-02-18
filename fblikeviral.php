<?php

/*
Plugin Name: FB Like Viral (Developer)
Plugin URI: http://www.fblikeviral.com/
Version: 1.2.1
Author: Valentin Jed
Author URI: http://www.micca.eu
Description: The Viral Traffic Multiplier for Wordpress.
*/

register_activation_hook( __FILE__, 'fblikeviral_install' );

function fblikeviral_install() {
  $options = fblikeviral_options();

  foreach ($options as $option => $value) {
    if (get_option('fblikeviral_' . $option) === false) {
      update_option('fblikeviral_' . $option, $value);
    }
  }
}

function fblikeviral_add_panel() {
  add_menu_page('FB Like Viral Settings', 'FB Like Viral', 'manage_options', 'fblikeviral', 'fblikeviral_settings_panel', plugin_dir_url(__FILE__) . '/icon.png');

  add_submenu_page('fblikeviral', 'Popup Settings', 'Popup Settings', 'manage_options', 'fblikeviral', 'fblikeviral_settings_panel');
  add_submenu_page('fblikeviral', 'Popup Styling', 'Popup Styling', 'manage_options', 'fblikeviral-style', 'fblikeviral_style_panel');
  add_submenu_page('fblikeviral', 'Content Buttons', 'Content Buttons', 'manage_options', 'fblikeviral-content', 'fblikeviral_content_panel');
  add_submenu_page('fblikeviral', 'Affiliate Setup', 'Affiliate Setup', 'manage_options', 'fblikeviral-affiliate', 'fblikeviral_affiliate_panel');
}

function fblikeviral_save($valid = array()) {
  if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $options = fblikeviral_options();

    foreach ($options as $option => $value) {
      if(in_array($option, $valid)) {
        if(isset($_POST['fb_' . $option])) {
          $d = $_POST['fb_' . $option];

          if(is_array($d)) {
            update_option('fblikeviral_' . $option, implode(',', $d));
          } else {
            update_option('fblikeviral_' . $option, $d);
          }
        } else {
          update_option('fblikeviral_' . $option, null);
        }
      }
    }

    return '<div class="updated">' . 'Changes Saved!' . '</div>';
  }
  
  return null;
}

function fblikeviral_affiliate_panel() {
  $message = fblikeviral_save(array('powered', 'affiliate'));
?>
<div class="wrap">
  <h2>FB Like Viral Affiliate Setup</h2>

<?php if($message): ?>
    <?php echo $message; ?>
<?php endif;?>

  <form method="post">
    <table class="form-table">
      <tr valign="top">
        <th scope="row"><label for="fbpowered">Show "Powered By" Link</label></th>
        <td>
          <input type="checkbox" name="fb_powered" value="1"<?php if(get_option('fblikeviral_powered')):?> checked="checked"<?php endif; ?> /><br />
        </td>
      </tr>
      <tr valign="top">
        <th scope="row"><label for="fbpoweredaff">Affiliate Link</label></th>
        <td>
          <input type="text" name="fb_affiliate" value="<?php echo get_option('fblikeviral_affiliate'); ?>" style="width: 300px;" /><br />
          <span class="description">The "Powered By" link will be replaced with your affiliate link.<br />To join our affiliate program please click <a href="http://fblikeviral.com/affiliates" target="_blank">here</a>.</span>
        </td>
      </tr>
    </table>

    <p class="submit">
      <input type="submit" value="Save Changes" class="button-primary" name="Submit">
    </p>

  </form>
</div>
<?php
}

function fblikeviral_content_panel() {
  $message = fblikeviral_save(array('show_posts', 'content_count','show_content_send',
    'show_content_like', 'show_content_share', 'show_content_share2', 'show_content_one',
    'content_url', 'content_custom_url', 'content_cats', 'hidesec'));

  $cats  = explode(',', get_option('fblikeviral_content_cats'));
  $hides = explode(',', get_option('fblikeviral_hidesec'));

  $catdrop = wp_dropdown_categories(
    array(
      'show_count' => false,
      'hide_empty' => false,
      'name' => 'fb_content_cats[]',
      'echo' => false,
      'hierarchical' => true,
    )
  );

  $catdrop = str_replace('<select ', '<select multiple="multiple" size="10" style="width: 300px; height: 100px;" ', $catdrop);

  if(is_array($cats) && ! empty($cats)) {
    foreach($cats as $cat) {
      $catdrop = str_replace(' value="'.(int)$cat.'">', ' value="'.(int)$cat.'" selected="selected">', $catdrop);
    }
  }
?>
<div class="wrap">
  <h2>FB Like Viral Content Buttons</h2>

<?php if($message): ?>
    <?php echo $message; ?>
<?php endif;?>

  <form method="post">
    <table class="form-table">
      <tr valign="top">
        <th scope="row"><label for="fbposts">Display Buttons in Content</label></th>
        <td>
          <select name="fb_show_posts" style="width: 110px;">
            <option value="no"<?php if(get_option('fblikeviral_show_posts') == 'no'):?> selected="selected"<?php endif;?>>No</option>
            <option value="top"<?php if(get_option('fblikeviral_show_posts') == 'top'):?> selected="selected"<?php endif;?>>Top</option>
            <option value="bottom"<?php if(get_option('fblikeviral_show_posts') == 'bottom'):?> selected="selected"<?php endif;?>>Bottom</option>
            <option value="both"<?php if(get_option('fblikeviral_show_posts') == 'both'):?> selected="selected"<?php endif;?>>Top &amp; Bottom</option>
          </select>
          <br /><span class="description">Display the checked buttons also in posts and pages.</span>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"><label for="fblike">Content Buttons</label></th>
        <td>
          <input type="checkbox" name="fb_show_content_send" value="1"<?php if(get_option('fblikeviral_show_content_send')):?> checked="checked"<?php endif; ?> /> Facebook Send<br />
          <input type="checkbox" name="fb_show_content_like" value="1"<?php if(get_option('fblikeviral_show_content_like')):?> checked="checked"<?php endif; ?> /> Facebook Like<br />
          <input type="checkbox" name="fb_show_content_share" value="1"<?php if(get_option('fblikeviral_show_content_share')):?> checked="checked"<?php endif; ?> /> Facebook Share<br />
          <input type="checkbox" name="fb_show_content_share2" value="1"<?php if(get_option('fblikeviral_show_content_share2')):?> checked="checked"<?php endif; ?> /> Twitter Share<br />
          <input type="checkbox" name="fb_show_content_one" value="1"<?php if(get_option('fblikeviral_show_content_one')):?> checked="checked"<?php endif; ?> /> Google One
          <br /><span class="description">Please select buttons that should appear in the content.</span>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"><label for="fburl">Link To Share</label></th>
        <td>
          <input type="radio" name="fb_content_url" value="home"<?php if(get_option('fblikeviral_content_url') == 'home'):?> checked="checked"<?php endif;?> /> Home Page<br />
          <input type="radio" name="fb_content_url" value="current"<?php if(get_option('fblikeviral_content_url') == 'current'):?> checked="checked"<?php endif;?> /> Current Page<br />
          <input type="radio" name="fb_content_url" value="custom"<?php if(get_option('fblikeviral_content_url') == 'custom'):?> checked="checked"<?php endif;?> /> Custom URL<br />
          Custom URL: <input type="text" name="fb_content_custom_url" style="width: 300px;" value="<?php echo get_option('fblikeviral_content_custom_url'); ?>" />
          <br /><span class="description">Select a page or enter the URL that will be shared.</span>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"><label for="show_count">Display Count</label></th>
        <td>
          <select name="fb_content_count" style="width: 55px;">
            <option value="0"<?php if(get_option('fblikeviral_content_count') == 0):?> selected="selected"<?php endif;?>>No</option>
            <option value="1"<?php if(get_option('fblikeviral_content_count') == 1):?> selected="selected"<?php endif;?>>Yes</option>
          </select>
          <br /><span class="description">Display the number of likes / shares.</span>
        </td>
      </tr>
    </table>

    <h3>Targeting</h3>

    <table class="form-table">
      <tr valign="top">
        <th scope="row"><label for="fbsection">Hide Buttons in</label></th>
        <td>
          <input type="checkbox" name="fb_hidesec[]" value="post"<?php if(in_array('post', $hides)): ?> checked="checked"<?php endif; ?> /> Posts<br />
          <input type="checkbox" name="fb_hidesec[]" value="page"<?php if(in_array('page', $hides)): ?> checked="checked"<?php endif; ?> /> Pages<br />
          <input type="checkbox" name="fb_hidesec[]" value="category"<?php if(in_array('category', $hides)): ?> checked="checked"<?php endif; ?> /> Categories<br />
          <input type="checkbox" name="fb_hidesec[]" value="archive"<?php if(in_array('archive', $hides)): ?> checked="checked"<?php endif; ?> /> Archive<br />
          <input type="checkbox" name="fb_hidesec[]" value="tags"<?php if(in_array('tags', $hides)): ?> checked="checked"<?php endif; ?> /> Tags<br />
          <input type="checkbox" name="fb_hidesec[]" value="author"<?php if(in_array('author', $hides)): ?> checked="checked"<?php endif; ?> /> Author Page<br />
          <input type="checkbox" name="fb_hidesec[]" value="search"<?php if(in_array('search', $hides)): ?> checked="checked"<?php endif; ?> /> Search Results
        </td>
      </tr>
      <tr valign="top">
        <th scope="row"><label for="fbsection">Hide in Categories</label></th>
        <td>
          <?php echo $catdrop; ?>
        </td>
      </tr>
    </table>

    <p class="submit">
      <input type="submit" value="Save Changes" class="button-primary" name="Submit">
    </p>

  </form>
</div>
<?php
}

function fblikeviral_style_panel() {
  $message = fblikeviral_save(array('message', 'style_bg', 'style_color', 'style_font',
                'style_size', 'style_opacity'));

  $fonts   = array('- Font', 'Verdana', 'Arial', 'Helvetica', 'Sans-Serif');
  $fsizes  = array('- Size', 10, 12, 14, 18, 20, 22);

?>
<div class="wrap">
  <h2>FB Like Viral Popup Styling</h2>

<?php if($message): ?>
    <?php echo $message; ?>
<?php endif;?>

  <h3>Box Content</h3>

  <form method="post">
    <table class="form-table">
      <tr valign="top">
        <th scope="row"><label for="fbmessage">Your Message</label></th>
        <td>
          <textarea name="fb_message" rows="10" cols="50"><?php echo stripslashes(get_option('fblikeviral_message')); ?></textarea>
          <br /><span class="description">Enter the message that should be displayed inside the popup.<br />HTML tags allowed.</span>
        </td>
      </tr>
    </table>

    <h3>Box Styling</h3>

    <table class="form-table">
      <tr valign="top">
        <th scope="row"><label for="fbbg">Background Color</label></th>
        <td>
          <input name="fb_style_bg" type="text" id="fb_style_bg" value="<?php echo get_option('fblikeviral_style_bg'); ?>" class="medium-text color {required:false}" />
        </td>
      </tr>
      <tr valign="top">
        <th scope="row"><label for="fbbg">Font Color</label></th>
        <td>
          <input name="fb_style_color" type="text" id="fb_style_color" value="<?php echo get_option('fblikeviral_style_color'); ?>" class="medium-text color {required:false}" />
        </td>
      </tr>
      <tr valign="top">
        <th scope="row"><label for="fbbg">Font &amp; Size</label></th>
        <td>
            <select name="fb_style_font" style="width: 100px;">
<?php foreach ($fonts as $font): ?>
              <option value="<?php echo $font; ?>"<?php if($font == get_option('fblikeviral_style_font')): ?> selected="selected"<?php endif;?>><?php echo $font; ?></option>
<?php endforeach; ?>
            </select>

            <select name="fb_style_size" style="width: 64px;">
<?php foreach ($fsizes as $fsize): ?>
              <option value="<?php echo $fsize; ?>"<?php if($fsize == get_option('fblikeviral_style_size')): ?> selected="selected"<?php endif;?>><?php echo $fsize; ?></option>
<?php endforeach; ?>
            </select> px
        </td>
      </tr>
      <tr valign="top">
        <th scope="row"><label for="fbopacity">Transperancy (0 - 100)</label></th>
        <td>
          <input type="text" name="fb_style_opacity" style="width: 40px;" value="<?php echo get_option('fblikeviral_style_opacity'); ?>" /> %
        </td>
      </tr>
    </table>

    <p class="submit">
      <input type="submit" value="Save Changes" class="button-primary" name="Submit">
    </p>

  </form>
</div>
<?php
}

function fblikeviral_settings_panel() {
  $message = fblikeviral_save(array('show_facebook_send', 'show_facebook_like', 'show_facebook_share',
    'show_twitter_share', 'show_google_one', 'show_count', 'effect', 'url', 'homepage', 'repeat',
    'trigger', 'trigger_timeout', 'timeout', 'twitter_via', 'hidebox', 'box_cats', 'trigger_hits',
    'countdown'));

  $cats  = explode(',', get_option('fblikeviral_box_cats'));
  $hides = explode(',', get_option('fblikeviral_hidebox'));

  $catdrop = wp_dropdown_categories(
    array(
      'show_count' => false,
      'hide_empty' => false,
      'name' => 'fb_box_cats[]',
      'echo' => false,
      'hierarchical' => true,
    )
  );

  $catdrop = str_replace('<select ', '<select multiple="multiple" size="10" style="width: 300px; height: 100px;" ', $catdrop);

  if(is_array($cats) && ! empty($cats)) {
    foreach($cats as $cat) {
      $catdrop = str_replace(' value="'.(int)$cat.'">', ' value="'.(int)$cat.'" selected="selected">', $catdrop);
    }
  }

?>
<div class="wrap">
  <h2>FB Like Viral Popup Settings</h2>

<?php if($message): ?>
    <?php echo $message; ?>
<?php endif;?>
  
  <form method="post">
    <table class="form-table">
      <tr valign="top">
        <th scope="row"><label for="fblike">Popup Buttons</label></th>
        <td>
          <input type="checkbox" name="fb_show_facebook_send" value="1"<?php if(get_option('fblikeviral_show_facebook_send')):?> checked="checked"<?php endif; ?> /> Facebook Send<br />
          <input type="checkbox" name="fb_show_facebook_like" value="1"<?php if(get_option('fblikeviral_show_facebook_like')):?> checked="checked"<?php endif; ?> /> Facebook Like<br />
          <input type="checkbox" name="fb_show_facebook_share" value="1"<?php if(get_option('fblikeviral_show_facebook_share')):?> checked="checked"<?php endif; ?> /> Facebook Share<br />
          <input type="checkbox" name="fb_show_twitter_share" value="1"<?php if(get_option('fblikeviral_show_twitter_share')):?> checked="checked"<?php endif; ?> /> Twitter Share<br />
          <input type="checkbox" name="fb_show_google_one" value="1"<?php if(get_option('fblikeviral_show_google_one')):?> checked="checked"<?php endif; ?> /> Google One
          <br /><span class="description">Please select buttons that should appear in the popup.</span>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"><label for="show_count">Twitter Username</label></th>
        <td>
          <input type="text" name="fb_twitter_via" value="<?php echo get_option('fblikeviral_twitter_via'); ?>" style="width: 300px;" />
          <br /><span class="description">Screen name of the user to attribute the Tweet to.<br />Same username will be used in the content button.</span>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"><label for="show_count">Display Count</label></th>
        <td>
          <select name="fb_show_count" style="width: 55px;">
            <option value="0"<?php if(get_option('fblikeviral_show_count') == 0):?> selected="selected"<?php endif;?>>No</option>
            <option value="1"<?php if(get_option('fblikeviral_show_count') == 1):?> selected="selected"<?php endif;?>>Yes</option>
          </select>
          <br /><span class="description">Display the number of likes / shares.</span>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"><label for="fbeffect">Popup Appearance</label></th>
        <td>
          <select name="fb_effect" style="width: 100px;">
            <option value="popup"<?php if(get_option('fblikeviral_effect') == 'popup'):?> selected="selected"<?php endif;?>>Pop-Up</option>
            <option value="slide"<?php if(get_option('fblikeviral_effect') == 'slide'):?> selected="selected"<?php endif;?>>Slide down</option>
            <option value="fade"<?php if(get_option('fblikeviral_effect') == 'fade'):?> selected="selected"<?php endif;?>>Fade in</option>
            <option value="disabled"<?php if(get_option('fblikeviral_effect') == 'disabled'):?> selected="selected"<?php endif;?>>Disabled</option>
          </select>
          <br /><span class="description">Select how the popup should appear on the site.</span>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"><label for="fburl">Link To Share</label></th>
        <td>
          <input type="radio" name="fb_url" value="home"<?php if(get_option('fblikeviral_url') == 'home'):?> checked="checked"<?php endif;?> /> Home Page<br />
          <input type="radio" name="fb_url" value="current"<?php if(get_option('fblikeviral_url') == 'current'):?> checked="checked"<?php endif;?> /> Current Page<br />
          <input type="radio" name="fb_url" value="custom"<?php if(get_option('fblikeviral_url') == 'custom'):?> checked="checked"<?php endif;?> /> Custom URL<br />
          Custom URL: <input type="text" name="fb_custom_url" style="width: 300px;" value="<?php echo get_option('fblikeviral_custom_url'); ?>" />
          <br /><span class="description">Select a page or enter the URL that will be shared.</span>
        </td>
      </tr>
    </table>
    
    <h3>Targeting</h3>
    
    <table class="form-table">
      <tr valign="top">
        <th scope="row"><label for="fbtrigger">Display The Popup</label></th>
        <td>
          <input type="radio" name="fb_trigger" value="end"<?php if(get_option('fblikeviral_trigger') == 'end'):?> checked="checked"<?php endif;?> /> When The Visitor Scrolls To The End<br />
          or<br />
          <input type="radio" name="fb_trigger" value="after"<?php if(get_option('fblikeviral_trigger') == 'after'):?> checked="checked"<?php endif;?> /> 
          <input type="text" name="fb_trigger_timeout" style="width: 40px;" value="<?php echo get_option('fblikeviral_trigger_timeout'); ?>" /> Seconds After Loading The Content<br />
          or<br />
          <input type="radio" name="fb_trigger" value="hits"<?php if(get_option('fblikeviral_trigger') == 'hits'):?> checked="checked"<?php endif;?> />
          After Every <input type="text" name="fb_trigger_hits" style="width: 40px;" value="<?php echo get_option('fblikeviral_trigger_hits'); ?>" /> Pageview
          <br /><span class="description">When should the popup appear.</span>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"><label for="fbtimeout">Hide The Popup</label></th>
        <td>
          After <input type="text" name="fb_timeout" style="width: 40px;" value="<?php echo get_option('fblikeviral_timeout'); ?>" /> Seconds
          <br /><span class="description">If no action is detected the popup will disappear after some amount of time.</span>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"><label for="fbcountdown">Display Countdown</label></th>
        <td>
          <select name="fb_countdown" style="width: 55px;">
            <option value="0"<?php if(get_option('fblikeviral_countdown') == 0):?> selected="selected"<?php endif;?>>No</option>
            <option value="1"<?php if(get_option('fblikeviral_countdown') == 1):?> selected="selected"<?php endif;?>>Yes</option>
          </select>
          <br /><span class="description">Display the countdown timer in the popup.</span>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"><label for="fbhome">Display On Front Page</label></th>
        <td>
          <select name="fb_homepage" style="width: 55px;">
            <option value="0"<?php if(get_option('fblikeviral_homepage') == 0):?> selected="selected"<?php endif;?>>No</option>
            <option value="1"<?php if(get_option('fblikeviral_homepage') == 1):?> selected="selected"<?php endif;?>>Yes</option>
          </select>
          <br /><span class="description">Should the popup also appear on the front page of your blog.</span>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"><label for="fbrepeat">Display To Return Visitors</label></th>
        <td>
          <select name="fb_repeat" style="width: 55px;">
            <option value="0"<?php if(get_option('fblikeviral_repeat') == 0):?> selected="selected"<?php endif;?>>No</option>
            <option value="1"<?php if(get_option('fblikeviral_repeat') == 1):?> selected="selected"<?php endif;?>>Yes</option>
          </select>
          <br /><span class="description">Should the popup be displayed to return visitors.<br />Cookie expires in 2 days.</span>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"><label for="fbsection">Don't display in</label></th>
        <td>
          <input type="checkbox" name="fb_hidebox[]" value="post"<?php if(in_array('post', $hides)): ?> checked="checked"<?php endif; ?> /> Posts<br />
          <input type="checkbox" name="fb_hidebox[]" value="page"<?php if(in_array('page', $hides)): ?> checked="checked"<?php endif; ?> /> Pages<br />
          <input type="checkbox" name="fb_hidebox[]" value="category"<?php if(in_array('category', $hides)): ?> checked="checked"<?php endif; ?> /> Categories<br />
          <input type="checkbox" name="fb_hidebox[]" value="archive"<?php if(in_array('archive', $hides)): ?> checked="checked"<?php endif; ?> /> Archive<br />
          <input type="checkbox" name="fb_hidebox[]" value="tags"<?php if(in_array('tags', $hides)): ?> checked="checked"<?php endif; ?> /> Tags<br />
          <input type="checkbox" name="fb_hidebox[]" value="author"<?php if(in_array('author', $hides)): ?> checked="checked"<?php endif; ?> /> Author Page<br />
          <input type="checkbox" name="fb_hidebox[]" value="search"<?php if(in_array('search', $hides)): ?> checked="checked"<?php endif; ?> /> Search Results
        </td>
      </tr>
      
      <tr valign="top">
        <th scope="row"><label for="fbsection">Hide in Categories</label></th>
        <td>
          <?php echo $catdrop; ?>
        </td>
      </tr>

    </table>

    <p class="submit">
      <input type="submit" value="Save Changes" class="button-primary" name="Submit">
    </p>

  </form>
</div>
<?php
}

function fblikeviral_admin_scripts() {
  $fblikeviral_plugin_url = trailingslashit(get_bloginfo('wpurl')) . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__));

  wp_enqueue_script('jquery');
  wp_enqueue_script('color_js', $fblikeviral_plugin_url . '/color/jscolor.js', array('jquery'));
}

function fblikeviral_add_scripts() {
  if(get_option('fblikeviral_homepage') == 0 && (is_home() || is_front_page())) {
    return false;
  }
  
  if(get_option('fblikeviral_effect') == 'disabled') {
    return false;
  }
  
  $fblikeviral_plugin_url = trailingslashit(get_bloginfo('wpurl')) . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__));

  wp_enqueue_script('jquery');
  wp_enqueue_script('jquery_more', $fblikeviral_plugin_url . '/fblikeviral.js', array('jquery'));
}

function fblikeviral_start_script() {
  global $post;

  $plugin_url = trailingslashit(get_bloginfo('wpurl')) . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__));
?>
<link rel="stylesheet" type="text/css" href="<?php echo $plugin_url; ?>/fblikeviral.css" media="all" />
<?php
  $popup = get_post_meta($post->ID, '_fblikeviral_popup', true);
  
  if($popup == 1) {
    return false;
  }

  if(get_option('fblikeviral_homepage') == 0 && (is_home() || is_front_page())) {
    return false;
  }

  if(get_option('fblikeviral_effect') == 'disabled') {
    return false;
  }

  if(is_404()) {
    return false;
  }

  $cats  = explode(',', get_option('fblikeviral_box_cats'));
  $hides = explode(',', get_option('fblikeviral_hidebox'));

  if(is_array($hides) && !empty($hides)) {
    if(in_array('post', $hides) && is_single()) {
      return false;
    }

    if(in_array('page', $hides) && is_page()) {
      return false;
    }

    if(in_array('category', $hides) && is_category()) {
      return false;
    }

    if(in_array('archive', $hides) && is_archive()) {
      return false;
    }

    if(in_array('tags', $hides) && is_tag()) {
      return false;
    }

    if(in_array('author', $hides) && is_author()) {
      return false;
    }

    if(in_array('search', $hides) && is_search()) {
      return false;
    }
  }

  if(is_array($cats) && !empty($cats) && is_category($cats)) {
    return false;
  }

  switch(get_option('fblikeviral_url')) {
    case 'home':
    default:
      $url = home_url();
      break;
    case 'current':
      if(is_home() || is_front_page()) {
        $url = home_url();
      } else {
        $url = get_permalink();
      }
      break;
    case 'custom':
      $url = get_option('fblikeviral_custom_url');
      break;
  }

?>
<?php if(get_option('fblikeviral_show_facebook_send')): ?>
<div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
<?php endif; ?>
<?php if(get_option('fblikeviral_show_google_one')): ?>
<script type="text/javascript" src="http://apis.google.com/js/plusone.js"></script>
<?php endif; ?>
<script type="text/javascript">
var vconf = {
  effect: '<?php echo get_option('fblikeviral_effect'); ?>',
  fbsend: <?php if(get_option('fblikeviral_show_facebook_send')) { echo 'true'; } else { echo 'false'; } ?>,
  fblike: <?php if(get_option('fblikeviral_show_facebook_like')) { echo 'true'; } else { echo 'false'; } ?>,
  fbshare: <?php if(get_option('fblikeviral_show_facebook_share')) { echo 'true'; } else { echo 'false'; } ?>,
  twitter: <?php if(get_option('fblikeviral_show_twitter_share')) { echo 'true'; } else { echo 'false'; } ?>,
  tvia: '<?php echo get_option('fblikeviral_twitter_via'); ?>',
  google: <?php if(get_option('fblikeviral_show_google_one')) { echo 'true'; } else { echo 'false'; } ?>,
  scount: <?php if(get_option('fblikeviral_show_count')) { echo 'true'; } else { echo 'false'; } ?>,
  url: '<?php echo $url; ?>',
  message: '<?php echo str_replace(array("\n", "\r", "\t"), null, get_option('fblikeviral_message')); ?>',
  style_bg: '<?php echo get_option('fblikeviral_style_bg'); ?>',
  style_color: '<?php echo get_option('fblikeviral_style_color'); ?>',
  style_font: '<?php echo get_option('fblikeviral_style_font'); ?>',
  style_size: '<?php echo get_option('fblikeviral_style_size'); ?>',
  opacity: <?php echo ((get_option('fblikeviral_style_opacity')) ? get_option('fblikeviral_style_opacity') : 80); ?>,
  trigger: '<?php echo get_option('fblikeviral_trigger'); ?>',
  trigger_timeout: <?php echo ((get_option('fblikeviral_trigger_timeout')) ? get_option('fblikeviral_trigger_timeout') : 7); ?>,
  trigger_hits: <?php echo ((get_option('fblikeviral_trigger_hits')) ? get_option('fblikeviral_trigger_hits') : 5); ?>,
  popcountdown: <?php if(get_option('fblikeviral_countdown')) { echo 'true'; } else { echo 'false'; } ?>,
  timeout: <?php echo ((get_option('fblikeviral_timeout')) ? get_option('fblikeviral_timeout') : 15); ?>,
  vreturn: <?php echo get_option('fblikeviral_repeat'); ?>,
  powered: <?php echo (int)get_option('fblikeviral_powered'); ?>,
  afflink: '<?php echo get_option('fblikeviral_affiliate'); ?>',
  perc: 80,
  show: 1
};
</script>
<?php
}

function fblikeviral_content_buttons($content) {
  global $post;

  $op = get_option('fblikeviral_show_posts');
  
  if($op == '' || ! $op || $op == 'no') {
    return $content;
  }

  if(is_404()) {
    return $content;
  }

  $buttons = get_post_meta($post->ID, '_fblikeviral_buttons', true);

  if($buttons == 1) {
    return $content;
  }

  $cats  = explode(',', get_option('fblikeviral_content_cats'));
  $hides = explode(',', get_option('fblikeviral_hidesec'));
  
  if(is_array($hides) && !empty($hides)) {
    if(in_array('post', $hides) && is_single()) {
      return $content;
    }

    if(in_array('page', $hides) && is_page()) {
      return $content;
    }

    if(in_array('category', $hides) && is_category()) {
      return $content;
    }

    if(in_array('archive', $hides) && is_archive()) {
      return $content;
    }

    if(in_array('tags', $hides) && is_tag()) {
      return $content;
    }

    if(in_array('author', $hides) && is_author()) {
      return $content;
    }

    if(in_array('search', $hides) && is_search()) {
      return $content;
    }
  }

  if(is_array($cats) && !empty($cats) && is_category($cats)) {
    return $content;
  }

  switch(get_option('fblikeviral_content_url')) {
    case 'home':
    default:
      $link = home_url();
      break;
    case 'current':
      if(is_home() || is_front_page()) {
        $link = home_url();
      } else {
        $link = get_permalink();
      }
      break;
    case 'custom':
      $link = get_option('fblikeviral_content_custom_url');
      break;
  }

  $twitter = get_option('fblikeviral_twitter_via');
  $show_count = get_option('fblikeviral_content_count');

  if($show_count == 1) {
    $html = '<div style="clear: both;"></div><div>';

    if(get_option('fblikeviral_show_content_like'))
      $html .= '<div class="vr_action_web"><iframe src="http://www.facebook.com/plugins/like.php?href=' . $link . '&layout=box_count&show_faces=false&width=55&action=like&colorscheme=light&height=61" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:70px; height:61px;" allowTransparency="true"></iframe></div>';

    if(get_option('fblikeviral_show_content_one'))
      $html .= '<div class="vr_action_web"><g:plusone size="tall" href="' . $link . '" count="true"></g:plusone></div>';

    if(get_option('fblikeviral_show_content_share2'))
      $html .= '<div class="vr_action_web"><a href="http://twitter.com/share" class="twitter-share-button" data-url="' . $link . '" data-via="' . $twitter . '" data-count="vertical">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div>';

    if(get_option('fblikeviral_show_content_share'))
      $html .= '<div class="vr_action_web"><a name="fb_share" type="box_count" href="http://www.facebook.com/sharer.php" share_url="' . $link . '">Share</a><script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script></div>';

    if(get_option('fblikeviral_show_content_send'))
      $html .= '<div class="vr_action_web"><div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:send href="' . $link . '" font=""></fb:send></div>';

    $html .= '</div><div style="clear: both;"></div>';
  } else {
    $html = '<div style="clear: both;"></div><div>';

    if(get_option('fblikeviral_show_content_like'))
      $html .= '<div class="vr_action_web"><iframe src="http://www.facebook.com/plugins/like.php?href=' . $link . '&layout=standard&show_faces=false&width=55&action=like&colorscheme=light&height=61" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:70px; height:61px;" allowTransparency="true"></iframe></div>';

    if(get_option('fblikeviral_show_content_one'))
      $html .= '<div class="vr_action_web"><g:plusone href="' . $link . '" count="false"></g:plusone></div>';

    if(get_option('fblikeviral_show_content_share2'))
      $html .= '<div class="vr_action_web"><a href="http://twitter.com/share" class="twitter-share-button" data-url="' . $link . '" data-via="' . $twitter . '" data-count="none">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div>';

    if(get_option('fblikeviral_show_content_share'))
      $html .= '<div class="vr_action_web"><a name="fb_share" type="standard" href="http://www.facebook.com/sharer.php" share_url="' . $link . '">Share</a><script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script></div>';
      
    if(get_option('fblikeviral_show_content_send'))
      $html .= '<div class="vr_action_web"><div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:send href="' . $link . '" font=""></fb:send></div>';

    $html .= '</div><div style="clear: both;"></div>';
  }

  if($op == 'top') {
    return $html . $content;
  } elseif ($op == 'bottom') {
    return $content . $html;
  } elseif ($op == 'both') {
    return $html . $content . $html;
  } else {
    return $content;
  }
}

function fblikeviral_api_check($transient) {
  if( empty( $transient->checked ) )
      return $transient;

  $plugin_slug = plugin_basename( __FILE__ );

  $args = array(
      'action' => 'update-check',
      'plugin_name' => $plugin_slug,
      'version' => $transient->checked[$plugin_slug],
  );

  $response = fblikeviral_api_request( $args );

  if( false !== $response ) {
      $transient->response[$plugin_slug] = $response;
  }

  return $transient;
}

function fblikeviral_api_request($args) {
  $request = wp_remote_post('http://www.fblikeviral.com/wp_update_d3v/index.php', array( 'body' => $args ) );

  if( is_wp_error( $request )
  or
  wp_remote_retrieve_response_code( $request ) != 200
  ) {
      return false;
  }

  $response = unserialize( wp_remote_retrieve_body( $request ) );
  if( is_object( $response ) ) {
      return $response;
  } else {
      return false;
  }
}

function fblikeviral_api_info($false, $action, $args) {
  $plugin_slug = plugin_basename( __FILE__ );

  if( $args->slug != $plugin_slug ) {
      return false;
  }

  $args = array(
      'action' => 'plugin_information',
      'plugin_name' => $plugin_slug,
      'version' => $transient->checked[$plugin_slug],
  );

  $response = fblikeviral_api_request( $args );
  $request = wp_remote_post('http://www.fblikeviral.com/wp_update_d3v/index.php', array( 'body' => $args ) );

  return $response;
}

function fblikeviral_options() {
  return array(
    'show_facebook_send' => false,
    'show_facebook_like' => true,
    'show_facebook_share' => true,
    'show_twitter_share' => false,
    'show_google_one' => false,
    'show_count' => false,
    'show_posts' => 'no',
    'effect' => 'popup',
    'url' => 'current',
    'custom_url' => false,
    'homepage' => false,
    'trigger' => 'end',
    'trigger_timeout' => 0,
    'trigger_hits' => 3,
    'timeout' => 30,
    'message' => 'If you like this post, please share it with others!',
    'repeat' => 1,
    'style_bg' => 'FFFFFF',
    'style_color' => '000000',
    'style_font' => 'Arial',
    'style_size' => '14',
    'style_opacity' => '80',
    'powered' => 1,
    'affiliate' => '',
    'twitter_via' => '',
    'show_content_send' => false,
    'show_content_like' => true,
    'show_content_share' => true,
    'show_content_share2' => true,
    'show_content_one' => true,
    'content_url' => '',
    'content_custom_url' => '',
    'content_count' => 0,
    'content_cats' => array(),
    'hidesec' => array(),
    'hidebox' => array(),
    'box_cats' => array(),
    'countdown' => 0
  );
}


function fblikeviral_boxes() {
  add_meta_box('fblikeviral-meta', 'FB Like Viral', 'fblikeviral_meta', 'post', 'normal', 'high');
}

function fblikeviral_meta($post) {
  $popup   = get_post_meta($post->ID, '_fblikeviral_popup', true);
  $buttons = get_post_meta($post->ID, '_fblikeviral_buttons', true);
  
?>
  <input type="hidden" name="fblikeviral_noncename" id="fblikeviral_noncename" value="<?php echo wp_create_nonce('fblikeviral_' . $post->ID);?>" />
  <p>
    <label><input type="checkbox" name="fblikeviral_popup" value="1"<?php if($popup): ?> checked="checked"<?php endif; ?> /> Disable Popup</label><br />
    <label><input type="checkbox" name="fblikeviral_buttons" value="1"<?php if($buttons): ?> checked="checked"<?php endif; ?> /> Disable Buttons in Content</label><br />
  </p>
<?php
}

function fblikeviral_save_post($post_id) {
	if (! wp_verify_nonce($_POST['fblikeviral_noncename'], 'fblikeviral_' . $post_id)) {
		return $post_id;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return $post_id;

	if (! current_user_can('edit_post', $post_id))
		return $post_id;

  if (isset($_POST['fblikeviral_popup']) && $_POST['fblikeviral_popup'] == 1) {
    update_post_meta($post_id, '_fblikeviral_popup', true);
  } else {
    delete_post_meta($post_id, '_fblikeviral_popup');
  }

  if (isset($_POST['fblikeviral_buttons']) && $_POST['fblikeviral_buttons'] == 1) {
    update_post_meta($post_id, '_fblikeviral_buttons', true);
  } else {
    delete_post_meta($post_id, '_fblikeviral_buttons');
  }
}

add_action('admin_menu', 'fblikeviral_add_panel');
add_action('template_redirect', 'fblikeviral_add_scripts');
add_action('admin_print_scripts', 'fblikeviral_admin_scripts');
add_action('wp_head', 'fblikeviral_start_script');
add_action('add_meta_boxes', 'fblikeviral_boxes');
add_action('save_post', 'fblikeviral_save_post');
add_filter('pre_set_site_transient_update_plugins', 'fblikeviral_api_check');
add_filter('site_transient_update_plugins', 'fblikeviral_api_check');
add_filter('plugins_api', 'fblikeviral_api_info', 10, 3);
add_filter('the_content', 'fblikeviral_content_buttons');

?>
