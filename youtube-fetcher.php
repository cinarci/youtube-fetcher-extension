<?php 
/*
Plugin Name: YouTube Video Fetcher
Description: Fetches videos from a specific YouTube channel and embeds them in a post.
Version: 1.2
Author: Adem ÇINARCI
*/
add_filter('cron_schedules', 'yvf_add_half_hourly_cron_schedule');
function yvf_add_half_hourly_cron_schedule($schedules) {
    $schedules['half_hourly'] = array(
        'interval' => 1800, // 1800 saniye = 30 dakika
        'display' => __('Every Half Hour')
    );
    return $schedules;
}

register_activation_hook(__FILE__, 'yvf_activation');
function yvf_activation() {
    if (!wp_next_scheduled('yvf_check_for_new_videos')) {
        wp_schedule_event(time(), 'half_hourly', 'yvf_check_for_new_videos');
    }
}

register_deactivation_hook(__FILE__, 'yvf_deactivation');
function yvf_deactivation() {
    wp_clear_scheduled_hook('yvf_check_for_new_videos');
}

add_action('yvf_check_for_new_videos', 'yvf_fetch_and_create_posts');
function yvf_fetch_and_create_posts() {
    yvf_fetch_videos();
}

register_activation_hook(__FILE__, 'yvf_fetch_all_videos');
function yvf_fetch_all_videos() {
    yvf_fetch_videos();
}

function yvf_fetch_videos() {
    $api_key = 'APIKEY';
    $channel_id = 'CHANNELID';
    $category_id = 7;
    $api_url = 'https://www.googleapis.com/youtube/v3/search?order=date&part=snippet&channelId=' . $channel_id . '&maxResults=50&type=video&key=' . $api_key;

    $response = wp_remote_get($api_url);

    if (is_wp_error($response)) {
        return;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body);

    if (!empty($data->items)) {
        foreach ($data->items as $item) {
            $video_id = $item->id->videoId;
            $video_title = $item->snippet->title;
            $video_url = 'https://www.youtube.com/watch?v=' . $video_id;
            $embed_code = '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $video_id . '" frameborder="0" allowfullscreen></iframe>';
            $thumbnail_url = $item->snippet->thumbnails->high->url;

            $existing_post = get_posts(array(
                'meta_key' => 'yvf_video_id',
                'meta_value' => $video_id,
                'post_type' => 'post',
                'post_status' => 'publish',
                'numberposts' => 1
            ));

            if (empty($existing_post)) {
                $post_id = wp_insert_post(array(
                    'post_title' => $video_title,
                    'post_content' => $embed_code,
                    'post_status' => 'publish',
                    'post_type' => 'post',
                    'post_category' => array($category_id), 
                ));

                add_post_meta($post_id, 'yvf_video_id', $video_id, true);

                
                yvf_set_post_thumbnail($post_id, $thumbnail_url);
            }
        }
    }
}

function yvf_set_post_thumbnail($post_id, $thumbnail_url) {
    
    $image = wp_remote_get($thumbnail_url);

    if (is_wp_error($image)) {
        return;
    }

    $image_body = wp_remote_retrieve_body($image);
    $image_name = basename($thumbnail_url);
    $upload = wp_upload_bits($image_name, null, $image_body);

    if (!$upload['error']) {
        $file_path = $upload['file'];
        $file_name = basename($file_path);
        $wp_filetype = wp_check_filetype($file_name, null);

        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name($file_name),
            'post_content' => '',
            'post_status' => 'inherit',
        );

        $attach_id = wp_insert_attachment($attachment, $file_path, $post_id);
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
        wp_update_attachment_metadata($attach_id, $attach_data);
        set_post_thumbnail($post_id, $attach_id);
    }
}
?>