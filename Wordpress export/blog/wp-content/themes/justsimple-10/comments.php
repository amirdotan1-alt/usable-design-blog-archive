<?php // Do not delete these lines
	if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('נא לא לקשר ישירות לעמוד זה. תודה!');

        if (!empty($post->post_password)) { // if there's a password
            if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
				?>
				
				<p class="nocomments"><?php _e("פוסט זה מוגן בסיסמה. יש להכניס סיסמה כדי לצפות בתגובות."); ?><p>
				
				<?php
				return;
            }
        }

		/* This variable is for alternating comment background */
		$oddcomment = 'alt';
?>

<!-- You can start editing here. -->

<?php if ($comments) : ?>
	<h3 id="comments"><?php comments_number('אין תגובות', 'תגובה אחת', '% תגובות' );?> על &#8220;<?php the_title(); ?>&#8221;</h3> 

	<ol class="commentlist">
	<?php $commentcounter = 0; ?>
	<?php foreach ($comments as $comment) : ?>
		<?php $commentcounter++; ?>
		<li class="<?php echo $oddcomment; ?>" id="comment-<?php comment_ID() ?>">
			<div class="cmtinfo">
				<em><?php comment_date('d/m/Y') ?> בשעה <?php comment_time() ?></em>
				<span style="direction: rtl;text-align:right;float: right;">
					<small class="commentmetadata" style="float: right;">
					<a href="#comment-<?php comment_ID() ?>" title="">
					<span class="commentnum"><?php echo $commentcounter; ?></span></a>
					</small><cite><?php comment_author_link() ?></cite>
				</span>
			</div>
			
			
			<?php if ($comment->comment_approved == '0') : ?>
			<em>תגובתך ממתינה לאישור.</em><br />
			<?php endif; ?>			
			<?php comment_text() ?><?php edit_comment_link('עריכה','',''); ?>			
		</li>

	<?php /* Changes every other comment to a different class */	
		if ('alt' == $oddcomment) $oddcomment = '';
		else $oddcomment = 'alt';
	?>

	<?php endforeach; /* end for each comment */ ?>

	</ol>

 <?php else : // this is displayed if there are no comments so far ?>

  <?php if ('open' == $post-> comment_status) : ?> 
		<!-- If comments are open, but there are no comments. -->
		
	 <?php else : // comments are closed ?>
		<!-- If comments are closed. -->
		<p class="nocomments">אפשרות הוספת התגובות סגורה.</p>
		
	<?php endif; ?>
<?php endif; ?>
<div class="post-content">
<p>
<?php if ($post->ping_status == "open") { ?>
	<a href="<?php trackback_url(display); ?>">טראקבק לפוסט זה</a> | 
<?php } ?>
<?php if ($post-> comment_status == "open") {?>
	<?php comments_rss_link('RSS לתגובות לפוסט זה'); ?>
<?php }; ?>
</p>
</div>

<?php if ('open' == $post-> comment_status) : ?>

<h3 id="respond">הוספת תגובה</h3>

<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
<p>עליך <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php the_permalink(); ?>">להכנס לחשבונך</a> כדי להוסיף תגובה.</p>
<?php else : ?>

<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

<?php if ( $user_ID ) : ?>

<p>שלום <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="<?php _e('התנתקות מחשבון זה') ?>">יציאה &raquo;</a></p>

<?php else : ?>

<p><input class="textbox" type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
<label for="author"><small>שם <?php if ($req) _e('(חובה)'); ?></small></label></p>

<p><input class="textbox" type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
<label for="email"><small>אימייל (לא יוצג באתר) <?php if ($req) _e('(חובה)'); ?></small></label></p>

<p><input class="textbox" type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
<label for="url"><small>אתר</small></label></p>

<?php endif; ?>

<!--<p><small><strong>XHTML:</strong> You can use these tags: <?php echo allowed_tags(); ?></small></p>-->

<p><textarea name="comment" id="comment" cols="100%" rows="10" tabindex="4"></textarea></p>

<p><input name="submit" type="submit" id="submit" tabindex="5" value="שליחת התגובה" />
<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
</p>
<?php do_action('comment_form', $post->ID); ?>

</form>

<?php endif; // If registration required and not logged in ?>

<?php endif; // if you delete this the sky will fall on your head ?>