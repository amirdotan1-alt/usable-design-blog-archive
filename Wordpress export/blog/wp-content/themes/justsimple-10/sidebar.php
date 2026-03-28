<div id="sidebar">
<ul>
<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar() ) : else : ?>
	<li>
  
  			<img src="http://www.amirdotan.com/blog/blog_images/me2.jpg">
			<h2>אמיר דותן</h2> 
<ul>
חי בלונדון מאז שנת 2001. עובד כארכיטקט חוויית משתמש בחברת הייעוץ <a href="http://lab49.com/">Lab49</a> המפתחת יישומים לשוק ההון <a href="http://www.slideshare.net/Amir_D/tag/presentation">ומרצה בכנסים</a>. בעבר <a href="http://www.city.ac.uk/informatics/school-organisation/centre-for-human-computer-interaction-design/research">חוקר באוניברסיטה</a>, <a href="http://london.sae.edu/en-gb/course/3487/BABSc_%28Hons%29_Web_Development">מרצה לתואר ראשון</a> ומתכנת. בעל תואר ראשון במולטימדיה ותואר שני באינטראקציית אדם-מחשב. מעדכן בטוויטר <a href="https://twitter.com/UX_links">קישורים בנושא חוויית משתמש</a> וקישורים בנושא <a href="https://twitter.com/ux_for_finance">חוויית משתמש במגזר הפיננסי</a>. 
<br />
<small>amirdotan1 [at] gmail.com</small>
</ul>
				
  
  
  </li>	
  
  <li>
<h2>&#1511;&#1489;&#1500;&#1514; &#1506;&#1491;&#1499;&#1493;&#1504;&#1497;&#1501;</h2>
				<ul>
<li><a href="https://www.facebook.com/pages/%D7%A2%D7%99%D7%A6%D7%95%D7%91-%D7%A9%D7%9E%D7%99%D7%A9/207217645991511">באמצעות פייסבוק</a></li>					

<li><a href="http://www.amirdotan.com/blog/maillist.php" target="blank">&#1489;&#1488;&#1502;&#1510;&#1506;&#1493;&#1514; &#1488;&#1497;&#1502;&#1497;&#1497;&#1500;</a></li>
					<li><a href="<?php bloginfo('rss2_url'); ?>">&#1489;&#1488;&#1502;&#1510;&#1506;&#1493;&#1514; RSS (&#1502;&#1488;&#1502;&#1512;&#1497;&#1501;)</a></li>
					<li><a href="<?php bloginfo_rss('comments_rss2_url'); ?>">&#1489;&#1488;&#1502;&#1510;&#1506;&#1493;&#1514; RSS (&#1514;&#1490;&#1493;&#1489;&#1493;&#1514;)</a></li>
				</ul>
  
  
  </li>
  
  
  <li>
    <h2><?php _e('מדורים'); ?></h2>
    <ul>
      <?php list_cats(0, '', 'name', 'asc', '', 1, 0, 1, 1, 1, 1, 0,'','','','','')  ?>
    </ul>
  </li>
  
  

							<li><h2><?php _e('&#1514;&#1490;&#1493;&#1489;&#1493;&#1514; &#1488;&#1495;&#1512;&#1493;&#1504;&#1493;&#1514;'); ?></h2><small>(סה"כ: <?php if (function_exists('commstats')) { commstats(); } ?>)</small>
							<ul>
							<?php get_recent_comments(); ?>
							</ul>
							</li>
							
							
			<li><h2>מעוררי דיון</h2>

			<ul><?php get_mostcommented(); ?></ul></li>
  
  
  	<li><h2>ארכיון</h2>
				<select name="archive-dropdown" onChange='document.location.href=this.options[this.selectedIndex].value;'>
<option value=""><?php echo attribute_escape(__('Select Month')); ?></option>
<?php wp_get_archives('type=monthly&format=option&show_post_count=1'); ?> </select>
			</li>
  
  
  
  
  
  
  
  
  
  
  
<?php if(is_home()) {?>
  <?php get_links_list(); ?>    

  <?php }?>
  <?php endif; ?>
</ul>

</div>
<!-- CLOSE sidebar-->
<div class="clear"></div>