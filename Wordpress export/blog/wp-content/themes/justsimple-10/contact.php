<?php
/*
Template Name: Contact
*/
$cp_question = "2+2 = ?";
$cp_answer = "4";
?>
<?php get_header(); ?>
	<div id="content">
	<div id="main">
 		   
    	<?php if (have_posts()) : ?>
			<?php while (have_posts()) : the_post(); ?>
				<div class="post">
				<?php
					//validate email adress
					function is_valid_email($email)
					{
  						return (eregi ("^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,4}$", $email));
					}
					function is_valid_user($answer)
					{
						global $cp_answer;
						if ($answer == $cp_answer) { return true; } else { return false;}
					}
					//clean up text
					function clean($text)
					{
						return stripslashes($text);
					}
					//encode special chars (in name and subject)
					function encodeMailHeader ($string, $charset = 'UTF-8')
					{
    					return sprintf ('=?%s?B?%s?=', strtoupper ($charset),base64_encode ($string));
					}

					$cp_name    = (!empty($_POST['cp_name']))    ? $_POST['cp_name']    : "";
					$cp_email   = (!empty($_POST['cp_email']))   ? $_POST['cp_email']   : "";
					$cp_url     = (!empty($_POST['cp_url']))     ? $_POST['cp_url']     : "";
					$cp_ans     = (!empty($_POST['cp_ans']))     ? $_POST['cp_ans']     : "";
					$cp_message = (!empty($_POST['cp_message'])) ? $_POST['cp_message'] : "";
					$cp_message = clean($cp_message);
					$error_msg = "";
					$send = 0;
					if (!empty($_POST['submit'])) {			
						$send = 1;
						if (empty($cp_name) || empty($cp_email) || empty($cp_message) || empty($cp_ans)) {
							$error_msg.= "<p style='color:#a00'><strong>בבקשה מלא את כל השדות הנדרשים.</strong></p>\n";
							$send = 0;							
						}						
						if (!is_valid_email($cp_email)) {
							$error_msg.= "<p style='color:#a00'><strong>כתובת האימייל שהכנסת אינה חוקית.</strong></p>\n";
							$send = 0;
						}	
						if (!is_valid_user($cp_ans)) {
							$error_msg.= "<p style='color:#a00'><strong>התשובה שהזנת עבור מנגנון האנטי-ספאם שגויה.</strong></p>\n";
							$send = 0;
						}									
					}
					if (!$send) { ?>
						<h2 class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="קישור קבוע אל: <?php the_title(); ?>"><?php the_title(); ?></a></h2>
						<div class="post-content">
							<?php the_content("המשך קריאה &#187;");
							?>
							<p class="post-info">* - שדות חובה</p>
							<?php echo $error_msg;?>
							<form method="post" action="<?php echo "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" id="contactform">
              
								<fieldset>
                  					<strong>שם</strong>*<br/>
									<input type="text" class="textbox" id="cp_name" name="cp_name" value="<?php echo $cp_name ;?>" /><br/><br/>
									<strong>אימייל</strong>*<br/>
									<input type="text" class="textbox" id="cp_email" name="cp_email" value="<?php echo $cp_email ;?>" /><br/><br/>
									<strong>אתר</strong><br/>
									<input type="text" class="textbox" id="cp_url" name="cp_url" value="<?php echo $cp_url ;?>" /><br/><br/>
									<strong>הגנה מספאם:<?php echo $cp_question; ?> </strong>*<br/>
									<input type="text" class="textbox" id="cp_ans" name="cp_ans" value="<?php echo $cp_ans ;?>" /><p class="post-info">[כדי להוכיח שאתה אנושי]</p><br/>
									<strong>הודעה</strong>*<br/>				
									<textarea id="cp_message" name="cp_message" value="<?php echo $cp_message ;?>"></textarea><br/>
									<input type="submit" id="submit" name="submit" value="שליחת ההודעה" />		
								</fieldset>
							</form>
						</div>
					<?php
					} else {
						$displayName_array	= explode(" ",$cp_name);
						$displayName = htmlentities(utf8_decode($displayName_array[0]));
			
						$header  = "MIME-Version: 1.0\n";
						$header .= "Content-Type: text/plain; charset=\"utf-8\"\n";
						$header .= "From:" . encodeMailHeader($cp_name) . "<" . $cp_email . ">\n";
						$email_subject	= "[" . get_settings('blogname') . "] " . encodeMailHeader($cp_name);
						$email_text		= "מאת......: " . $cp_name . "\n" .
							  "אימייל.....: " . $cp_email . "\n" .
							  "אתר.......: " . $cp_url . "\n\n" .
							  $cp_message;

						if (@mail(get_settings('admin_email'), $email_subject, $email_text, $header)) {
							echo "<h2>שלום " . $displayName . ",</h2><p>תודה עבור הודעתך! אצור עמך קשר בהקדם.</p>";
						}
					}
					?>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
</div>
</div>
    <?php get_sidebar();?>
<?php get_footer(); ?>