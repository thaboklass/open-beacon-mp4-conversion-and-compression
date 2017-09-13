<?php
// If uninstall is not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

// delete options from options table
delete_option('ob_mp4_cc_ffmpeg_path');
delete_option('ob_mp4_cc_ffmpeg_quality');
delete_option('ob_mp4_cc_ffmpeg_speed');
delete_option('ob_mp4_cc_max_video_size');
delete_option('ob_mp4_cc_system_environment_section');
delete_option('ob_mp4_cc_ffmpeg_exists'); 
delete_option('ob_mp4_cc_category_children');
?>