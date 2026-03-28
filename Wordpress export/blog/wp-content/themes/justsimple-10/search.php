<?php get_header()?>
	<div id="content">
		<div id="main">
		<?php if (have_posts()) : ?>
		<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
		<?php if (is_search()) { ?>
		
		
		<h2 class="pagetitle">
		<?
		if($wp_query->found_posts == 1){
			echo 'נמצאה רק תוצאת חיפוש אחת עבור  '. ' <strong>'.$s.'</strong>. לא נורא. זה עדיף מכלום.';
		}else{
			echo 'נמצאו ' . $wp_query->found_posts . ' תוצאות חיפוש עבור'. ' <strong>'.$s.'</strong>';
		}
		?>
		</h2>
		
		
		
		
		<!--
			<h2 class="pagetitle">תוצאות חיפוש עבור '<?php echo $s; ?>'</h2>
		-->
			
			
		<?php } ?>
		<?php while (have_posts()) : the_post(); ?>
				
			<div class="post">
				<h2 class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="קישור קבוע: <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<span class="postdate"><?php the_time('d/m/Y') ?> <!-- by <?php the_author() ?> --></span>
				
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
	
		<?php endwhile; ?>

		<p align="center"><?php posts_nav_link(' - ','&#171; הקודם','הבא &#187;') ?></p>
		
	<?php else : ?>

		<h2 class="pagetitle">לא נמצאה אף תוצאת חיפוש עבור <?php echo ' <strong>'.$s.'</strong> :( '; ?></h2>
	<?php endif; ?>

		</div>
	</div>
<?php get_sidebar();?>
<?php get_footer();?>