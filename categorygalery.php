<?php
/*
Plugin Name: Category Gallery
Plugin URI: www.ermanseneren.com/categorygallery/
Description: Prepare WP categorized image galleries including more than 500 photos with preloader techique without decreasing initial page loading performance.  
Version: Version (1.0)
Author: Erman Şeneren
Author URI: www.ermanseneren.com
License: GNU
*/
if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
 {
 die('You are not allowed to call this page directly.');
}

register_activation_hook(__FILE__, 'catgal_register');
function catgal_register( ) {
 add_option('catgal_interval', '4000');
 add_option('catgal_otoplay', 'true');
 add_option('catgal_enableTitle', 'true');
 add_option('catgal_enableControls', 'true');
}

register_deactivation_hook(__FILE__, 'catgal_unregister');
function catgal_unregister( ) {
  delete_option('catgal_interval');
  delete_option('catgal_otoplay');
  delete_option('catgal_enableTitle');
  delete_option('catgal_enableControls');
}

function catgal_yonetim()
{
	 add_options_page( 'Category Gallery', 'Category Gallery', 'manage_options', 'Category_Gallery', 'categorygallery_fonks' ); 
	};
add_action('admin_menu', 'catgal_yonetim');

add_shortcode('CATGAL', 'show_catgal_01');

function show_catgal_01($atts){
	
	$cat_id = $atts['cat_id'];
	wp_register_style( 'Category Gallery CSS', plugins_url( 'css/kGallery.css', __FILE__ ) );
	wp_enqueue_style( 'Category Gallery CSS' );
	
	
	wp_register_script('Category Gallery JS',plugins_url( 'js/kGallery-full.min.js', __FILE__ ));
	wp_enqueue_script("Category Gallery JS");
    $innerHTMLcatGal = "
    <style>
        #gallery-wrapper{
            height: 500px; width: 100%; text-align: center; overflow:hidden;
		}
		.kSlideshowWrapper, .kSlideshowItemDiv{
				width: 98%;
				height: 400px;
				overflow:hidden; 
		}
		.kThumbnailsInnerWrapper{
				height: 60px;
				margin-left:10px; 
				margin-right:10px;
				margin-top:5px;
				margin-bottom:5px;
				
				
		}
		.kThumbnailsWrapper{
				margin-left:10px; 
				margin-right:10px;
				height: 95px;
				padding-top:5px;
				margin-bottom:0px;
				
		}
		.kThumbnailsPage{
				margin-left:10px; 
				margin-right:10px;
				text-align: left;
				height:80px;
				overflow:hidden;
				
		}
		.kThumbnailsPage img{
				padding: 2px;
				border: 1px solid #c2c2c2;
				margin-right: 5px;
				height:50px;
				
		}
		.kThumbnailsPage .selectedThumbnail{
				border:#999999 2px solid;
				
		}
		.kSlideshowItemDiv img{
				z-index: 400;
				padding:5px;
		}
    </style>
    <h2 id=\"galeriBaslik\" style=\"margin:10px;\">&nbsp;</h2><hr style=\"border:#D0D0D0 1px solid;\" /><br/>
    <div id=\"gallery-wrapper\">
     <script>
	 cg_enableTitle = ".get_option('catgal_enableTitle').";
	 cg_interval = ".get_option('catgal_interval').";
	 cg_autoPlay = ".get_option('catgal_otoplay').";
	 cg_enableControls = ".get_option('catgal_enableControls').";
	 
	 window.onload = function() {
		var $ = jQuery;
     	eval( ".categoryImages($cat_id)." );
		if('".get_category($cat_id)->name."' != '') $('#galeriBaslik').html('".get_category($cat_id)->name."');
		$(\".kSlideshowTitle\").hide(0);
	 };
     </script>
    </div>

	";
	return $innerHTMLcatGal;
}

function categoryImages($catID) {
	global $wpdb;
	$query0 = "SELECT object_id FROM {$wpdb->term_relationships} where term_taxonomy_id=$catID";
	$object_ids = $wpdb->get_results($query0);
	
	$glDataSource = "gallery = kGallery({
       				 wrapper: '#gallery-wrapper',
       				 startItem: 0,
        			 dataType: 'array',
					 dataSource: [";
	foreach( $object_ids as $objectid ) {
		$img_info =  wp_get_attachment( $objectid->object_id );
		if($ilkKayit==1){$glDataSource = $glDataSource.",";}
		$ilkKayit = 1;
		$glDataSource = $glDataSource."{ \"large\":\"".wp_get_attachment_url( $objectid->object_id )."\", \"thumb\":\"".wp_get_attachment_thumb_url( $objectid->object_id )."\", \"title\":\"".$img_info['title']."\" }";
	};
	$glDataSource = $glDataSource."]})";
	
	return $glDataSource;
	
}
 
 function wp_get_attachment( $attachment_id ) {

	$attachment = get_post( $attachment_id );
	return array(
		'alt' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
		'caption' => $attachment->post_excerpt,
		'description' => $attachment->post_content,
		'href' => get_permalink( $attachment->ID ),
		'src' => $attachment->guid,
		'title' => $attachment->post_title
	);
}

function categorygallery_fonks() {
	$khata = 0;
	$khata_desc = '';
	if ($_POST['gizli'] == 'asdq2wdasf3r') {
	$catgal_interval = sanitize_text_field($_POST['catgal_interval']);
	$catgal_otoplay = sanitize_text_field($_POST['catgal_otoplay']);
	$catgal_enableTitle = sanitize_text_field($_POST['catgal_enableTitle']);
	$catgal_enableControls = sanitize_text_field($_POST['catgal_enableControls']);
	
	if(!is_numeric($catgal_interval)){$khata = 1; $khata_desc = 'Image Refresh Time should be numeric! e.g. 4000';};
	$catgal_interval = (int)$catgal_interval;
	if($khata != 1 && $catgal_interval<1){$khata = 1; $khata_desc = 'Image Refresh Time shoud be above 0';};
	if($catgal_otoplay=='true' && $catgal_otoplay=='false'){$khata = 2; $khata_desc = 'Auto Play should be Enable/Disable e.g. Enable';};
	if(($catgal_enableTitle=='true' && $catgal_enableTitle=='false')){$khata = 3; $khata_desc = 'Enable Title should be Enable/Disable e.g. Enable';};
	if($catgal_enableControls=='true' && $catgal_enableControls=='false'){$khata = 4; $khata_desc = 'Enable Controls should be Enable/Disable e.g. Enable';};
	
	if($khata==0){
		update_option('catgal_interval', $catgal_interval);
		update_option('catgal_otoplay', $catgal_otoplay);
		update_option('catgal_enableTitle', $catgal_enableTitle);
		update_option('catgal_enableControls', $catgal_enableControls);
		?>
		<div class="updated"><p><strong><?php _e('Options saved.'); ?></strong></p></div>
		<?php
	} else {
	?>
    	<div class="error"><p><strong><?php _e('Options not saved!'); ?></strong></p><?php _e($khata_desc); ?><br />&nbsp;
    <?php
	};
}

?> </div>
<style>
	#tabs ul { margin:0px; }
	#tabs ul li {float:left; width:200px; border:#C3C0C0 1px solid; background-color:#FFF8F8; height:20px;padding:15px; font-weight:bold; cursor:pointer;}
	.secilitab { background-color:#DCDBDB !important;}
	#tabs div {float:none; padding:10px; border-top:#c2c2c2 1px solid;}
</style>
<div style="margin-top:10px;">
<h2>Category Gallery Management</h2>

<div id="tabs">
  <ul>
    <li id="tabli1" onClick="tabch(0);" class="secilitab">Default Settings</li>
    <li id="tabli2" onClick="tabch(1);">Short Code Generator</li>
  </ul><br /><br /><br /><br />
  <div id="tabs1">
    <form method="post" action="">
	<table border="0" cellspacing="0" cellpadding="0">
	<tbody>
	<tr><td><label for="catgal_interval">Image Refresh Time (ms)</label></td><td> &nbsp; : <input type="text" id="catgal_interval" name="catgal_interval" value="<?php echo get_option('catgal_interval'); ?>" /></td></tr>
	<tr><td><label for="catgal_otoplay">Auto Play</label></td><td> &nbsp; : <select id="catgal_otoplay" name="catgal_otoplay"><option <?php if(get_option('catgal_otoplay')=="true") echo "selected"  ?> value="true">Enable</option><option <?php if(get_option('catgal_otoplay')!="true") echo "selected"  ?> value="false">Disable</option></select></td></tr>
	<tr><td><label for="catgal_enableTitle">Enable Title</label></td><td> &nbsp; : <select id="catgal_enableTitle" name="catgal_enableTitle"><option <?php if(get_option('catgal_enableTitle')=="true") echo "selected"  ?> value="true">Enable</option><option <?php if(get_option('catgal_enableTitle')!="true") echo "selected"  ?> value="false">Disable</option></select></td></tr>
	<tr><td><label for="catgal_enableControls">Enable Controls</label></td><td> &nbsp; : <select id="catgal_enableControls" name="catgal_enableControls"><option <?php if(get_option('catgal_enableControls')=="true") echo "selected"  ?> value="true">Enable</option><option <?php if(get_option('catgal_enableControls')!="true") echo "selected"  ?> value="false">Disable</option></select></td></tr>
	</tbody></table>
	<input type="hidden" id="gizli" name="gizli" value="asdq2wdasf3r"/><br /><br />
	<input type="submit" id="submit" name="submit" value="<?php _e('Save Changes'); ?>">
 </form>
  </div>
  </div>
  <div id="tabs2">
  <?php
  $categories=get_categories('type=attachment'); 
		if  ($categories) {
  ?>
    <select id="taxon" onChange="kisakodolustur($(this));">
    	<option>Choose</option>
        <?php
		
		  foreach ($categories  as $categoriesl ) {
			echo ('<option value="'.$categoriesl->term_id.'">'.$categoriesl->name.'</option>');
		  }
		
    ?>
    </select><label for="taxon">Choose your media category for your gallery, shortcode will be generated automatically. </label><br />
    <input type="text" value="" id="codeshort" /><label id="codetext" for="codeshort">&nbsp;</label>
    <?php
	} 
		else{
			echo ('<span style="color:red;">You do not have any categorized media.</span>');
		}
		
		if (!is_plugin_active( 'wp-media-category-management/wp-media-category-management.php' ) ) {
			$plugin_name = 'WP-Media-Category-Management';
$install_link = '<a href="' . esc_url( network_admin_url('plugin-install.php?tab=plugin-information&plugin=' . $plugin_name . '&TB_iframe=true&width=600&height=550' ) ) . '" class="thickbox" title="More info about ' . $plugin_name . '">Install ' . $plugin_name . '</a>';
			echo ('<div style="padding:10px; border:#0000ff 1px solid; background-color:#00ffff; margin:20px;">You can experince WP-Media-Category plugin that is tested to organize media categories.<br />'.$install_link.'</div>');
		}
	?>
    <script>
	document.getElementById('codeshort').value='';
	function kisakodolustur(objects){
		$('#codeshort').val('[CATGAL cat_id='+ objects.val() +']');
		$('#codetext').html('Please copy the generated code and paste it to your content.');
	}
    	
    </script>
  </div>
</div>
  <script>
    var $ = jQuery;
	$( "#tabs2" ).hide();
	function tabch(tabnum){
		switch(tabnum){
		case 0:
			$( "#tabs2" ).hide();
			$( "#tabli2" ).removeClass('secilitab');
			$( "#tabs1" ).show();
			$( "#tabli1" ).addClass('secilitab');
		break;
		case 1:
			$( "#tabs1" ).hide();
			$( "#tabli1" ).removeClass('secilitab');
			$( "#tabs2" ).show();
			$( "#tabli2" ).addClass('secilitab');
		break;
		}
	}
  </script>
</div>
<?php } ?>