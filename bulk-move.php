<?php
/**
 * Bulk Move
 *
 * Helper page for Zenphoto, enabling the bulk move of images based on search criteria
 *
 * @author Marcus Wong (wongm)
 * @package plugins
 */

$plugin_description = gettext("Helper page for Zenphoto, enabling the bulk move of images based on search criteria.");
$plugin_author = "Marcus Wong (wongm)";
$plugin_version = '1.0.0'; 
$plugin_URL = "https://github.com/wongm/zenphoto-bulk-move/";
$plugin_is_filter = 500 | ADMIN_PLUGIN;
$plugin_disable = !extensionEnabled('photostream') ? gettext('<em>photostream</em> plugin is required.') : false;

zp_register_filter('admin_utilities_buttons', 'bulkMove::button');

class bulkMove {
	
	static function button($buttons) {
		$buttons[] = array(
						'category'		 => gettext('Admin'),
						'enable'			 => true,
						'button_text'	 => gettext('Bulk move'),
						'formname'		 => 'zenphotoCaption_button',
						'action'			 => WEBPATH.'/plugins/bulk-move',
						'icon'				 => 'images/pencil.png',
						'title'				 => gettext('Bulk move images in your gallery.'),
						'alt'					 => '',
						'hidden'			 => '',
						'rights'			 => ALBUM_RIGHTS
		);
		return $buttons;
	}
}
?>