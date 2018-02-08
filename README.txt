=== Generate Thumbnail ===
Contributors: themeegg
Tags: ajax, thumbnail, regenerate, admin, image, photo
Requires at least: 2.8
Tested up to: 4.9.4
Stable tag: 1.0

Generate Thumbnail allows you to generate all thumbnails at once without script timeouts on your server.

== Description ==

Generate Thumbnail allows you to generate all thumbnails on your site. There are already some plugins available for this, but they have one thing in common: All thumbnails are regenerate in a single step. This works fine when you don’t have that many photos on your site. When you have a lot of full-size photos, the script on the server side takes a long time to run. Unfortunately the time a script is allowed to run is limited, which sets an upper limit to the number of thumbnails you can regenerate. This number depends on the server configuration and the computing power your server has available. When you get over this limit, you won’t be able to generate your thumbnails.

Why would you want to generate your thumbnails? Wordpress allows you to change the size of thumbnails. This way, you can make the size of thumbnails fit the design of your website. When you change the size to fit for a new theme, all future photos you are going to upload will have this new size. Your old thumbnails won’t be resized. That’s where this plugin comes into action. After changing the image sizes, you can generate all thumbnails. But instead of telling the server to recreate all thumbnails at once, they are generate one after another. Generateing thumbnails for one photo won’t take all too long, so you won’t run into any script timeouts. Note that you still have to wait until all thumbnails have been rebuilt. If you close the page before the task is completed, you have to start all over again.

You can also select the thumbnail sizes you want to generate, so that you don't need to recreate all images if you've just changed one thumbnail-size. You can also choose to only generate post thumbnails (featured images).

This plugin requires JavaScript to be enabled.

== Installation ==

Upload the plugin to your blog, activate it, done. You can then generate all thumbnails in the tools section (Tools -> Regenerate Thumbnails).

== Changelog ==

= 1.0 =

* Initial release