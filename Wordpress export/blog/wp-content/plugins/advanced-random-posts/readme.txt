=== Advanced Random Posts ===
Contributors: yakuphan
Tags: Random, Posts, Random Posts, Category, Category posts
Donate link: http://www.yakupgovler.com/?p=416
Requires at least: 2.8
Tested up to: 2.8.4
Stable tag: 2.3

Display random posts from selected categories or current category or all posts with thumbnail images (optional).

== Description ==
Advanced Random Posts Widget displays your posts by selecting randomly with thumbnail images (optional). It gets posts from selected categories or current category or all posts. When your visitors are at home, it gets posts from all posts or selected category. If you set 'Get posts from current category', when visitors see single post, widget lists posts in the same category of single post or when visitors click a category link, it gets posts from current category.

Notice: Version 2.2+ requires at least 2.8.

= Supported Languages =
* English
* Turkish
* Russian - Thanks <a href="http://www.fatcow.com">Fatcow </a>

== Installation ==

= Installation =
1. Make sure you are running WordPress version 2.8 or better. It won't work with older versions. If you have older versions, please download v1.1
2. Download the zip file and extract the contents.
3. Upload the 'advanced-random-posts' folder (wp-content/plugins/).
4. Activate the plugin through the 'plugins' page in WP.
5. See 'Appearance'->'Widgets' to place it on your sidebar. Set the settings.

== Frequently Asked Questions ==

= How can I set it to get posts from current category? =
Select checkbox on widget's settings called 'Get posts from current category'.

= I want to display only the posts in two categories. =
You have to write their category's ids -seperated with a comma- to 'Categories' textbox.

= I don't use Widgets. How can use this widget? =

Please, visit my [website](http://www.yakupgovler.com/?p=416).

== Screenshots ==

1. Widget's screenshot in 'Appearance'->'Widgets'

== Options ==

Widget's options allow you to change your random posts list displaying.

= Title: =
Your random posts widget's title on your sidebar.

= Number of posts to show: =
How many posts to display

= Excerpt length (letters) =
You know that

= Thumbnail Custom Field Name =
If you want to display the thumbnail of your posts via a custom field, write its name.

= Height - Width =
Images size.

= Get first image of post =
If you don't want to use custom field, plugin will get first image from your post content.

= Get first attached image of post =
Plugin gets first attached image of post.

= Default image =
If post has no image, plugin display this image. Ex: http://www.yakupgovler.com/default-image.png


Notice: If you use three options, plugin uses custom field image firstly. If the post has no custom field, it gets first image from content. At last it gets first attached image. I suggest not to use "Get first image of post" for performance. It queries much more.

= Categories =
Plugin gets posts in these categories. (Category IDs, separated by commas.)


= Get posts from current category: =
Posts will be get from current category (single post's category or current category).


== Changelog ==
= 2.3 =
* Fixed a bug. If you don't set image dimensions, it displays thumbnail wrong.

= 2.2 =
* Added Russian language support

= 2.1 =
* Fixed a bug. When you check 'Get posts from current category' and you are in a single post, widget calls an error. Thanks [E.Ali](http://www.alivesitesi.com/).

= 2.0 =
* Added thumbnail image support
* Added multi-language support
* Rewriten by using WordPress 2.8 Widget Class API

= 1.1 =
* Bug fixed, showing the same post id.

= Version 1.0 =
* Initial release version.
