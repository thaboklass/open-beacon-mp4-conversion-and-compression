<?php

/*------------------------------------------------------------------*
 * The 'ob_mp4_cc_upload' class
/*------------------------------------------------------------------*/

if(!class_exists('ob_mp4_cc_upload')) :

    class ob_mp4_cc_upload {
	// Taxonomy name for custom taxonomy metabox
	public $ob_mp4_cc_taxonomy;
	
	// Taxonomy id for custom taxonomy metabox
	public $ob_mp4_cc_taxonomy_metabox_id;
	
	// Post type text for custom taxonomy metabox
	public $ob_mp4_cc_post_type;
	
	
	/**
	* The 'ob_mp4_cc_upload' constructor
	* 
	*/
	
	public function __construct() {
	    
	    // Start the static function to initialize the custom post type
	    // and custom taxonomy
	    self::ob_mp4_cc_post_type_and_taxonomy_init();
	    
	    // The video concomp and caption metaboxes
	    $this->ob_mp4_cc_video_metaboxes();
	    
	    $this->ob_mp4_cc_taxonomy = 'ob_mp4_cc_category';
	    $this->ob_mp4_cc_taxonomy_metabox_id = 'ob_mp4_cc_categorydiv';
	    $this->ob_mp4_cc_post_type = 'ob_mp4_c_and_c';
	    
	    // Remove old taxonomy meta box  
	    add_action( 'admin_menu', array($this, 'ob_mp4_cc_remove_meta_box'));
	    
	    // Add new screenshot meta box  
	    add_action('add_meta_boxes', array($this, 'ob_mp4_cc_add_screenshot_meta_box'));
	    
	    // Add new taxonomy meta box  
	    add_action('add_meta_boxes', array($this, 'ob_mp4_cc_add_taxonomy_meta_box'));
	    
	    // Load admin scripts
	    add_action('admin_enqueue_scripts', array($this, 'ob_mp4_cc_admin_script'));
	    
	    // Load admin scripts
	    add_action('wp_ajax_radio_ob_mp4_cc_ajax_add_term', array($this, 'ob_mp4_cc_ajax_add_term'));
	}
	
	
	/**
	 * Register post type and taxonomy
	 *
	 * Registers a custom post type called 'ob_video_comment'
	 * and a custom taxonomy called —b_mp4_cc_categories'
	 *
	 * @param	none
	 * @return	none
	*/
	
	public static function ob_mp4_cc_post_type_and_taxonomy_init() {
	    $ob_mp4_cc_type_args = array(
		'labels' => array(
		    'name' => _x('OB MP4 Conversions and Compressions', 'post type general name'),
		    'singular_name' => _x('OB MP4 Conversion and/or Compression', 'post type singular name'),
		    'add_new' => _x('Add New OB MP4 Conversion and/or Compression', 'image'),
		    'add_new_item' => __('Add OB MP4 Conversion and/or Compression'),
		    'edit_item' => __('Edit OB MP4 Conversion and/or Compression'),
		    'new_item' => __('Add New OB MP4 Conversion and/or Compression'),
		    'all_items' => __('View OB MP4 Conversions and Compressions'),
		    'view_item' => __('View OB MP4 Conversion and/or Compression'),
		    'search_items' => __('Search OB MP4 Conversions and Compressions'),
		    'not_found' =>  __('No OB OB MP4 Conversions and Compressions found'),
		    'not_found_in_trash' => __('No OB MP4 Conversions and Compressions found in Trash'), 
		    'parent_item_colon' => '',
		    'menu_name' => 'OB MP4 ConComps'
		),
		'public' => true,
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'capabilities' => array(
		    'create_posts' => false, // Removes support for the "Add New" functionality
		),
		'has_archive' => true, 
		'hierarchical' => false,
		'map_meta_cap' => true,
		'menu_position' => null,
		'supports' => array(
		    'thumbnail',
		    'custom-fields'
		)
	    );
	    register_post_type('ob_mp4_c_and_c', $ob_mp4_cc_type_args);
	    
	    $ob_mp4_cc_category_args = array(
		'labels' => array(
		    'name' => _x( 'OB MP4 Conversions and Compressions Categories', 'taxonomy general name' ),
		    'singular_name' => _x( 'OB MP4 Conversions and Compressions Category', 'taxonomy singular name' ),
		    'search_items' =>  __( 'OB MP4 Conversions and Compressions Categories' ),
		    'all_items' => __( 'All OB MP4 Conversions and Compressions Categories' ),
		    'parent_item' => __( 'Parent OB MP4 Conversions and Compressions Category' ),
		    'parent_item_colon' => __( 'Parent OB MP4 Conversions and Compressions Category:' ),
		    'edit_item' => __( 'Edit OB MP4 Conversions and Compressions Category' ), 
		    'update_item' => __( 'Update OB MP4 Conversions and Compressions Category' ),
		    'add_new_item' => __( 'Add New OB MP4 Conversions and Compressions Category' ),
		    'new_item_name' => __( 'New OB MP4 Conversions and Compressions Category' ),
		    'menu_name' => __( 'OB MP4 Conversions and Compressions Categories' ),
		),   
		'hierarchical' => true,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array(
		    'slug' => 'ob_mp4_cc_category'
		)
	    );
	     
	    register_taxonomy('ob_mp4_cc_category', array('ob_mp4_c_and_c'), $ob_mp4_cc_category_args);
	    
	    // Default concomp categories 
	    $ob_mp4_cc_default_categories = array('Autos & Vehicles', 'Comedy', 'Education', 'Film & Animation',
		'Gaming', 'Howto & Style', 'Music', 'News & Politics', 'Nonprofits & Activism',
		'People & Blogs', 'Pets & Animals', 'Science & Technology', 'Sports',
		'Travel & Events'
		);
	     
	    foreach($ob_mp4_cc_default_categories as $cat) {
	     
		if(!term_exists($cat, 'ob_mp4_cc_category')) wp_insert_term($cat, 'ob_mp4_cc_category');
	       
	    }
	}
	
	
	/**
	 * Adds metabaxes to the custom post type backend
	 *
	 * @param	none
	 * @return	none
	*/
	
	public function ob_mp4_cc_video_metaboxes() {
	    add_action('add_meta_boxes', array($this, 'ob_mp4_cc_add_video_metaboxes'));
	}
	
	
	/**
	 * Metaboxes callback
	 *
	 * Callback implements the video comment and caption metaboxex.
	 * Through this, a user will be able to see the caption,
	 * view the video concomp and get direct access to the
	 * video concomp file through a URL
	 *
	 * @param	none
	 * @return	none
	*/
	
	public function ob_mp4_cc_add_video_metaboxes() {       
	    add_meta_box('ob_mp4_cc_meta_box_caption', 'Caption', 'ob_mp4_cc_caption', 'ob_mp4_c_and_c');
	    add_meta_box('ob_mp4_cc_meta_box_video', 'Video', 'ob_mp4_cc_video', 'ob_mp4_c_and_c');
	    
	    function ob_mp4_cc_video($post) {
		$ob_mp4_cc_video = get_attached_media('video', $post->ID);
		    foreach ($ob_mp4_cc_video as $video) {
		?>
			<p>
			    <label for="ob_mp4_cc_meta_box_video"> URL: </label>
			    <input type="text" name="ob_mp4_cc_meta_box_video" id="ob_mp4_cc_meta_box_video" value="<?php echo wp_get_attachment_url($video->ID, 'full'); ?>" />
			    <a href="<?php echo wp_get_attachment_url($video->ID, 'full'); ?>" target="_blank">
                                <input type="button" id="ob_mp4_cc_other_download_button" class="button button-primary" name="ob_mp4_cc_other_download_button" value="Download"/>
                            </a>
			    <label for="ob_mp4_cc_other_download_help"> (Right Click and "Save Target/Link As...")  </label>
			    <div class="wp_attachment_holder">
				<div style="width: 640px; max-width: 100%;" class="wp-video"><!--[if lt IE 9]><script>document.createElement('video');</script><![endif]-->
				    <video class="wp-video-shortcode" width="640" height="360" preload="metadata" controls="controls"><source type="<?php echo get_post_mime_type($video->ID); ?>" src="<?php echo wp_get_attachment_url($video->ID, 'full'); ?>" /><a href="<?php echo wp_get_attachment_url($video->ID, 'full'); ?>"><?php echo wp_get_attachment_url($video->ID, 'full'); ?></a></video>
				</div>
			    </div>                
			</p>
		<?php
			break;
		    }
	    }
	    
	    function ob_mp4_cc_caption($post) {
	    ?>
		<p>
		    <textarea class="widefat" rows="1" cols="40" name="ob_mp4_cc_meta_box_caption" id="ob_mp4_cc_meta_box_caption"><?php echo get_the_title($post->ID); ?></textarea>
		</p>
	    <?php
	    }
	}
	
	
	/**
	 * Removes the existing taxonomy metabox
	 *
	 * @param	none
	 * @return	none
	*/
	
	public function ob_mp4_cc_remove_meta_box(){  
	    remove_meta_box($this->ob_mp4_cc_taxonomy_metabox_id, $this->ob_mp4_cc_post_type, 'normal');  
	}
	
	
	/**
	 * Add radio buttons-based metabox
	 *
	 * Add the new custom taxonomy metabox that is based
	 * on radio buttons instead of checkboxes. In this way,
	 * a video comment can be associated with only one term.
	 *
	 * @param	none
	 * @return	none
	*/
	
	public function ob_mp4_cc_add_taxonomy_meta_box() {  
	    add_meta_box(
		'ob_mp4_cc_meta_box_categories',
		'OB MP4 ConComp Categories',
		array($this, 'ob_mp4_cc_taxonomy_metabox'),
		$this->ob_mp4_cc_post_type,
		'side',
		'core'
	    );  
	}
	
	/**
	 * Radio buttons-based metaxbox callback
	 *
	 * Callback implements the new taxonomy metabox
	 * based on radio buttons
	 *
	 * @param	$post: the video concomp post variable
	 * @return	none
	*/ 
	public function ob_mp4_cc_taxonomy_metabox($post) {
	    //Get taxonomy and terms  
	    $taxonomy = $this->ob_mp4_cc_taxonomy;
	  
	    //Set up the taxonomy object and get terms  
	    $tax = get_taxonomy($taxonomy);
	    $terms = get_terms($taxonomy, array('hide_empty' => 0));
	  
	    //Name of the form
	    $name = 'tax_input[' . $taxonomy . ']';
	  
	    //Get current and popular terms  
	    $popular = get_terms( $taxonomy, array( 'orderby' => 'count', 'order' => 'DESC', 'number' => 10, 'hierarchical' => false ) );  
	    $postterms = get_the_terms($post->ID, $taxonomy);  
	    $current = ($postterms ? array_pop($postterms) : false);  
	    $current = ($current ? $current->term_id : 0);  
	    ?>  
	  
		<div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
		    <!-- Display tabs-->
		    <ul id="<?php echo $taxonomy; ?>-tabs" class="category-tabs">
			<li class="tabs"><a href="#<?php echo $taxonomy; ?>-all" tabindex="3"><?php echo $tax->labels->all_items; ?></a></li>
			<li class="hide-if-no-js"><a href="#<?php echo $taxonomy; ?>-pop" tabindex="3"><?php _e( 'Most Used' ); ?></a></li>
		    </ul>
    
		    <!-- Display taxonomy terms -->
		    <div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
			<ul id="<?php echo $taxonomy; ?>checklist" class="list:<?php echo $taxonomy?> categorychecklist form-no-clear">
			<?php
			    foreach($terms as $term) {
				$id = $taxonomy.'-'.$term->term_id;
				$value = (is_taxonomy_hierarchical($taxonomy) ? "value='{$term->term_id}'" : "value='{$term->term_slug}'");
				echo "<li id='$id'><label class='selectit'>";
				echo "<input type='radio' id='in-$id' name='{$name}'".checked($current,$term->term_id,false)." {$value} />$term->name<br />";
				echo "</label></li>";
			     }
			?>
			</ul>
		    </div>
    
		    <!-- Display popular taxonomy terms -->
		    <div id="<?php echo $taxonomy; ?>-pop" class="tabs-panel" style="display: none;">
			<ul id="<?php echo $taxonomy; ?>checklist-pop" class="categorychecklist form-no-clear" >
			<?php
			    foreach($popular as $term){
				$id = 'popular-'.$taxonomy.'-'.$term->term_id;
				$value= (is_taxonomy_hierarchical($taxonomy) ? "value='{$term->term_id}'" : "value='{$term->term_slug}'");
				echo "<li id='$id'><label class='selectit'>";
				echo "<input type='radio' id='in-$id'".checked($current,$term->term_id,false)." {$value} />$term->name<br />";
				echo "</label></li>";
			    }
			?>
			</ul>
		    </div>
    
		    <p id="<?php echo $taxonomy; ?>-add" class="">
			<label class="screen-reader-text" for="new<?php echo $taxonomy; ?>"><?php echo $tax->labels->add_new_item; ?></label>
			<input type="text" name="new<?php echo $taxonomy; ?>" id="new<?php echo $taxonomy; ?>" class="form-required form-input-tip" value="<?php echo esc_attr( $tax->labels->new_item_name ); ?>" tabindex="3" aria-required="true"/>
			<input type="button" id="" class="radio-tax-add button" value="<?php echo esc_attr( $tax->labels->add_new_item ); ?>" tabindex="3" />
			<?php wp_nonce_field( 'radio-tax-add-'.$taxonomy, '_wpnonce_radio-add-tag', false ); ?>
		    </p>
		</div>
	    <?php  
	}
	
	
	/**
	 * Add screenshot metabox
	 *
	 * Add the video concomp screenshot metabox that is based.
	 *
	 * @param	none
	 * @return	none
	*/
	
	public function ob_mp4_cc_add_screenshot_meta_box() {  
	    add_meta_box(
		'ob_mp4_cc_meta_box_screenshot',
		'Screenshot',
		array($this, 'ob_mp4_cc_screenshot_metabox'),
		$this->ob_mp4_cc_post_type,
		'side',
		'core'
	    );
	}
	
	/**
	 * Screenshot metaxbox callback
	 *
	 * Callback implements the screenshot metabox
	 *
	 * @param	$post: the video concomp post variable
	 * @return	none
	*/ 
	public function ob_mp4_cc_screenshot_metabox($post) {
	    $ob_mp4_cc_screenshot = get_attached_media('image', $post->ID);
	    foreach ($ob_mp4_cc_screenshot as $screenshot) {
	?>
		<p>
		    <label for="ob_mp4_cc_meta_box_screenshot"> URL: </label>
		    <input type="text" name="ob_mp4_cc_meta_box_screenshot" id="ob_mp4_cc_meta_box_screenshot" value="<?php echo wp_get_attachment_url($screenshot->ID, 'full'); ?>" />
		    <a href="<?php echo wp_get_attachment_url($screenshot->ID, 'full'); ?>" target="_blank">
			<input type="button" id="ob_mp4_cc_screenshot_view_button" class="action" name="ob_mp4_cc_screenshot_view_button" value="View"/>
		    </a>               
		</p>
	<?php
		break;
	    } 
	}
	
	/**
	 * Add scripts to make the new radio button based
	 * metabox work
	 *
	 * @param	none
	 * @return	none
	*/
	
	public function ob_mp4_cc_admin_script() {  
	    wp_register_script('radiotax', plugins_url('js/radiotax.js', __FILE__), array('jquery'), null, true ); // We specify true here to tell WordPress this script needs to be loaded in the footer  
	    wp_localize_script('radiotax', 'radio_tax', array('slug'=>$this->ob_mp4_cc_taxonomy));
	    wp_enqueue_script('radiotax');  
	}
	
	
	/**
	 * Add terms to the new radio button based custom
	 * taxonomy metabox
	 *
	 * @param	none
	 * @return	none
	*/
	
	public function ob_mp4_cc_ajax_add_term() {
    
	    $taxonomy = !empty($_POST['taxonomy']) ? $_POST['taxonomy'] : '';
	    $term = !empty($_POST['term']) ? $_POST['term'] : '';
	    $tax = get_taxonomy($taxonomy);
    
	    check_ajax_referer('radio-tax-add-'.$taxonomy, '_wpnonce_radio-add-tag');
    
	    if(!$tax || empty($term))
		    exit();
    
	    if ( !current_user_can( $tax->cap->edit_terms ) )
		    die('-1');
    
	    $tag = wp_insert_term($term, $taxonomy);
    
	    if ( !$tag || is_wp_error($tag) || (!$tag = get_term( $tag['term_id'], $taxonomy )) ) {
		    //TODO Error handling
		    exit();
	    }
    
	    $id = $taxonomy.'-'.$tag->term_id;
	    $name = 'tax_input[' . $taxonomy . ']';
	    $value= (is_taxonomy_hierarchical($taxonomy) ? "value='{$tag->term_id}'" : "value='{$term->tag_slug}'");
    
	    $html ='<li id="'.$id.'"><label class="selectit"><input type="radio" id="in-'.$id.'" name="'.$name.'" '.$value.' />'. $tag->name.'</label></li>';
    
	    echo json_encode(array('term'=>$tag->term_id,'html'=>$html));
	    exit();
	}
	
	
	/**
	 * This validates the inputs a visitor entered when
	 * creating a video comment
	 *
	 * @param	$video_file: the video file to be uploaded
	 * @param	$video_caption: the caption
	 * @return	$result: the upload result
	*/
	
	public static function ob_mp4_cc_parse_file_errors($video_file = '', $video_caption) {
	    $result = array();
	    $result['error'] = 0;
	    
	    if($video_file['error']){
		$result['error'] = "Either your video is larger than 32MB, there was no video selected or there was an upload error!";
	       
		return $result;
	    }
	   
	    $video_caption = trim(preg_replace('/[^a-zA-Z0-9\s]+/', ' ', $video_caption));
	     
	    if($video_caption == ''){
		$result['error'] = "Your caption may only contain letters, numbers and spaces! Your caption cannot be empty!";
	       
		return $result;
	    }
	    
	    // validate video file
	    $video_file_type = wp_check_filetype($video_file['name']);
	    
	    if (((strcasecmp($video_file_type['ext'], 'mov') == 0) && (strcasecmp($video_file_type['type'], 'video/quicktime') == 0)) ||
		((strcasecmp($video_file_type['ext'], 'mov') == 0) && (strcasecmp($video_file_type['type'], 'video/x-quicktime') == 0)) ||
		((strcasecmp($video_file_type['ext'], 'mov') == 0) && (strcasecmp($video_file_type['type'], 'image/mov') == 0)) ||
		((strcasecmp($video_file_type['ext'], 'mp4') == 0) && (strcasecmp($video_file_type['type'], 'video/mp4') == 0)) ||
		((strcasecmp($video_file_type['ext'], 'flv') == 0) && (strcasecmp($video_file_type['type'], 'video/x-flv') == 0))) {
		// do nothing - no errors here
	    } else {
		$result['error'] = "You did not select a video file for the comment! Supported video formats are MOV, MP4 and FLV.";
	       
		return $result;
	    }
	    
	     
	    $result['caption'] = $video_caption;
	    
	    $video_size = filesize($video_file['tmp_name']);
	    
	    $max_size = 1000000 * (float) get_option('ob_mp4_cc_max_video_size');
	    
	    // validate video size
	    if(($video_size > $max_size)){
		$result['error'] = 'Your video was ' . $video_size . ' bytes! It must not exceed ' . $max_size . ' bytes.';
	    }
	       
	    return $result;
	}
	
	/**
	 * This processes the video uploaded by a visitor
	 * and converts it to mp4 format.  The mp4 video is
	 * the used to create a snapshot and the snapshot
	 * is used to create a 150x150 pixel thumbnail
	 *
	 * @param	$file: the image file to be uploaded
	 * @param	$post_id: the id of the video comment
	 * @param	$caption: the video comment's caption
	 * @param       $ffmpeg_path the path to the ffmpeg executable
	*/
	
	public static function ob_mp4_cc_process_everything($file, $post_id, $caption, $ffmpeg_path) {
	    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	    require_once(ABSPATH . "wp-admin" . '/includes/media.php');
	    
	    // Upload of the untouched video
	    $original_video_attachment_id = media_handle_upload($file, $post_id);
	    
	    update_post_meta($post_id, '_video_id', $original_video_attachment_id);
	    
	    // The full path to the untouched video
	    $original_video_path = get_attached_file($original_video_attachment_id);
	    
	    // array containing upload dir info
	    $uploads_array = wp_upload_dir();
	    // the path to the current upload dir
	    $uploads_path = $uploads_array['path'];
	    
	    // the path of the new video
	    $converted_video_path = $uploads_path . "/" . uniqid('ob_mp4_cc_') . ".mp4";
	    
	    // the path of the new image screenshot taken from the new video
	    $screenshot_path = $converted_video_path . ".jpg";
	    
	    // command to convert untouched video to mp4
	    $convert_video_command = $ffmpeg_path . "ffmpeg -i ". $original_video_path . " -c:v libx264 -preset " . get_option('ob_mp4_cc_ffmpeg_speed') . " -crf ". get_option('ob_mp4_cc_ffmpeg_quality') . " -c:a copy " . $converted_video_path . " 2>ffmpeg_vid.log";
	    exec($convert_video_command);

	    // command to create screenshot from newly converted video
	    $create_screenshot_command = $ffmpeg_path . "ffmpeg -y  -i " . $converted_video_path . " -f mjpeg -vframes 1 -ss 1 " . $screenshot_path . " 2>ffmpeg_3.log";
	    exec($create_screenshot_command);
	    
	    // get WP_IMAGE_EDITOR for file
	    $image = wp_get_image_editor($screenshot_path);
	    
	    // path of 150x150 thumbnail
	    $thumbnail_path = $screenshot_path . "150x150.jpg";

	    if (!is_wp_error($image)) {
		//$image->rotate( 90 );
		//$image->resize(150, 150, true);
		$image->save($thumbnail_path);
	    }
	    
	    // Attaching thumbnail to post
	    
	    // Check the type of file. We'll use this as the 'post_mime_type'.
	    $thumnail_filetype = wp_check_filetype(basename($thumbnail_path), null);
	    
	    // Get the path to the upload directory.
	    $wp_upload_dir = wp_upload_dir();
	    
	    // Prepare an array of post data for the attachment.
	    $thumbnail_attachment = array(
		    'guid'           => $wp_upload_dir['url'] . '/' . basename($thumbnail_path), 
		    'post_mime_type' => $thumnail_filetype['type'],
		    'post_title'     => preg_replace( '/\.[^.]+$/', '', basename($thumbnail_path)),
		    'post_content'   => '',
		    'post_status'    => 'inherit'
	    );
	    
	    // Insert the attachment.
	    $thumbnail_attachment_id = wp_insert_attachment($thumbnail_attachment, $thumbnail_path, $post_id);
	    
	    // Generate the metadata for the attachment, and update the database record.
	    $thumbnail_attachment_data = wp_generate_attachment_metadata($thumbnail_attachment_id, $thumbnail_path);
	    wp_update_attachment_metadata($thumbnail_attachment_id, $thumbnail_attachment_data);
	    
	    // add featured image to post
	    update_post_meta($post_id, '_thumbnail_id', $thumbnail_attachment_id);
	    
	    // Remove video attachment to make room for new one
	    wp_delete_attachment($original_video_attachment_id);
	    
	    // Attaching new video to post
	    
	    // Check the type of file. We'll use this as the 'post_mime_type'.
	    $converted_video_filetype = wp_check_filetype(basename($converted_video_path), null);
	    
	    // Get the path to the upload directory.
	    $wp_upload_dir = wp_upload_dir();
	    
	    // Prepare an array of post data for the attachment.
	    $converted_video_attachment = array(
		    'guid'           => $wp_upload_dir['url'] . '/' . basename($converted_video_path), 
		    'post_mime_type' => $converted_video_filetype['type'],
		    'post_title'     => preg_replace( '/\.[^.]+$/', '', basename($converted_video_path)),
		    'post_content'   => '',
		    'post_status'    => 'inherit'
	    );
	    
	    // Insert the attachment.
	    $converted_video_attachment_id = wp_insert_attachment($converted_video_attachment, $converted_video_path, $post_id);
	    
	    // Generate the metadata for the attachment, and update the database record.
	    $converted_video_attachment_data = wp_generate_attachment_metadata($converted_video_attachment_id, $converted_video_path);
	    wp_update_attachment_metadata($converted_video_attachment_id, $converted_video_attachment_data);
	    
	    update_post_meta($post_id, '_video_id', $converted_video_attachment_id);
	}
    }

endif;
?>