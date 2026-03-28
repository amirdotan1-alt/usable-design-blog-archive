<?php get_header()?>
	<div id="content">
		<div id="main">
			<div class="post">
<?php
	global $wp_query;
	$curauth = $wp_query->get_queried_object();
?>
<h2>אודות: <?php echo $curauth->nickname; ?></h2>

<strong>שם מלא</strong>
<p><?php echo $curauth->first_name. ' ' . $curauth->last_name ;?></p>
<strong>אתר</strong>
<p><a href="<?php echo $curauth->user_url; ?>"><?php echo $curauth->user_url; ?></a></p>
<strong>פרטים</strong>
<p><?php echo $curauth->description; ?></p>


			<h2>פוסטים מאת <?php echo $curauth->nickname; ?>:</h2>
			<ul class="authorposts">
			<!-- The Loop -->
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<li>
				<a href="<?php the_permalink() ?>" rel="bookmark" title="קישור קבוע: <?php the_title(); ?>"><?php the_title(); ?></a> [<?php the_time('d בM Y'); ?>]
			</li>
			<?php endwhile; else: ?>
			<p><?php _e('לא נמצאו פוסטים למחבר זה.'); ?></p>

			<?php endif; ?>
			<!-- End Loop -->			
		</ul>
		<p align="center"><?php posts_nav_link() ?></p>
	</div>
		</div>
	</div>
<?php get_sidebar();?>
<?php get_footer();?>