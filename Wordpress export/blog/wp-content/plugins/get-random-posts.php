<?php
   /*
   Plugin Name: Get Random Posts
   Plugin URI: http://www.texthelden.de/index.php/2007/01/15/wordpress-plugin-get-random-posts-from-database/
   Description: Generates a set of links to random post in your wp database 
   Version: 0.1
   Author: Felix Bosseler
   Author URI: http://www.texthelden.de
   */
   
   function get_random_posts($before="<li>",$after="</li>",$numPosts="5")
   {
      global $wpdb; // Global wordpress variables
		
      // Get random posts from the $categoryId
      $postSQL =  "SELECT 
			$wpdb->posts.ID, 
			$wpdb->posts.post_title";
		
  		$postSQL	.=	" FROM $wpdb->posts WHERE $wpdb->posts.post_status = 'publish'";
		
		
  		//	Order the posts randomly
		  $postSQL	.=	" ORDER BY RAND() LIMIT $numPosts";
		
		  //	Get the results
		  $articles	=	$wpdb->get_results($postSQL);
		
		  //	Show the results
		  $result  =  "";
		
  		if ( $articles ) {
	  		//	Display the post titles with permalinks
			  foreach ( $articles as $display ) {
				  //	Format the title
				  $listTitle	=	stripslashes(str_replace('"', '', $display->post_title));
				
				 //	Set up the return string
				  if ( $before ) $result	.=	$before;
				  $result  .=	'<a href="' . post_permalink($display->ID) . '" rel="bookmark" title="Permanent link &quot;' . $listTitle . '&quot;">' . $listTitle . '</a>';
				$result	.=	$after;
			}
		
		} else {
			//	There were no posts in the category
			$result	.=	"<p>Keine passenden Artikel gefunden</p>\n";
		}
		
		return  $result;
   }
?>
