<?php
/*
Plugin Name: Previous Posts
Plugin URI: http://www.cypherhackz.net/plugins-themes/previous-posts/
Description: Returns a list of your previous posts. You can set how many posts to be displayed.
Version: 1.2
Author: Fauzi Mohd Darus
Author URI: http://www.cypherhackz.net
*/

function cypher_previousposts($min = 10, $max = 15) {

	//You can edit the value
	$before = '<li>';	//Text to insert before the title
	$after = '</li>';	//Text to insert after the title

	global $wpdb;
	$bil;
	$output = '';

	$request = "SELECT ID, post_title, post_date FROM $wpdb->posts WHERE post_status ='publish'";
	$posts = $wpdb->get_results($request);
	shuffle($posts);

	if($posts){
		foreach ($posts as $post){
			$post_title = stripslashes($post->post_title);
			$post_date = stripslashes($post->post_date);
			$permalink = get_permalink($post->ID);
			$bil++;
		
			if($bil > $min && $bil < ($max + 1)){
			//$output .= $before . $post_date . $after;
			$output .= $before . '<a href="' . $permalink . '" rel="Bookmark" title="Permanent Link to ' . $post_title .'">' 
			. $post_title . '</a>';
			$output .= $after;
			}
		}
	}

	if($bil < ($min)){
	$output .= 'There is no previous posts';
	}
	echo $output;
}
?>
