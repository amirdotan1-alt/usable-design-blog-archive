<?php get_header()?>
	<div id="content">
		<div id="main">
					
	<?php if ($posts) {
		$AsideId = get_settings('justsimple_asideid');
		function stupid_hack($str)
		{
			return preg_replace('|</ul>\s*<ul class="asides">|', '', $str);
		}
		ob_start('stupid_hack');
		foreach($posts as $post)
		{
			start_wp();
	?>
	<?php if ( in_category($AsideId) && !is_single() ) : ?>
		<ul class="asides">
			<li id="p<?php the_ID(); ?>">
				<?php echo wptexturize($post->post_content); ?>							
				<br/>
				<?php comments_popup_link('(0)', '(1)','(%)')?>  | <a href="<?php the_permalink(); ?>" title="קישור קבוע: <?php echo wptexturize(strip_tags(stripslashes($post->post_title), '')); ?>" rel="bookmark">#</a> <?php edit_post_link('(edit)'); ?>
			</li>						
		</ul>
	<?php else: // If it's a regular post or a permalink page ?>
		<div class="post">
		
			<h2 class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="קישור קבוע <?php the_title(); ?>"><?php the_title(); ?></a></h2>
			<span class="postdate"><?php the_time('d/m/Y') ?> <!-- by <?php the_author() ?> --></span>
			
							
<div class="fb-share-list" style='padding-bottom:8px;padding-top:8px;'><script src='http://static.ak.fbcdn.net/connect.php/js/FB.Share' type='text/javascript'></script><a name='fb_share' rel='nofollow' share_url='<?php echo get_permalink($post->ID); ?>'  style='width:57px;height:57px;padding-right:0px;padding-left:2px;' type='button_count'></a></div>

			
			
			
			<div class="post-content">
				<?php the_excerpt();	 ?>
				<a href="<?php the_permalink() ?>" >להמשך המאמר..</a>
				<?php wp_link_pages(); ?>		
				<p class="post-info">
				
							פורסם במדור <?php the_category(', ') ?> <strong>|</strong> <?php edit_post_link('לערוך','','<strong>|</strong>'); ?>  <?php comments_popup_link('אין תגובות &#187;', 'תגובה אחת &#187;', '% תגובות &#187;'); ?></p> 
					
				</p>
				<!--
				<?php trackback_rdf(); ?>
				-->
			</div>
			<?php comments_template(); ?>
		</div>
	<?php endif; // end if in category ?>
	<?php
		}
	} // end if (posts)
	else
		{
			echo '<p>לא נמצאו תכנים.</p>';
		}
	?>
		<p align="center"><?php posts_nav_link() ?></p>
		</div>
	</div>
<?php get_sidebar();?>
<?php get_footer();?>