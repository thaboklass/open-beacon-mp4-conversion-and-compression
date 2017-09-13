/**
* Validate input
*
* This validates the data that
* visitors/user enter when creating video comments
*
*/

jQuery(document).ready(function($) {
    $('#ob_mp4_cc_main_content').fadeIn('slow');
    
    var ob_mp4_cc_upload_form = $("#ob_mp4_cc_upload_form");
    var ob_mp4_cc_upload_form_ffmpeg_not_found = $("#ob_mp4_cc_upload_form_ffmpeg_not_found");
    var ob_mp4_cc_vid = $("#ob_mp4_cc_vid");
    var ob_mp4_cc_caption = $("#ob_mp4_cc_caption");
    
    ob_mp4_cc_vid.focus(function(e) {
        $('#ob_mp4_cc_incomplete_dialog').hide();
    });
    
    ob_mp4_cc_caption.focus(function(e) {
        $('#ob_mp4_cc_incomplete_dialog').hide();
    });
    //
    //// validation function for when FFmpeg is not being used
    ob_mp4_cc_upload_form.submit(function(e) {
        if (!validate_video() || !validate_caption()) {
            e.preventDefault();
            $('#ob_mp4_cc_incomplete_dialog').show();
        } else {
            $('input[type="submit"]').prop('disabled', true);
            $('#ob_mp4_cc_loading').show();
        }
    });
    
    ob_mp4_cc_upload_form_ffmpeg_not_found.submit(function(e) {
        e.preventDefault();
        $('#ob_mp4_cc_ffmpeg_not_found').show();
    });
    
    function validate_video() {
        // get the file name, possibly with path (depends on browser)
        var vid_name = ob_mp4_cc_vid.val();
        
        // format validity boolean
        var format_valid = true;
        
        // Use a regular expression to trim everything before final dot
        var vid_ext = vid_name.replace(/^.*\./, '');

        // check the validity of the extension
        if (vid_ext == '') {
            format_valid = false;
        } else {
            vid_ext = vid_ext.toLowerCase()
            if ((vid_ext == 'mov')
                || (vid_ext == 'mp4')
                || (vid_ext == 'flv')) {
                format_valid = true;
            } else {
                format_valid = false;
            }
        }
        
        if (vid_name.length > 0 && format_valid) {
            return true;
            
        } else {
            return false;
        }
    }
    
    // validate caption
    function validate_caption() {
        if (ob_mp4_cc_caption.val().length < 1) {
            return false;
        } else {
            return true;
        }
    }
});