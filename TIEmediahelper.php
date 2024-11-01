<?php

/*
Plugin Name: TIEmediahelper
Plugin URI: http://www.setupmyvps.com/tiemediahelper/
Description: Finds leftover files on disk which are not listed in the Media Library and adds them as unattached media.
Version: 1.0
Author: TIEro
Author URI: http://www.setupmyvps.com
License: GPL2
*/

// Add actions to define scheduled job and place settings menu on the Dashboard.
add_action('admin_menu', 'TIEmediahelper_settings_page');

// Define the Settings page function for options.
function TIEmediahelper_settings_page() {
  add_management_page('Media Helper', 'Media Helper', 'administrator', 'TIEmediahelper_settings', 'TIEmediahelper_option_settings');
}

// The function that kicks off the actual work.
function TIEmediahelper_find_media($optional_subdirectory) {
	global $wpdb;
	global $media_library_files;
	global $media_library_meta;
	$mediapath = wp_upload_dir();
	$pathstart = $mediapath['basedir'] . '/' . $optional_subdirectory;

//	$media_url = str_replace($mediapath['basedir'],$mediapath['baseurl'],$media_location);
//	$media_filename = basename($media_location);
	
	// Tell the user what's up. Remember kicking off the function refreshes the page, so redo the title.
	echo   '<div class="wrap">
			<h2><img src="' . plugins_url( 'mediahelper.png' , __FILE__ ) . '" border=0 alt="Media Helper" style="vertical-align:middle"> Media Helper</h2>';

	if (is_dir($pathstart) == false) {
		echo '<p><strong>That folder does not exist.</strong><br>';
		$redirector = admin_url() . 'tools.php?page=TIEmediahelper_settings';
		echo '<p><a href="' . $redirector . '">Go back to Media Helper page.</a>';
	}
	else {
		echo '<p><strong>Building list of existing media from database.</strong>';
		$get_all_media = "SELECT distinct guid
							FROM $wpdb->posts
							WHERE post_status = 'inherit'
							AND guid is not null";
		$media_library_files = $wpdb->get_col($get_all_media,0);
		foreach($media_library_files as &$item) {
			$item = basename($item);
		}
		
		$get_all_media = "SELECT meta_value as file_value
							FROM $wpdb->postmeta
							WHERE meta_key IN ('_wp_attached_file', '_wp_attachment_metadata')";
		$media_library_meta = $wpdb->get_col($get_all_media,0);
		foreach($media_library_meta as &$item) {
			$item = $item . '';
		}		
		
		// Start the search for new files to add.
		echo '<br><strong>Starting media search</strong>
			  <br>';
			
		// Call function to find files and send them for checking.
		// If your php version can't handle the RecursiveDirectoryIterator, you'll have to use
		// the old-style function instead. To do that, comment out the following line of code:

		TIEmediahelper_directory_scan($pathstart);

		// Then uncomment the following line of code:
	
			// TIEmediahelper_directory_scan_oldstyle($pathstart, true);
			
		// Job complete, notify user and provide link to Media Library.
		$redirector = admin_url() . 'upload.php?detached=1';
		echo '<p><strong>Job complete.</strong>
			<p><a href="' . $redirector . '">Go to unattached media in the library</a>';
	}
}	

// Get all files and call the function to check if they are in the library.
function TIEmediahelper_directory_scan($pathstart) {
	$it = new RecursiveDirectoryIterator($pathstart);
	foreach(new RecursiveIteratorIterator($it) as $file) {
		if ($file->hasChildren == false) {
			TIEmediahelper_check_file($file);
		}
	}
}

// Get all files and call the function to check if they are in the library.
// Oldstyle version built around code from http://dzone.com/snippets/get-recursive-directory, with thanks.
function TIEmediahelper_directory_scan_oldstyle($directory, $recursive) {
    if ($handle = opendir($directory)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                if (is_dir($directory. "/" . $file)) {
                    if($recursive) {
						echo '<br><em>Checking folder ' . $file . '</em>';
                        TIEmediahelper_directory_scan($directory. "/" . $file, $recursive);
                    }
                } else {
                    $file = $directory . "/" . $file;
                    $file_to_check = preg_replace("/\/\//si", "/", $file);
					TIEmediahelper_check_file($file_to_check);
                }
            }
        }
        closedir($handle);
    }
}

// The function that does all the real work. Checks library, creates new attachments where needed.
function TIEmediahelper_check_file($media_location) {	
	global $media_library_files;
	global $media_library_meta;
	$mediapath = wp_upload_dir();

	// Check whether $media_location is in the list of files created earlier.
	// If it's not there, create an attachment record and notify the user.
	$media_url = str_replace($mediapath['basedir'],$mediapath['baseurl'],$media_location);
	$media_filename = basename($media_location);
	$friendly_url = str_replace($mediapath['basedir'],'',$media_location);

	if ($media_filename != '.' && $media_filename != '..') {
		if (in_array($media_filename, $media_library_files) == false) {
			if (is_null(key(preg_grep('#'.preg_quote($media_filename).'#i', $media_library_meta))) == true) {
				echo '<br>Creating Media Library entry for ' . $friendly_url . '<br>';
				$wp_filetype = wp_check_filetype(basename($media_location), null );
				$attachment = array(
					'guid' => $media_url,
					'post_mime_type' => $wp_filetype['type'],
					'post_title' => basename($media_location),
					'post_content' =>'',
					'post_status' => 'inherit');
				$new_post_id = wp_insert_attachment($attachment, $media_location, 0);
				require_once(ABSPATH . 'wp-admin/includes/image.php');
				$attach_data = wp_generate_attachment_metadata( $new_post_id, $media_location );
				wp_update_attachment_metadata( $new_post_id, $attach_data );
			}
		}
		else {
			echo '.';
		}
	}
}
	
// Code for the options page on the Dashboard.
function TIEmediahelper_option_settings() {

	if (isset($_POST['textfield'])) {
		TIEmediahelper_find_media($_POST['textfield']);
		return;
	}

	// Get the base uploads URL for display and remove the domain name part.
	$sitepath = site_url();
	$mediapath = wp_upload_dir();
	$showpath = str_replace($sitepath . '/','',$mediapath['baseurl']) . '/';
	
	// The header section line of the options page, with the logo and basic info.
	$html= '</pre><div class="wrap">
			<div style="width:60%">
			<h2><img src="' . plugins_url( 'mediahelper.png' , __FILE__ ) . '" border=0 alt="Media Helper" style="vertical-align:middle"> Media Helper</h2>
			<p>This plugin will search your wp-uploads folder (and sub-folders) to find files which are not included in the Media Library. 
			It will create a new "unattached media" entry for each file it finds and also generate meta data where appropriate.
			<p>The plugin creates entries in real time, so if it gets interrupted, you can just run it again. It will not create duplicate entries.
			<p>Please bear in mind that the plugin may sit for a long time before appearing to do anything when it handles thousands of files.
			Do not click the button again: something will happen eventually.
			<p> The plugin may fail to function on very large directories. If this happens to you, try running it on individual sub-directories.
			<p>To start the process, press the button.
			<p>
			<form name="form1" method="post" action="">
			<label>Start search in: ' . $showpath . '<input type="text" name="textfield" id="textfield"></label>
			<p><label><input type="image" src="' . plugins_url( 'start_button.png' , __FILE__ ) . '" name="button" id="button" value="Submit"></label>
			</form>
			</div></div>
			<div style="clear:both">
			<pre>';

	// Display the HTML. Yes, it's a roundabout way of doing things, but it makes adding new stuff easier in future.
	echo $html;
}
