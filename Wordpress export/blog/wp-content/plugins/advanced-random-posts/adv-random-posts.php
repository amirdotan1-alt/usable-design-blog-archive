<?php
/*
Plugin Name: Advanced Random Posts
Plugin URI: http://www.yakupgovler.com/?p=416
Description: Display random posts from selected categories or current category or all posts with thumbnail images (optional).
Version: 2.3
Author: Yakup GÖVLER
Author URI: http://www.yakupgovler.com
*/

class yg_adv_random_posts extends WP_Widget {
	function yg_adv_random_posts() {
	 //Load Language
	 load_plugin_textdomain( 'adv-rnd-posts', false, dirname(plugin_basename(__FILE__)) .  '/lang' );
	 $widget_ops = array('description' => __('Shows Random Posts. You can customize it easily.', 'adv-rnd-posts') );
	 //Create widget
	 $this->WP_Widget('advancedrandomposts', __('Advanced Random Posts', 'adv-rnd-posts'), $widget_ops);
	}

  function widget($args, $instance) {
	 		extract($args, EXTR_SKIP);
			echo $before_widget;
			$title = empty($instance['title']) ? __('Random Posts', 'adv-rnd-posts') : apply_filters('widget_title', $instance['title']);
			$parameters = array(
			  'title' => $title,
				'limit' => (int) $instance['show-num'],
				'excerpt' => (int) $instance['excerpt-length'],
				'actcat' => (bool) $instance['actcat'],
				'cats' => esc_attr($instance['cats']),
				'cusfield' => esc_attr($instance['cus-field']),
				'w' => (int) $instance['width'],
				'h' => (int) $instance['height'],
				'firstimage' => (bool) $instance['firstimage'],
				'atimage' =>(bool) $instance['atimage'],
				'defimage' => esc_url($instance['defimage'])
			);

			if ( !empty( $title ) ) {
		    echo $before_title . $title . $after_title;
			};
        //print random posts
				yg_randomposts($parameters);
			echo $after_widget;
  } //end of widget
	
	//Update widget options
  function update($new_instance, $old_instance) {

		$instance = $old_instance;
		//get old variables
		$instance['title'] = esc_attr($new_instance['title']);
		$instance['show-num'] = (int) abs($new_instance['show-num']);
		if ($instance['show-num'] > 20) $instance['show-num'] = 20;
		$instance['excerpt-length'] = (int) abs($new_instance['excerpt-length']);
		$instance['cats'] = esc_attr($new_instance['cats']);
		$instance['actcat'] = $new_instance['actcat'] ? 1 : 0;
	  $instance['cus-field'] = esc_attr($new_instance['cus-field']);
		$instance['width'] = esc_attr($new_instance['width']);
		$instance['height'] = esc_attr($new_instance['height']);
		$instance['firstimage'] = $new_instance['first-image'] ? 1 : 0;
		$instance['atimage'] = $new_instance['atimage'] ? 1 : 0;
		$instance['defimage'] = esc_url($new_instance['def-image']);
		return $instance;
  } //end of update
	
	//Widget options form
  function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => __('Advanced Random Posts','adv-rnd-posts'), 'show-num' => 10, 'excerpt-length' => 0, 'actcat' => 0, 'cats' => '', 'cus-field' => '', 'width' => '', 'height' => '', 'firstimage' => 0, 'atimage' => 0,'defimage'=>'' ) );
		
		$title = esc_attr($instance['title']);
		$show_num = (int) $instance['show-num'];
		$excerpt_length = (int) $instance['excerpt-length'];
		$cats = esc_attr($instance['cats']);
		$actcat = (bool) $instance['actcat'];
		$cus_field = esc_attr($instance['cus-field']);
		$width = esc_attr($instance['width']);
		$height = esc_attr($instance['height']);
		$firstimage = (bool) $instance['firstimage'];
		$atimage = (bool) $instance['atimage'];
		$defimage = esc_url($instance['defimage']);

		?>
		<p>
		   <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:');?> 
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		   </label>
		</p>
		<p>
		   <label for="<?php echo $this->get_field_id('show-num'); ?>"><?php _e('Number of posts to show:');?> 
		  <input id="<?php echo $this->get_field_id('show-num'); ?>" name="<?php echo $this->get_field_name('show-num'); ?>" type="text" value="<?php echo $show_num; ?>" size ="3" /><br />
			<small><?php _e('(at most 20)','adv-rnd-posts'); ?></small>
		  </label>
	  </p>
		<p>
		  <label for="<?php echo $this->get_field_id('excerpt-length'); ?>"><?php _e('Excerpt length (letters):', 'adv-rnd-posts');?> 
		  <input id="<?php echo $this->get_field_id('excerpt-length'); ?>" name="<?php echo $this->get_field_name('excerpt-length'); ?>" type="text" value="<?php echo $excerpt_length; ?>" size ="3" /><br />
			<small>(<?php _e('0 - Don\'t show excerpt', 'adv-rnd-posts');?>)</small>
		  </label>
	  </p>
		<p>
		  <label for="<?php echo $this->get_field_id('cus-field'); ?>"><?php _e('Thumbnail Custom Field Name:', 'adv-rnd-posts');?> 
		  <input id="<?php echo $this->get_field_id('cus-field'); ?>" name="<?php echo $this->get_field_name('cus-field'); ?>" type="text" value="<?php echo $cus_field; ?>" size ="20" /> 
		  </label><br />
		  <label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width:', 'adv-rnd-posts');?> <input id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width; ?>" size ="3" /></label>px<br />
			<label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height:', 'adv-rnd-posts');?> <input id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $height; ?>" size ="3" /></label>px
	  </p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('first-image'); ?>" name="<?php echo $this->get_field_name('first-image'); ?>"<?php checked( $firstimage ); ?> />
			<label for="<?php echo $this->get_field_id('first-image'); ?>"><?php _e('Get first image of post', 'adv-rnd-posts');?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('atimage'); ?>" name="<?php echo $this->get_field_name('atimage'); ?>"<?php checked( $atimage ); ?> />
			<label for="<?php echo $this->get_field_id('atimage'); ?>"><?php _e('Get first attached image of post', 'adv-rnd-posts');?></label>
		</p>
	  <p>
		  <label for="<?php echo $this->get_field_id('def-image'); ?>"><?php _e('Default image:', 'adv-rnd-posts');?> 
		  <input class="widefat" id="<?php echo $this->get_field_id('def-image'); ?>" name="<?php echo $this->get_field_name('def-image'); ?>" type="text" value="<?php echo $defimage; ?>" /><br />
			<small>(<?php _e('if there is no thumbnail, use this', 'adv-rnd-posts');?></small>
		  </label>
	  </p>	
	  <p>
		  <label for="<?php echo $this->get_field_id('cats'); ?>"><?php _e('Categories:', 'adv-rnd-posts');?> 
		  <input class="widefat" id="<?php echo $this->get_field_id('cats'); ?>" name="<?php echo $this->get_field_name('cats'); ?>" type="text" value="<?php echo $cats; ?>" /><br />
			<small>(<?php _e('Category IDs, separated by commas.', 'adv-rnd-posts');?>)</small>
		  </label>
	  </p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('actcat'); ?>" name="<?php echo $this->get_field_name('actcat'); ?>"<?php checked( $actcat ); ?> />
			<label for="<?php echo $this->get_field_id('actcat'); ?>"> <?php _e('Get posts from current category', 'adv-rnd-posts');?></label>
		</p>
   <?php
  } //end of form
}

add_action( 'widgets_init', create_function('', 'return register_widget("yg_adv_random_posts");') );
//Register Widget

 // Show random posts function
 function yg_randomposts($args = '') {
  global $wpdb;
	$defaults = array('limit' => 10, 'excerpt' => 0, 'actcat' => 0, 'cats'=>'', 'cusfield' =>'', 'w' => 48, 'h' => 48, 'firstimage' => 0, 'atimage' => 0, 'defimage' => '');
	$args = wp_parse_args( $args, $defaults );
	extract($args);
	
	$limit = (int) abs($limit);
	$firstimage = (bool) $firstimage;
	$atimage = (bool) $atimage;
	$defimage = esc_url($defimage);
	$w = (int) $w;
	$h = (int) $h;
	
	$excerptlength = (int) abs($excerpt);
	$excerpt = '';
	$cats = str_replace(" ", "", esc_attr($cats));
	if (($limit < 1 ) || ($limit > 20)) $limit = 10;
	
	if (($actcat) && (is_category())) {
	 $cats = get_query_var('cat');
	}
	if (($actcat) && (is_single())) {
	 $cats = '';
	 foreach (get_the_category() as $catt) {
	   $cats .= $catt->cat_ID.' '; 
	 }
	 $cats = str_replace(" ", ",", trim($cats));
	}
	
	if (!intval($cats)) $cats='';
	$query = "cat=$cats&showposts=$limit&orderby=rand";
	$rnd_posts = get_posts($query); //get posts by random
	$postlist = '';
	$height = $h ? ' height = "' . $h .'"' : '';
	$width = $w ? ' width = "' . $w . '"' : '';
    foreach ($rnd_posts as $post) {
		  $post_title = htmlspecialchars(stripslashes($post->post_title));
			if ($excerptlength) {
				$excerpt = $post->post_excerpt;
				if ( '' == $excerpt ) {
					$text = $post->post_content;
					$text = strip_shortcodes( $text );
					$text = str_replace(']]>', ']]&gt;', $text);
					$text = strip_tags($text);
					$excerpt_length = 100;
					$words = explode(' ', $text, $excerpt_length + 1);
					if (count($words) > $excerpt_length) {
						array_pop($words);
						$text = implode(' ', $words);
					}
					$excerpt = $text;
				}
				
			  if(strlen($excerpt) > $excerptlength) {
				 $excerpt = mb_substr($excerpt, 0, $excerptlength) . '...';
				}
				$excerpt = ': ' . $excerpt;
			}
			$image = '';
			$img = '';
			if ($cusfield) {
			 $cusfield = esc_attr($cusfield);
			 $img = get_post_meta($post->ID, $cusfield, true);
			}

			 if (!$img && $firstimage) {
			   $match_count = preg_match_all("/<img[^']*?src=\"([^']*?)\"[^']*?>/", $post->post_content, $match_array, PREG_PATTERN_ORDER);		
			   $img = $match_array[1][0];
			 }
		   if (!$img && $atimage) { 
				 $p = array(
				  'post_type' => 'attachment',
				  'post_mime_type' => 'image',
				  'numberposts' => 1,
				  'order' => 'ASC',
				  'orderby' => 'menu_order ID',
				  'post_status' => null,
				  'post_parent' => $post->ID
				 );
				 $attachments = get_posts($p);
				 if ($attachments) {
				   $imgsrc = wp_get_attachment_image_src($attachments[0]->ID, 'thumbnail');
				   $img = $imgsrc[0];
				 }
				}
				 
			 if (!$img && $defimage)
			  $img = $defimage;
				 
			 if ($img)
        $image = '<img src="' . $img . '" title="' . $post_title . '" class="random-posts-thumb" ' . $width . $height . ' />';

				
      $postlist .= "<li><a href=\"" . get_permalink($post->ID) . "\" title=\"". $post_title ."\" >$image" . $post_title ."</a>$excerpt</li>\n";
    }
		echo '<ul class="advanced-random-posts">';		
    echo $postlist;
		echo '</ul>';
 }
 
?>