<?php get_header()?>
	<div id="content">
		<div id="main">
		<?php if (have_posts()) : ?>
	<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
    
    <?php /* If this is a category archive */ if (is_category()) { ?>				
		<h2 class="pagetitle">ארכיון קטגוריית '<?php echo single_cat_title(); ?>'</h2>
		
 	  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<h2 class="pagetitle">ארכיון לתאריך <?php the_time('j בF, Y'); ?></h2>
		
	 <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h2 class="pagetitle">ארכיון לחודש <?php the_time('F, Y'); ?></h2>

		<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h2 class="pagetitle">ארכיון לשנת <?php the_time('Y'); ?></h2>
		
	  <?php /* If this is a search */ } elseif (is_search()) { ?>
		<h2 class="pagetitle">תוצאות חיפוש</h2>
		
	  <?php /* If this is an author archive */ } elseif (is_author()) { ?>
		<h2 class="pagetitle">ארכיון המחבר</h2>

		<?php /* If this is a paged archive */ } elseif (is_paged()) { ?>
		<h2 class="pagetitle">ארכיון</h2>

		<?php } ?>
		<?php while (have_posts()) : the_post(); ?>
				
			<div class="post">
				<h2 class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="קישור קבוע אל: <?php the_title(); ?>"><?php the_title(); ?></a></h2>
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

		<h2 class="center">לא נמצא</h2>
		<p class="center">מה שחיפשת איננו כאן.</p>
	<?php endif; ?>

		</div>
	</div>
<?php get_sidebar();?>
<?php get_footer();?>