<?php get_header()?>
	<div id="content">
		<div id="main">
			<?php if ($posts) : foreach ($posts as $post) : start_wp(); ?>
			<div class="post">
				<h2 class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="קישור קבוע <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<div class="post-content">
					<?php the_content(); ?>
					<?php wp_link_pages(); ?>				
					<?php $sub_pages = wp_list_pages( 'sort_column=menu_order&depth=1&title_li=&echo=0&child_of=' . $id );?>
					<?php if ($sub_pages <> "" ){?>
						<p class="post-info">צפה גם בדפי הבן האלו.</p>
						<ul><?php echo $sub_pages; ?></ul>
					<?php }?>	
					<p class="post-info">
					<!--
						<?php the_author_posts_link() ?> :: <?php the_time('d.M.Y'); ?> :: 
						<?php the_category(', ') ?> <?php edit_post_link(); ?> :: 
					-->	
						
						<?php comments_popup_link('אין תגובות &#187;', 'תגובה אחת &#187;', '% תגובות &#187;'); ?>
					</p>
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