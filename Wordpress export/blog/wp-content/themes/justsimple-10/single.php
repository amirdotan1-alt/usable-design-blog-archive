<?php get_header()?>
	<div id="content">
		<div id="main">
			<?php if ($posts) : foreach ($posts as $post) : start_wp(); ?>
			<div class="post">
				<h2 class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="קישור קבוע: <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<span class="postdate"><?php the_time('d/m/Y') ?> </span>
				
				
				<div class="fb-share-list" style='padding-bottom:8px;padding-top:8px;'><script src='http://static.ak.fbcdn.net/connect.php/js/FB.Share' type='text/javascript'></script><a name='fb_share' rel='nofollow' share_url='<?php echo get_permalink($post->ID); ?>'  style='width:57px;height:57px;padding-right:0px;padding-left:2px;' type='button_count'></a></div>

				
				<div class="post-content">
				
					<?php the_excerpt(); ?> 
					<?php the_content(); ?>
					<?php wp_link_pages(); ?>
					
					<span class="postdate">פורסם במדור <?php the_category(', ')?></span>
										
					<div class="fb-share-list" style='padding-bottom:8px;padding-top:8px;'><script src='http://static.ak.fbcdn.net/connect.php/js/FB.Share' type='text/javascript'></script><a name='fb_share' rel='nofollow' share_url='<?php echo get_permalink($post->ID); ?>'  style='width:57px;height:57px;padding-right:0px;padding-left:2px;' type='button_count'></a></div>
					

<!--
<h4 style="color:grey;"> מאמרים אקראיים שפורסמו כאן בעבר:</h4>
<div style="list-style:none;">
<?php cypher_previousposts(3,6); ?>
</div>
-->

					
					

					<!--
					<?php trackback_rdf(); ?>
					-->
				</div>
				<?php comments_template(); ?>
			</div>
			<?php endforeach; else: ?>
			<p><?php _e('לא נמצאו תכנים.'); ?></p>
			<?php  endif; ?>
			<p align="center"><?php posts_nav_link() ?></p>
			
			

			
			
		</div>
	</div>
<?php get_sidebar();?>
<?php get_footer();?>