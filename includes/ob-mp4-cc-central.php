<?php

/*------------------------------------------------------------------*
 * The 'ob_mp4_cc_central' class
/*------------------------------------------------------------------*/

if (!class_exists('ob_mp4_cc_central')) :

class ob_mp4_cc_central {
    public $ob_mp4_cc_assistant;
    public $ob_mp4_cc_ffmpeg_ext;
    public $ob_mp4_cc_ffmpeg_presets;
    
    // This hold a "boolean" indicating whether or not
    // an upload just occured in the latest refresh - that
    // is, whether or not the request contains any post data
    public $ob_mp4_cc_upload_occured;
       
    /**
    * The 'ob_mp4_cc_central' constructor
    * 
    */
    
    public function __construct() {
        // create new 'assistant' class
        $this->ob_mp4_cc_assistant = new ob_mp4_cc_assistant();
        
        if (get_option('ob_mp4_cc_ffmpeg_exists') === FALSE) {
            $this->ob_mp4_cc_assistant->ob_mp4_cc_command_exists_check('ffmpeg', true);
        }
        
        $this->ob_mp4_cc_ffmpeg_ext = get_option('ob_mp4_cc_ffmpeg_exists', 0);
        
        $this->ob_mp4_cc_ffmpeg_presets = array(
            "ultrafast",
            "superfast",
            "veryfast",
            "faster",
            "fast",
            "medium",
            "slow",
            "slower",
            "veryslow"
        );
        
        // add OB MP4 CC options
        $this->ob_mp4_cc_add_options();
        
        // if an upload has been made, the $ob_mp4_cc_upload_occured
        // variable will be set to 1 otherwise, it is set to 0
        // 
        $this->ob_mp4_cc_upload_occured = 0;
        if(isset($_FILES['ob_mp4_cc_vid'])) {
            $this->ob_mp4_cc_upload_occured = 1;
        }
        
        // This loads admin scripts
        add_action('admin_enqueue_scripts', array($this, 'ob_mp4_cc_load_admin_scripts'));
    }
    
    
    /*------------------------------------------------------------------*
     * Menus
    /*------------------------------------------------------------------*/
    
    /**
     * Adds 'OB Video Comment Settings' menu item
     *
     * Adds the 'Basic Functionality' menu titled 'OB Video Comment Settings'
     * as a top level menu item in the dashboard.
     *
     * @param	none
     * @return	none
    */
    
    public function ob_mp4_cc_add_menu_page() {
        
        // Introduces a top-level menu page
        add_menu_page(
            'Conversion and Compression Central',                        // The text that is displayed in the browser title bar
            __('OB MP4 ConComp Central'),                                // The text that is used for the top-level menu
            'manage_options',                                            // The user capability to access this menu
            'ob-mp4-cc_central',                                         // The name of the menu slug that accesses this menu item
            array($this, 'ob_mp4_cc_central_display'),                   // The name of the function used to display the page content
            '');
    } // end of function ob_mp4_cc_add_menu_page
    
    
    
    /*------------------------------------------------------------------*
     * Sections, Settings and Fields
    /*------------------------------------------------------------------*/
    
    /**
     * Register section, fields and page
     *
     * Registers a new settings section and settings fields on the
     * 'OB Video Comment Settings' page of the WordPress dashboard.
     *
     * @param	none
     * @return	none
    */
    
    public function ob_mp4_cc_initialize_options() {
        // Introduce an new section that will be rendered on the new
        // settings page.  This section will be populated with settings
        // that will give the 'OB MP4 Conversion and Compression' plugin its basic
        // functionality
        add_settings_section(
            'ob_mp4_cc_functionality_settings_section',                                // The ID to use for this section
            'Functionality Settings',                                            // The title of this section that is rendered to the screen
            array($this, 'ob_mp4_cc_functionality_settings_section_display'),  // The function that is used to render the options for this section
            'ob-mp4-cc_central'                                         // The ID of the page on which the section is rendered
        );
        
        // Defines the settings field 'Maxim Video Size'
        // which controls the size of the video comment
        // a user can upload
        add_settings_field(
            'ob_mp4_cc_max_video_size',                                // The ID of the setting field
            'Maximum Video Size(MB) for WordPress',                              // The text to be displayed
            array($this, 'ob_mp4_cc_max_video_size_display'),  // The function used to render the setting field
            'ob-mp4-cc_central',                           // The ID of the page on which the setting field is rendered
            'ob_mp4_cc_functionality_settings_section'                    // The section to which the setting field belongs
        );
        
        // Register the 'ob_mp4_cc_max_video_size'
        // with the 'Functionality Options' section
        register_setting(
            'ob_mp4_cc_functionality_settings_section',  // The section holding the settings fields
            'ob_mp4_cc_max_video_size'                // The name of the settings field to register
        );
        
        // Simply displays the system environment
        add_settings_field(
            'ob_mp4_cc_system_environment_section',
            'FFmpeg',
            array($this, 'ob_mp4_cc_system_environment'),
            'ob-mp4-cc_central',
            'ob_mp4_cc_functionality_settings_section'
        );
        
        // Register the 'ob_mp4_cc_system_environment_section'
        // with the 'Functionality Options' section
        register_setting(
            'ob_mp4_cc_functionality_settings_section',
            'ob_mp4_cc_system_environment_section'
        );
        
        // Defines the settings field 'FFMpeg location'
        // which is the location where FFmpeg is located
        // of a remote host or server
        add_settings_field(
            'ob_mp4_cc_ffmpeg_path',
            '',
            array($this, 'ob_mp4_cc_ffmpeg_path'),
            'ob-mp4-cc_central',
            'ob_mp4_cc_functionality_settings_section'
        );
        
        // Register the 'ob_mp4_cc_ffmpeg_path'
        // with the 'Functionality Options' section
        register_setting(
            'ob_mp4_cc_functionality_settings_section',
            'ob_mp4_cc_ffmpeg_path'
        );
        
        // Defines the settings field 'FFMpeg Speed'
        // which is sets the conversion preset that
        // the speeed at which a video is encoded
        add_settings_field(
            'ob_mp4_cc_ffmpeg_speed',
            '',
            array($this, 'ob_mp4_cc_ffmpeg_speed'),
            'ob-mp4-cc_central',
            'ob_mp4_cc_functionality_settings_section'
        );
        
        // Register the 'ob_mp4_cc_ffmpeg_speed'
        // with the 'Functionality Options' section
        register_setting(
            'ob_mp4_cc_functionality_settings_section',
            'ob_mp4_cc_ffmpeg_speed'
        );
        
        // Defines the settings field 'FFmpeg Quality'
        // which is sets the quality of the converted
        // video
        add_settings_field(
            'ob_mp4_cc_ffmpeg_quality',
            '',
            array($this, 'ob_mp4_cc_ffmpeg_quality'),
            'ob-mp4-cc_central',
            'ob_mp4_cc_functionality_settings_section'
        );
        
        // Register the 'ob_mp4_cc_ffmpeg_quality'
        // with the 'Functionality Options' section
        register_setting(
            'ob_mp4_cc_functionality_settings_section',
            'ob_mp4_cc_ffmpeg_quality'
        );
        
        // After the user interface elements have been rendered,
        // upload the video data if that has been requested.
        $this->ob_mp4_cc_video_data_upload();
    } // end of function ob_mp4_cc_initialize_options
    
    
    
    /*------------------------------------------------------------------*
     * Callbacks
    /*------------------------------------------------------------------*/
    
    /**
     * This function is used to render all of the page content
     *
     * @param	none
     * @return	none
     */
    
    public function ob_mp4_cc_central_display($active_tab = '') {
    ?>
        <div class="wrap" id="ob_mp4_cc_main_content">
            <?php
                if(isset($_POST['ob_mp4_cc_upload_form_submitted'])) {
            ?>
            <div id="ob_mp4_cc_upload_complete">
                <p>Your video was converted and compressed sucessfully! Go to "OB MP4 ConComps->View..." to review and download the results.</p>
            </div>
            <?php
                }
            ?>
            <div id="icon-options-general" class="icon32"></div>
            <h2>OB MP4 Conversion and Compression</h2>
            <?php
            if(isset($_GET[ 'tab' ])) {
                $active_tab = $_GET['tab'];
            } else if($active_tab == 'settings') {
                $active_tab = 'settings';
            } else {
                $active_tab = 'central';
            } // end if/else
            ?>
            <h2 class="nav-tab-wrapper">
                <a href="?page=ob-mp4-cc_central&tab=central" class="nav-tab <?php echo $active_tab == 'central' ? 'nav-tab-active' : ''; ?>">Central</a>
                <a href="?page=ob-mp4-cc_central&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>
            </h2>
            <?php 
            if($active_tab == 'central') {
                ?>
                <h3>Central Feature - Video Conversion and Compression</h3>
                This is where the conversion and compression of video data happens. Click the 'Settings' tab to set up the environment before going any further.
                <form id="<?php if (get_option('ob_mp4_cc_ffmpeg_exists') == 1) { echo "ob_mp4_cc_upload_form"; } else { echo "ob_mp4_cc_upload_form_ffmpeg_not_found"; }?>" action="" method="post" enctype="multipart/form-data">
                    <?php echo wp_nonce_field('ob_mp4_cc_upload_form', 'ob_mp4_cc_upload_form_submitted'); ?>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Select a video to convert and compress:</th>
                            <td>
                                <input type="file" accept="video/*" name="ob_mp4_cc_vid" id="ob_mp4_cc_vid" capture><br>
                                <p>Pick a video from the MOV, MP4 or FLV file formats. The video will be converted</p>
                                <p>or compressed or both, depending on what you stipulated in the settings.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Pick your video's category:</th>
                            <td>
                                <?php $this->ob_mp4_cc_categories_display(); ?>
                                <p>The category that best describes your video.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Write a short caption:</th>
                            <td>
                                <input type="text" name="ob_mp4_cc_caption" id="ob_mp4_cc_caption"><br>
                                <p>Make the caption short but relevant so that it will be easy to remember</p>
                                <p>the video when selecting it from the 'OB MP4 ConComps' list.</p>
                            </td>
                        </tr>
                    </table>
                    <input type="submit" class="button button-primary" name="ob_mp4_cc_submit" value="Convert and Compress Video" id="ob_mp4_cc_submit">
                    <img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" id="ob_mp4_cc_loading" class="ob_mp4_cc_loading"/>
                </form>
                <div id="ob_mp4_cc_incomplete_dialog" title="Please fill all required fields!">
                    <p>It appears that you may have not filled one or more fields.  Please make sure the video and caption fields are filled. Also make sure that the correct video format has been selected.</p>
                </div>
                <div id="ob_mp4_cc_ffmpeg_not_found">
                    <p>FFmpeg was not found! Please go to the "Settings" tab and enter the correct FFmpeg path.</p>
                </div>
                <?php
            ?>
            <?php
            } else {
            ?>
            <form method="post" action="options.php">
            <?php
            
                // Outputs pertinent nonces, actions and options for
                // the section
                settings_fields('ob_mp4_cc_functionality_settings_section');
                
                // Renders the setting sections added to the page
                // 'Basic Fuctionality'
                do_settings_sections('ob-mp4-cc_central');
                
                // Renders a submit button that saves all of the options
                // pertaining to the settings fields
                submit_button();
            ?>
            </form>
            <?php
            }
            ?>
        </div>
    <?php
    }
    
    
    /**
     * Inline 'Functionality Options' description
     *
     * Displays an explanation of the role of the 'Functionality
     * Options' section.
     *
     * @param	none
     * @return	none
     */
    
    public function ob_mp4_cc_functionality_settings_section_display() {
        echo "These options are designed to help you control the functionality of the OB MP4 Conversion and Compression.";
    }
    
    
    /**
     * Renders 'Maximum Video Size'
     *
     * Renders the input field for the 'Maximum Video
     * Size' setting in the 'Functionality Options'
     * section.
     *
     * @param	none
     * @return	none
     */
    
    public function ob_mp4_cc_max_video_size_display() {
    ?>
        <input type="number" name="ob_mp4_cc_max_video_size" id="ob_mp4_cc_max_video_size" value="<?php echo get_option('ob_mp4_cc_max_video_size'); ?>" />
        <p>The maximum size of the video that a user can upload when converting and compressing.</p>
        <p>According to your system environment, <b><?php echo ob_mp4_cc_assistant::ob_mp4_cc_max_upload_size(false); ?>MB</b> is the maximum size allowed. You can increase</p>
        <p>this figure by adjusting 'upload_max_filesize', 'post_max_size' and 'memory_limit' in your</p>
        <p>php.ini file.</p>
    <?php    
    } // end of ob_mp4_cc_max_video_size_display
    
    
    /**
     * Renders 'System Environment' section
     *
     * Renders the 'System Environment' section
     * setting in the 'Functionality Options' section.
     *
     * @param	none
     * @return	none
     */
    public function ob_mp4_cc_system_environment() {
	$exts = array(
	    'FFMPEG'=>$this->ob_mp4_cc_ffmpeg_ext
	);
    ?>
        <h4><?php _e('System Environment');?></h4>
        <ul>
            <?php 
            if(is_array($_SERVER)):?>
                    <li><strong><?php _e('Server');?></strong> <span><?php echo $_SERVER['SERVER_SOFTWARE'];?></span></li>
            <?php
            endif;
            ?>
            <?php if(function_exists('phpversion')):?>
            <li><strong><?php _e('PHP');?></strong> <span><?php echo phpversion();?></span></li>
            <?php endif;?>
            <?php 
                $exec_c = $this->ob_mp4_cc_assistant->ob_mp4_cc_check_function('exec');
            ?>
            <li><strong><?php _e('EXEC');?></strong> <span><?php if($exec_c){ echo '<span class="ob_mp4_cc_true">ENABLED</span>';} else { echo '<span class="ob_mp4_cc_false">DISABLED</span>';}?></span></li>
            <?php 
            foreach($exts as $k=>$ext):?>
                    <li><strong><?php echo $k;?></strong> <span class="<?php echo strtolower($k);?>"><?php if($ext) { echo '<span class="ob_mp4_cc_true">FOUND</span>';} else { echo '<span class="ob_mp4_cc_false">NOT FOUND</span>';}?></span></li>
            <?php endforeach; ?>
        </ul>
        <!--<input type="button" value="Re-check FFMPEG" class="recheckExt" />-->
        
    <?php    
    } // end of ob_mp4_cc_system_environment
    
    /**
     * Renders 'FFmpeg Path' section
     *
     * Renders the 'FFmpeg path' section
     * setting in the 'Functionality Options' section.
     *
     * @param	none
     * @return	none
     */
    public function ob_mp4_cc_ffmpeg_path() {
    ?>
        <b>If FFmpeg was not automatically found, enter the path to your FFmpeg on</b><br/>
        <b>your remote host or server in the field below and click the 'Save' button</b><br/>
        <b>at the bottom. If 'EXEC' is not enabled or 'FFMPEG' is still not found after</b><br/>
        <b>enter the path below, FFmpeg conversion will not work.</b><br/><br/>
        Path to FFmpeg installation: <input type="text" name="ob_mp4_cc_ffmpeg_path" id="ob_mp4_cc_ffmpeg_path" value="<?php echo get_option('ob_mp4_cc_ffmpeg_path'); ?>" />
        <?php _e("(example: /usr/local/bin/ or c:/wamp/www/ffmpeg/)"); ?> 
    <?php    
    } // end of ob_mp4_cc_ffmpeg_path
    
    /**
     * Renders 'FFmpeg Speed' section
     *
     * Renders the 'FFmpeg speed' section
     * setting in the 'Functionality Options' section.
     *
     * @param	none
     * @return	none
     */
    public function ob_mp4_cc_ffmpeg_speed() {
    ?>
        FFmpeg conversion speed: 
        <select name="ob_mp4_cc_ffmpeg_speed">
            <?php
                for ($i = 0; $i < count($this->ob_mp4_cc_ffmpeg_presets); $i++) {
            ?>
            <option value="<?php echo $this->ob_mp4_cc_ffmpeg_presets[$i]; ?>" <?php selected(get_option('ob_mp4_cc_ffmpeg_speed'), $this->ob_mp4_cc_ffmpeg_presets[$i]); ?>><?php echo $this->ob_mp4_cc_ffmpeg_presets[$i]; ?></option>
            <?php
                }
            ?>
        </select><br/>
        <?php _e("This is the speed at which FFmpeg conversion happens. Slower speeds result in"); ?><br/>
        <?php _e("better compression."); ?> 
    <?php    
    } // end of ob_mp4_cc_ffmpeg_speed
    
    /**
     * Renders 'FFmpeg Quality' section
     *
     * Renders the 'FFmpeg quality' section
     * setting in the 'Functionality Options' section.
     *
     * @param	none
     * @return	none
     */
    public function ob_mp4_cc_ffmpeg_quality() {
    ?>
        FFmpeg video quality: 
        <select name="ob_mp4_cc_ffmpeg_quality">
            <?php
                for ($i = 21; $i < 52; $i++) {
            ?>
            <option value="<?php echo $i; ?>" <?php selected(get_option('ob_mp4_cc_ffmpeg_quality'), $i); ?>><?php echo $i . ""; ?></option>
            <?php
                }
            ?>
        </select><br/>
        <?php _e("21 is the best video quality and 51 is the worst."); ?> 
    <?php    
    } // end of ob_mp4_cc_ffmpeg_quality
    
    public function ob_mp4_cc_categories_display() {
    ?>
    </label><?php echo $this->ob_mp4_cc_get_category_dropdown('ob_mp4_cc_category', 0); ?><br/>
    <?php
    }
    
    
    /**
    * Taxonomy drop-down list on front-end widget
    *
    * @param $taxonomy     The taxanomy to be used to retrieve data.
    * @param $selected     Which item is to be selected on the list   
    */
    
    function ob_mp4_cc_get_category_dropdown($taxonomy, $selected){
        return wp_dropdown_categories(array('taxonomy' => $taxonomy, 'name' => 'ob_mp4_cc_category', 'selected' => $selected, 'hide_empty' => 0, 'echo' => 0));
    }
    
    /**
    * Upload the content provided by the visitor to create the
    * video comment
    *
    * @param    none
    * @return   none
    */

    function ob_mp4_cc_video_data_upload() {
        // If the $_POST data and nonce are set, upload the data
        // within the video comment inputs
        if(isset($_POST['ob_mp4_cc_upload_form_submitted']) && wp_verify_nonce($_POST['ob_mp4_cc_upload_form_submitted'], 'ob_mp4_cc_upload_form')) {
            // sanitize the caption
            $ob_mp4_cc_sanitized_caption = sanitize_text_field($_POST['ob_mp4_cc_caption']);
            
            // This parses through any possible errors of the input
            // data and returns a description of the error if it
            // exists.
            
            $result = ob_mp4_cc_upload::ob_mp4_cc_parse_file_errors($_FILES['ob_mp4_cc_vid'], $ob_mp4_cc_sanitized_caption);
            
            if($result['error']){
             
                echo '<p>WHOOPS: ' . $result['error'] . '</p>';
             
            } else { // if no errors were present, the upload continues
                $ob_mp4_cc_visitor_id = get_current_user_id();
                
                $video_cc_data = array(
                  'post_title' => $result['caption'],
                  'post_status' => 'publish',
                  'post_author' => $ob_mp4_cc_visitor_id,
                  'post_type' => 'ob_mp4_c_and_c'    
                );
                 
                if ($ob_mp4_cc_post_id = wp_insert_post($video_cc_data)) {
                    // This uploads the video, processes it, creates a thumbnail, inserts the caption and parent post id
                    ob_mp4_cc_upload::ob_mp4_cc_process_everything('ob_mp4_cc_vid', $ob_mp4_cc_post_id, $result['caption'], get_option('ob_mp4_cc_ffmpeg_path'));
                    
                    // This refreshes the custom post type and taxonomy
                    ob_mp4_cc_upload::ob_mp4_cc_post_type_and_taxonomy_init();
                  
                    // This adds one term out of the taxonomy (one region)
                    // to the video comment. This is data viewed on the
                    // backend by an administrator
                    $term_taxonomy_ids = wp_set_object_terms($ob_mp4_cc_post_id, (int)$_POST['ob_mp4_cc_category'], 'ob_mp4_cc_category');
                  
                    if (is_wp_error($term_taxonomy_ids)) {
                        echo '<p>WHOOPS: There was an error assigning a region to the comment</p>';
                        var_dump($term_taxonomy_ids);
                    }
                }
            }
        }
    }
    
    /**
    * Add options for new activation
    *
    * This checks whether or not backend options that define the basic
    * functionality have been added and if not, they are added
    * with what have been determined as the most efficient defaults
    *
    * @param	none
    * @return	none
   */
   
    public function ob_mp4_cc_add_options() {
       if (!get_option('ob_mp4_cc_max_video_size')) {
           $max_size = ob_mp4_cc_assistant::ob_mp4_cc_max_upload_size(false);
           $max_size_string = $max_size . "";
           add_option('ob_mp4_cc_ffmpeg_path');
           add_option('ob_mp4_cc_ffmpeg_quality', '32');
           add_option('ob_mp4_cc_ffmpeg_speed', 'ultrafast');
           add_option('ob_mp4_cc_max_video_size', $max_size_string);
           add_option('ob_mp4_cc_system_environment_section');
       }
    }
    
    /**
    * Load scripts
    *
    * Load all relevant styles and scripts - in this case we load just
    * one stylesheet and two javascript files
    *
    * @param	none
    * @return	none
   */
   
    public function ob_mp4_cc_load_admin_scripts() {
        wp_register_style('ob_mp4_cc_admin_css', plugins_url('../css/admin.css', __FILE__));
        wp_register_script('ob_mp4_cc_after_upload', plugins_url('../js/ob-mp4-cc-after-upload.js', __FILE__), array('jquery'), null, true);
            
        // Get the address of this WP installation's 'admin-ajax.php'
        //$ob_mp4_cc_ajax_url = admin_url('admin-ajax.php');
        
        // The array of data that will be passed to an external script
        //$ob_mp4_cc_ajax_params = array(
        //    'ob_mp4_cc_ajax_url' => $ob_mp4_cc_ajax_url,
        //    'ob_mp4_cc_upload_occured' => $this->ob_mp4_cc_upload_occured
        //);
        
        // Pass the PHP parameter to Javascript by localizing it
        //wp_localize_script('ob_mp4_cc_after_upload', 'ob_mp4_cc_ajax_params', $ob_mp4_cc_ajax_params);
        
        wp_enqueue_style('ob_mp4_cc_admin_css');
        wp_enqueue_script('ob_mp4_cc_after_upload');
    }
}

endif;
?>