=== Better Extended Live Archive ===
Contributors: Charles
Donate link: http://sexywp.com/archives
Tags: template tags, archive, post, archives, BELA, ELA,
Requires at least: 2.6
Tested up to: 3.5.1
Stable tag: 0.80

Extended Live Archive(ELA) is a very cool WordPress plugin, which can generate a clean, structured archive page with fantastic visual effect.

== Description ==

Extended Live Archive (ELA) is a cool AJAX application with which you can display a well-structured and multi-viewed archive navigator on your archive page. With the help of this navigator, visitors of your blog can surf all the articles on your blog easily. They can sort titles of your articles by date, by category or by tag. [Here](http://sexywp.com/archives "The author's site") is a LIVE DEMO.

To install the plugin,

1. Upload the `better-extended-live-archive` directory and its content to your `wp-content/plugins/` directory.
2. Make sure the `cache` directory permission are set to 0777 (refer to your webhost knowledge-base if need be).
3. Place `<?php af_ela_super_archive(); ?>` in your archive page template. Generally it is named archives.php NOT the archive.php.
4. Then, visit the Settings->Ext. Live Archive page once to initialize it.

= Some History =

The development of the original ELA stopped in June 22nd, 2006. The final version is 0.10 beta R18, which is for WordPress older than version 2.3. Because of the changes of WP database structure, the original ELA did not work in WordPress 2.3 or later. Nevertheless, bloggers can not stop loving it. Many warm hearted programmers fixed this plugin again and again. Till now, you can still find a few working versions of ELA for even WordPress 2.7 or later (This project is also one of them). Of course, this kind of searching work is not that easy.

Although there are many patches of ELA, none of them make it better. All the patches you can find are to fix ELA under a certain WordPress version. The aim of this project is to make ELA stronger, faster and easier to use. And this page will exist until Google Company is closed down.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the `better-extended-live-archive` directory and its content to your `wp-content/plugins/` directory.
1. Make sure the `cache` directory permission are set to 0777 (refer to your webhost knowledge-base if need be).
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php af_ela_super_archive(); ?>` in your archive page template. Generally it is named archives.php NOT the archive.php.
1. NEW(2010-6-20): ShortCode supported, insert'[extended-live-archive]' to your post, then that post will show ELA.
1. Create a page based on the "Archives" page template.
1. Then, visit the Settings->Ext. Live Archive page once to initialize it.

== Frequently Asked Questions ==

= Nothing here now =

none.

== Screenshots ==

1. The effect of archive by date.
2. by category

== Changelog ==
= 0.80 =
* Remove the version check in the admin page, you can use WP Plugin directory now.
* Exclude categories feature was not working, now it has been fixed.
* Error when tag list generating fixed.
* Post new bug fixed.
* SQL query optimized.
* Cache update functions optimized.

= 0.70 =
* A SQL query bug fixed when new comment posted.
* Speed optimization, 75% queries removed when first time generating the cache.

= 0.60 =
* Make the ajax js file do not include the wp.

= 0.50 =
* A tiny change: short code supported.

= 0.40 =
* Fixed another path problem.
* Readme file changed.


== Upgrade Notice ==
= 0.70 =
* Bugs fixed and speed optimized. Upgrade recommended.

= 0.60 =
* If you use WP 3.0, you should try this.

= 0.50 =
* You don't have to upgrade if it is work good for you.
