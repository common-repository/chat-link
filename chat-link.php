<?php
/*
Plugin Name: Chat Link
Plugin URI:   https://tebiko.com/chat-link
Description: Adds a chat link widget to your WordPress site.
Version: 1.1
Author: Tebiko
Author URI:   https://tebiko.com/
License:      GPL3
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined('ABSPATH')) {
    exit;
}

// Enqueue plugin styles and scripts
function chat_link_enqueue_styles() {
    if (!is_admin()) {
        wp_enqueue_style('chat-link-style', plugins_url('style.css', __FILE__), array(), '1.0.1');
    }
}
add_action('wp_enqueue_scripts', 'chat_link_enqueue_styles');

function chat_link_enqueue_admin_scripts($hook_suffix) {
    // Check if we are on the plugin settings page
    if ($hook_suffix === 'toplevel_page_chat-link') {
        wp_enqueue_media();
        wp_enqueue_script('chat-link-admin-script', plugins_url('admin.js', __FILE__), array(), '1.0.1', true);
        wp_enqueue_style('chat-link-admin-style', plugins_url('admin.css', __FILE__), array(), '1.0.1');
        

    }}
add_action('admin_enqueue_scripts', 'chat_link_enqueue_admin_scripts');

function chat_link_enqueue_scripts() {
    wp_enqueue_script('chat-link-frontend-script', plugins_url('frontend.js', __FILE__), array(), '1.0.1', true);
}
add_action('wp_enqueue_scripts', 'chat_link_enqueue_scripts');

// Add admin menu
function chat_link_admin_menu() {
    add_menu_page(
        'Chat Link Settings',
        'Chat Link',
        'manage_options',
        'chat-link',
        'chat_link_settings_page',
        'data:image/svg+xml;base64,' . base64_encode('<?xml version="1.0" encoding="iso-8859-1"?><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g><g><path d="M256,0C114.848,0,0,114.848,0,256c0,49.216,13.792,96.48,39.936,137.216L1.152,490.048 c-2.368,5.952-0.992,12.736,3.552,17.28C7.744,510.368,11.84,512,16,512c2.016,0,4-0.384,5.952-1.152l96.832-38.784 C159.52,498.208,206.784,512,256,512c141.152,0,256-114.848,256-256S397.152,0,256,0z M256,480 c-45.632,0-89.312-13.504-126.272-39.072c-2.688-1.888-5.888-2.848-9.088-2.848c-2.016,0-4.032,0.384-5.952,1.152l-69.952,28.032 l28.032-69.952c1.984-4.992,1.344-10.656-1.696-15.04C45.504,345.312,32,301.632,32,256C32,132.48,132.48,32,256,32 s224,100.48,224,224S379.52,480,256,480z"/></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>')
    );
}
add_action('admin_menu', 'chat_link_admin_menu');

// Display admin settings page
function chat_link_settings_page() {
    ?>
    <div class="wrap">
        <h1>Chat Link Settings</h1>
        <form method="post" action="options.php">
        <?php wp_nonce_field('chat_link_nonce_action', 'chat_link_nonce'); ?>

            <?php
            settings_fields('chat_link_settings_group');
            do_settings_sections('chat-link');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register settings
function chat_link_register_settings() {
    register_setting('chat_link_settings_group', 'chat_link_enabled');
    register_setting('chat_link_settings_group', 'chat_link_number', 'chat_link_validate_number');
    register_setting('chat_link_settings_group', 'chat_link_side');
    register_setting('chat_link_settings_group', 'chat_link_size');
    register_setting('chat_link_settings_group', 'chat_link_margin_side', array(
        'default' => 10,
    ));
    register_setting('chat_link_settings_group', 'chat_link_margin_bottom', array(
        'default' => 10,
    ));
    register_setting('chat_link_settings_group', 'chat_link_custom_message');
    register_setting('chat_link_settings_group', 'chat_link_custom_image');

    add_settings_section('chat_link_main_section', '', null, 'chat-link');

    add_settings_field('chat_link_enabled', 'Enable Chat', 'chat_link_enabled_callback', 'chat-link', 'chat_link_main_section');
    add_settings_field('chat_link_number', 'Whatsapp Number', 'chat_link_number_callback', 'chat-link', 'chat_link_main_section');
    add_settings_field('chat_link_side', 'Widget Position', 'chat_link_side_callback', 'chat-link', 'chat_link_main_section');
    add_settings_field('chat_link_size', 'Icon Size (px)', 'chat_link_size_callback', 'chat-link', 'chat_link_main_section');
    add_settings_field('chat_link_margin_side', 'Margin from Side', 'chat_link_margin_side_callback', 'chat-link', 'chat_link_main_section');
    add_settings_field('chat_link_margin_bottom', 'Margin from Bottom', 'chat_link_margin_bottom_callback', 'chat-link', 'chat_link_main_section');
    add_settings_field('chat_link_custom_message', 'Custom Message', 'chat_link_custom_message_callback', 'chat-link', 'chat_link_main_section');
    add_settings_field('chat_link_custom_image', 'Custom Chat Icon', 'chat_link_custom_image_callback', 'chat-link', 'chat_link_main_section');
}
add_action('admin_init', 'chat_link_register_settings');

// Callbacks for settings fields
function chat_link_enabled_callback() {
    $enabled = get_option('chat_link_enabled');
    echo '<input type="checkbox" id="chat_link_enabled" name="chat_link_enabled" value="1"' . checked(1, $enabled, false) . ' />';
}

function chat_link_number_callback() {
    $number = get_option('chat_link_number');
    echo '<input type="text" id="chat_link_number" name="chat_link_number" value="' . esc_attr($number) . '" />';
    echo '<p class="description">Please enter a valid phone number, including country code, with just numbers (e.g., 1234567890).</p>';
    echo '<p class="description" id="chat-number-description" style="color: red; display: none;">Please enter a valid phone number</p>';
}

function chat_link_side_callback() {
    $side = get_option('chat_link_side');
    echo '<select id="chat_link_side" name="chat_link_side">
            <option value="right"' . selected($side, 'right', false) . '>Right</option>
            <option value="left"' . selected($side, 'left', false) . '>Left</option>
          </select>';
}

function chat_link_size_callback() {
    $size = get_option('chat_link_size', 50);
    echo '<input type="number" id="chat_link_size" name="chat_link_size" value="' . esc_attr($size) . '" min="10" max="100" />';
}

function chat_link_margin_side_callback() {
    $margin_side = get_option('chat_link_margin_side', 10);
    echo '<input type="number" id="chat_link_margin_side" name="chat_link_margin_side" value="' . esc_attr($margin_side) . '" />';
}

function chat_link_margin_bottom_callback() {
    $margin_bottom = get_option('chat_link_margin_bottom', 10);
    echo '<input type="number" id="chat_link_margin_bottom" name="chat_link_margin_bottom" value="' . esc_attr($margin_bottom) . '" />';
}

function chat_link_custom_message_callback() {
    $custom_message = get_option('chat_link_custom_message');
    echo '<textarea id="chat_link_custom_message" name="chat_link_custom_message">' . esc_textarea($custom_message) . '</textarea>';
    echo '<p class="description">You can use [title] to include the page title and [url] to include the page URL.</p>';
}

function chat_link_custom_image_callback() {
    $custom_image = get_option('chat_link_custom_image');
    echo '<input type="hidden" id="chat_link_custom_image" name="chat_link_custom_image" value="' . esc_attr($custom_image) . '" />';
    echo '<button type="button" class="button" id="upload_image_button">Upload Image</button>';
    if ($custom_image) {
        echo '<div id="chat_link_custom_image_preview">';
        echo '<img src="' . esc_url($custom_image) . '" style="max-width:100px; max-height:100px; display:inline; margin-top:10px;" />';
        echo '<button type="button" class="button" id="remove_image_button" style="display:inline; margin-left:10px;">Remove Image</button>';
        echo '</div>';
    } else {
        echo '<div id="chat_link_custom_image_preview"></div>';
    }
}

function chat_link_save_custom_image($value) {
    if (!isset($_POST['chat_link_custom_image_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['chat_link_custom_image_nonce'])), 'chat_link_custom_image_nonce_action')) {
        // Nonce verification failed.
        return $value;
    }


    if (isset($_POST['chat_link_custom_image']) && empty($_POST['chat_link_custom_image'])) {
        return '';
    }

    return $value;
}
add_filter('pre_update_option_chat_link_custom_image', 'chat_link_save_custom_image');

// Validate Chat number
function chat_link_validate_number($number) {
    $cleaned_number = preg_replace('/[^0-9]/', '', $number);
    if (strlen($cleaned_number) < 10 || strlen($cleaned_number) > 15) {
        add_settings_error('chat_link_number', 'invalid_phone_number', 'Please enter a valid phone number', 'error');
        return get_option('chat_link_number');
    }
    return $cleaned_number;
}

// Display the Chat widget
function chat_link_display_widget() {
    if (!get_option('chat_link_enabled')) {
        return;
    }

    $number = get_option('chat_link_number');
    $side = get_option('chat_link_side', 'right');
    $size = get_option('chat_link_size', 50);
    $margin_side = get_option('chat_link_margin_side', 10);
    $margin_bottom = get_option('chat_link_margin_bottom', 10);
    $custom_message = get_option('chat_link_custom_message', 'Hello! I have a question.');
    $custom_image = get_option('chat_link_custom_image');

    global $wp;
    $current_title = wp_title('', false);
    $current_url = home_url(add_query_arg(array(), $wp->request));
    $custom_message = str_replace('[title]', $current_title, $custom_message);
    $custom_message = str_replace('[url]', $current_url, $custom_message);

    if ($number) {
        $default_image = plugins_url('whatsapp.svg', __FILE__);
        $icon = !$custom_image ? '<img src="' . esc_url($default_image) . '" alt="' . esc_attr('Chat Link') . '" style="width:' . esc_attr($size) . 'px; height:' . esc_attr($size) . 'px; object-fit: contain;" />' : '<img src="' . esc_url($custom_image) . '" alt="' . esc_attr('Chat Link') . '" style="width:' . esc_attr($size) . 'px; height:' . esc_attr($size) . 'px; object-fit: contain;" />';



        echo '<div id="chat-link-widget" style="position: fixed; ' . esc_attr($side) . ': ' . esc_attr($margin_side) . 'px; bottom: ' . esc_attr($margin_bottom) . 'px;">
                <a href="https://wa.me/' . esc_attr($number) . '?text=' . urlencode($custom_message) . '" target="_blank" id="chat-link">
                    ' . wp_kses_post($icon)  . '
                </a>
              </div>';
    }
}
add_action('wp_footer', 'chat_link_display_widget');

// Create a stylesheet and save it in the uploads directory
function chat_link_create_stylesheet() {
    global $wp_filesystem;

    // Ensure the filesystem API is available
    if ( empty( $wp_filesystem ) ) {
        require_once ABSPATH . '/wp-admin/includes/file.php';
        WP_Filesystem();
    }

    // Retrieve the uploads directory path
    $upload_dir = wp_upload_dir();
    $upload_path = trailingslashit($upload_dir['basedir']) . 'chat-link/';

    // Create the directory if it doesn't exist
    if (!file_exists($upload_path)) {
        wp_mkdir_p($upload_path);
    }

    // Define the file and CSS content
    $file = $upload_path . 'style.css';
    $css = "
#chat-link-widget {
    position: fixed;
}
#chat-link-widget img {
    cursor: pointer;
}
";

    // Write the CSS to the file in the uploads directory
    if ($wp_filesystem) {
        $wp_filesystem->put_contents($file, $css, FS_CHMOD_FILE);
    }
}
add_action('admin_init', 'chat_link_create_stylesheet');
?>
