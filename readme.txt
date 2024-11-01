=== TIEmediahelper Media Library Tools ===
Contributors: TIEro
Donate link: http://www.setupmyvps.com/tiemediahelper/
Tags: media, unattached, delete, mass, automatic, automated, images, files, pictures, missing, clean, find unused files, find unused images, leftover images, leftover files, delete leftover files, delete unused files
Requires at least: 3.0.1
Tested up to: 4.0
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Find leftover files stored in the WP uploads directory tree which are missing from the Media Library.

== Description ==

A simple plugin to find files which are stored in the WP uploads folder (and sub-folders) but which have no corresponding entry in the Media Library. 

The plugin creates a new unattached media record for each file, plus metadata where appropriate. You can then delete leftover files through the normal WordPress Media Library UI.

== Installation ==

1. Upload the plugin folder and its contents to the /wp-content/plugins directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Run the search from the Media Helper option in the Tools menu.

Alternatively, use the built-in 'Add New' option on the Plugins menu to install.

== Frequently Asked Questions ==

= Does it work on multisite? =

Absolutely no idea. It was never designed for that, so I'd honestly be surprised if it does.

= The plugin sits there for ages and doesn't seem to be doing anything. =

It's working, honest. The problem is that the file search is quite intensive, especially if you have thousands of files in the search path. Kick it off and leave it running... it'll get there eventually. In testing, the plugin ran successfully on sites with over 10,000 files. Patience, grasshopper. :)

= The plugin sits there for ages, then gives me a 404. =

This can happen if you have a ridiculous number of files in a directory and a wussy server. You can try running it on individual sub-folders (by entering the path on the options page) but if you have tens of thousands of files in a single folder, you'll probably have to work around it by temporarily shuffling them around.

= The plugin timed out. =

This can happen if you have a lot of files - you'll get the standard "Maximum execution time of 30 seconds exceeded" error message if your server's php settings are normal. Don't worry. Just hit back and start the process again: it'll whizz through the ones it's done and carry on from where it left off.

= Does the plugin have any limitations? =

Yes. This plugin may not find a duplicate copy of a file in a different folder. For example, if you have a file called "image.jpg" in your media library from 11/2013, the plugin will not add a record for a file called "image.jpg" in the wp-uploads/2013/10 folder. 

This is because of the ridiculously messy attachments setup for large images which means that multiple attachment records are created, each with multiple metadata records. It didn't seem worth the effort to try to untangle such a mess just to catch a duplicate filename or two.

= Will the plugin attach images to their posts? =

No. It has no way of knowing which posts they were associated with, if they ever were.

= Is this plugin actively maintained? =

Yes, it is. I would LOVE to hear your comments, see your reviews and listen to what you'd like added or changed. You can do that here on WP.org or at http://www.setupmyvps.com/tiemediahelper.

= Does the plugin take a long time to run? =

That will depend very much on how many images you have in your uploads folders. The plugin gives a simple running commentary, so at least you know it's working and can see what it's doing.

= Is there any documentation? =

You're reading it. The plugin code is also heavily commented to help you find your way. You can visit the plugin homepage at http://setupmyvps.com/tiemediahelper for thoughts and comments

== Changelog ==

= 1.0 =
Original working release.
