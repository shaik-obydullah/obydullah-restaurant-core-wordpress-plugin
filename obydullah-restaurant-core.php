<?php
/**
 * Plugin Name: Obydullah Restaurant Core
 * Description: Core functionality for Obydullah Restaurant theme
 * Version:     1.0.0
 * Author:      Shaik Obydullah
 * Author URI:  https://obydullah.com
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: obydullah-restaurant-core
 * Domain Path: /languages
 *
 * ================================================================
 *                         INDEX
 * ================================================================
 * 1. Security & Constants
 * 2. Hero Slider CPT + Meta Boxes
 * 3. Chef's Special CPT + Meta Boxes (Single Instance)
 * 4. Menu Items CPT + Category Taxonomy + Meta Boxes
 * 5. Menu Area (single instance) + Meta Boxes
 * 6. Testimonials CPT + Meta Boxes
 * 7. Testimonial Area (single instance)
 * 8. Opening Hours CPT (single instance) + Repeater Hours
 * 9. Table Reservations (custom DB table, AJAX handler, admin list)
 * 10. Footer Settings (single instance) + Meta Boxes
 * 11. About Page (single instance) + Meta Boxes (story, philosophy, slider)
 * 12. Contact Page
 * 13. Contact Form 7 Support
 * ================================================================
 */

/* ======================================================
   1. Security & Constants
====================================================== */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'OBIRC_VERSION', '1.0.0' );
define( 'OBIRC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'OBIRC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once OBIRC_PLUGIN_DIR . 'includes/obirc-booking-list-table.php';

function obirc_add_admin_menu() {
    add_menu_page(
        'Restaurant Theme Core',
        'Restaurant Theme Core',
        'manage_options',
        'obirc-restaurant-core',
        'obirc_restaurant_core_page',                          
        'dashicons-editor-kitchensink',      
        59
    );
}
add_action( 'admin_menu', 'obirc_add_admin_menu', 9 );

function obirc_enqueue_dashboard_assets( $hook ) {
    if ( 'toplevel_page_obirc-restaurant-core' === $hook ) {
        wp_enqueue_style( 'obirc-dashboard-css', OBIRC_PLUGIN_URL . 'assets/css/admin-dashboard.css', array(), OBIRC_VERSION );
    }
}
add_action( 'admin_enqueue_scripts', 'obirc_enqueue_dashboard_assets' );

function obirc_restaurant_core_page() {
    $sections = array(
        'hero_slides' => array(
            'title' => __( 'Hero Slides', 'obydullah-restaurant-core' ),
            'url'   => admin_url( 'edit.php?post_type=obirc_hero_slide' ),
            'icon'  => 'dashicons-images-alt2',
        ),
        'chef_specials' => array(
            'title' => __( 'Chef\'s Specials', 'obydullah-restaurant-core' ),
            'url'   => admin_url( 'edit.php?post_type=obirc_chef_special' ),
            'icon'  => 'dashicons-media-text',
        ),
        'menu_items' => array(
            'title' => __( 'Menu Items', 'obydullah-restaurant-core' ),
            'url'   => admin_url( 'edit.php?post_type=obirc_menu_item' ),
            'icon'  => 'dashicons-food',
        ),
        'menu_area' => array(
            'title' => __( 'Menu Area', 'obydullah-restaurant-core' ),
            'url'   => admin_url( 'edit.php?post_type=obirc_menu_area' ),
            'icon'  => 'dashicons-menu',
        ),
        'testimonials' => array(
            'title' => __( 'Testimonials', 'obydullah-restaurant-core' ),
            'url'   => admin_url( 'edit.php?post_type=obirc_testimonial' ),
            'icon'  => 'dashicons-format-quote',
        ),
        'testimonial_area' => array(
            'title' => __( 'Testimonial Area', 'obydullah-restaurant-core' ),
            'url'   => admin_url( 'edit.php?post_type=obirc_testi_area' ),
            'icon'  => 'dashicons-menu',
        ),
        'opening_hours' => array(
            'title' => __( 'Opening Hours', 'obydullah-restaurant-core' ),
            'url'   => admin_url( 'edit.php?post_type=obirc_opening_hours' ),
            'icon'  => 'dashicons-clock',
        ),
        'bookings' => array(
            'title' => __( 'Table Bookings', 'obydullah-restaurant-core' ),
            'url'   => admin_url( 'admin.php?page=obirc-bookings' ),
            'icon'  => 'dashicons-calendar-alt',
        ),
        'footer_settings' => array(
            'title' => __( 'Footer Settings', 'obydullah-restaurant-core' ),
            'url'   => admin_url( 'edit.php?post_type=obirc_footer' ),
            'icon'  => 'dashicons-layout',
        ),
        'about_page' => array(
            'title' => __( 'About Page', 'obydullah-restaurant-core' ),
            'url'   => admin_url( 'edit.php?post_type=obirc_about_page' ),
            'icon'  => 'dashicons-info',
        ),
        'contact_page' => array(
            'title' => __( 'Contact Page', 'obydullah-restaurant-core' ),
            'url'   => admin_url( 'edit.php?post_type=obirc_contact_page' ),
            'icon'  => 'dashicons-email',
        ),
    );
    ?>
<div class="wrap obirc-dashboard">
    <h1><?php esc_html_e( 'Restaurant Theme Core', 'obydullah-restaurant-core' ); ?></h1>
    <p class="obirc-dashboard-description">
        <?php esc_html_e( 'Welcome to the Restaurant Theme Core plugin. Use the links below to manage your restaurant content.', 'obydullah-restaurant-core' ); ?>
    </p>

    <div class="obirc-dashboard-grid">
        <?php foreach ( $sections as $section ) : ?>
        <div class="obirc-dashboard-card">
            <div class="dashicons <?php echo esc_attr( $section['icon'] ); ?>"></div>
            <h2><?php echo esc_html( $section['title'] ); ?></h2>
            <a href="<?php echo esc_url( $section['url'] ); ?>"
                class="button button-primary"><?php esc_html_e( 'Manage', 'obydullah-restaurant-core' ); ?></a>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php
}

/* ======================================================
   2. Hero Slider CPT + Meta Boxes
====================================================== */

function obirc_register_hero_slide_cpt() {
    register_post_type( 'obirc_hero_slide', array(
        'labels' => array(
            'name'          => __( 'Hero Slides', 'obydullah-restaurant-core' ),
            'singular_name' => __( 'Hero Slide', 'obydullah-restaurant-core' ),
            'add_new_item'  => __( 'Add New Hero Slide', 'obydullah-restaurant-core' ),
            'edit_item'     => __( 'Edit Hero Slide', 'obydullah-restaurant-core' ),
        ),
        'public'        => true,
        'show_in_menu'  => 'obirc-restaurant-core',
        'menu_icon'     => 'dashicons-images-alt2',
        'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
        'show_in_rest'  => true,
        'has_archive'   => false,
        'rewrite'       => array( 'slug' => 'obirc-hero-slide' ),
    ) );
}
add_action( 'init', 'obirc_register_hero_slide_cpt' );

function obirc_add_hero_slide_meta_box() {
    add_meta_box(
        'obirc_hero_slide_meta',
        __( 'Hero Slide Settings', 'obydullah-restaurant-core' ),
        'obirc_render_hero_slide_meta_box',
        'obirc_hero_slide',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'obirc_add_hero_slide_meta_box' );


function obirc_render_hero_slide_meta_box( $post ) {
    $subtitle = get_post_meta( $post->ID, 'obirc_subtitle', true );
    wp_nonce_field( 'obirc_save_hero_slide_meta', 'obirc_hero_slide_nonce' );
    ?>
<p>
    <label
        for="obirc_subtitle"><strong><?php esc_html_e( 'Subtitle', 'obydullah-restaurant-core' ); ?></strong></label><br>
    <input type="text" id="obirc_subtitle" name="obirc_subtitle" value="<?php echo esc_attr( $subtitle ); ?>"
        class="widefat">
</p>
<?php
}

function obirc_save_hero_slide_meta( $post_id ) {
    if ( ! isset( $_POST['obirc_hero_slide_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['obirc_hero_slide_nonce'] ) ), 'obirc_save_hero_slide_meta' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( 'obirc_hero_slide' !== get_post_type( $post_id ) ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['obirc_subtitle'] ) ) {
        update_post_meta( $post_id, 'obirc_subtitle', sanitize_text_field( wp_unslash( $_POST['obirc_subtitle'] ) ) );
    }
}
add_action( 'save_post_obirc_hero_slide', 'obirc_save_hero_slide_meta' );


/* ======================================================
   3. Chef's Special CPT + Meta Boxes (Single Instance)
====================================================== */

function obirc_register_chef_special_cpt() {
    register_post_type( 'obirc_chef_special', array(
        'labels' => array(
            'name'          => __( 'Chef\'s Special', 'obydullah-restaurant-core' ),
            'singular_name' => __( 'Chef\'s Special', 'obydullah-restaurant-core' ),
            'add_new_item'  => __( 'Edit Chef\'s Special', 'obydullah-restaurant-core' ),
            'edit_item'     => __( 'Edit Chef\'s Special', 'obydullah-restaurant-core' ),
            'new_item'      => __( 'Edit Chef\'s Special', 'obydullah-restaurant-core' ),
        ),
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => 'obirc-restaurant-core',
        'menu_icon'       => 'dashicons-media-text',
        'supports'        => array( 'title', 'thumbnail' ),
        'show_in_rest'    => true,
        'capability_type' => 'post',
        'map_meta_cap'    => true,
    ) );
}
add_action( 'init', 'obirc_register_chef_special_cpt' );

function obirc_limit_chef_special() {
    global $pagenow;
    if ( $pagenow === 'post-new.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'obirc_chef_special' ) {
        $existing = get_posts( array(
            'post_type'      => 'obirc_chef_special',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'fields'         => 'ids',
        ) );
        if ( ! empty( $existing ) ) {
            $post_id = $existing[0];
            if ( current_user_can( 'edit_post', $post_id ) ) {
                wp_redirect( admin_url( 'post.php?post=' . $post_id . '&action=edit' ) );
                exit;
            }
        }
    }
}
add_action( 'admin_init', 'obirc_limit_chef_special' );

function obirc_add_chef_special_meta_box() {
    add_meta_box(
        'obirc_chef_special_meta',
        __( 'Chef\'s Special Settings', 'obydullah-restaurant-core' ),
        'obirc_render_chef_special_meta_box',
        'obirc_chef_special',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'obirc_add_chef_special_meta_box' );

function obirc_render_chef_special_meta_box( $post ) {
    $subtitle = get_post_meta( $post->ID, 'obirc_subtitle', true );
    $body     = get_post_meta( $post->ID, 'obirc_body', true );
    wp_nonce_field( 'obirc_save_chef_special_meta', 'obirc_chef_special_nonce' );
    ?>
<p>
    <label
        for="obirc_subtitle"><strong><?php esc_html_e( 'Subtitle', 'obydullah-restaurant-core' ); ?></strong></label><br>
    <input type="text" id="obirc_subtitle" name="obirc_subtitle" value="<?php echo esc_attr( $subtitle ); ?>"
        class="widefat">
</p>
<p>
    <label for="obirc_body"><strong><?php esc_html_e( 'Body', 'obydullah-restaurant-core' ); ?></strong></label><br>
    <textarea id="obirc_body" name="obirc_body" rows="5"
        class="large-text"><?php echo esc_textarea( $body ); ?></textarea>
</p>
<?php
}

function obirc_save_chef_special_meta( $post_id ) {
    if ( ! isset( $_POST['obirc_chef_special_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['obirc_chef_special_nonce'] ) ), 'obirc_save_chef_special_meta' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( 'obirc_chef_special' !== get_post_type( $post_id ) ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['obirc_subtitle'] ) ) {
        update_post_meta( $post_id, 'obirc_subtitle', sanitize_text_field( wp_unslash( $_POST['obirc_subtitle'] ) ) );
    }

    if ( isset( $_POST['obirc_body'] ) ) {
        update_post_meta( $post_id, 'obirc_body', sanitize_textarea_field( wp_unslash( $_POST['obirc_body'] ) ) );
    }
}
add_action( 'save_post_obirc_chef_special', 'obirc_save_chef_special_meta' );

/* ======================================================
   4. Menu Items CPT + Category Taxonomy + Meta Boxes
====================================================== */

function obirc_register_menu_item() {
    register_post_type( 'obirc_menu_item', array(
          'labels'      => array(
            'name'          => __( 'Menu Items', 'obydullah-restaurant-core' ),
            'singular_name' => __( 'Menu Item', 'obydullah-restaurant-core' ),
            'add_new_item'  => __( 'Add New Menu Item', 'obydullah-restaurant-core' ),
            'edit_item'     => __( 'Edit Menu Item', 'obydullah-restaurant-core' ),
        ),
        'public'      => true,
        'show_in_menu'        => 'obirc-restaurant-core',
        'menu_icon'   => 'dashicons-food',
        'supports'    => array( 'title', 'thumbnail' ),
        'show_in_rest'=> true,
    ) );
}
add_action( 'init', 'obirc_register_menu_item' );

function obirc_register_menu_category() {
    register_taxonomy( 'obirc_menu_category', 'obirc_menu_item', array(
        'labels' => array(
            'name'              => __( 'Categories', 'obydullah-restaurant-core' ),
            'singular_name'     => __( 'Category', 'obydullah-restaurant-core' ),
            'add_new_item'      => __( 'Add New Category', 'obydullah-restaurant-core' ),
            'new_item_name'     => __( 'New Category Name', 'obydullah-restaurant-core' ),
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
    ) );
}
add_action( 'init', 'obirc_register_menu_category' );

function obirc_add_menu_item_subtitle_meta_box() {
    add_meta_box( 'obirc_menu_item_subtitle', __( 'Subtitle', 'obydullah-restaurant-core' ), 'obirc_subtitle_callback', 'obirc_menu_item', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'obirc_add_menu_item_subtitle_meta_box' );

function obirc_subtitle_callback( $post ) {
    wp_nonce_field( 'obirc_menu_item_meta', 'obirc_menu_item_nonce' );
    $subtitle = get_post_meta( $post->ID, 'obirc_menu_subtitle', true );
    echo '<textarea name="obirc_menu_subtitle" rows="2" class="large-text">' . esc_textarea( $subtitle ) . '</textarea>';
}

function obirc_add_price_meta_box() {
    add_meta_box( 'obirc_menu_price', __( 'Price', 'obydullah-restaurant-core' ), 'obirc_price_callback', 'obirc_menu_item', 'side', 'default' );
}
add_action( 'add_meta_boxes', 'obirc_add_price_meta_box' );


function obirc_price_callback( $post ) {
    wp_nonce_field( 'obirc_menu_item_meta', 'obirc_menu_item_nonce' );
    $price = get_post_meta( $post->ID, 'obirc_menu_price', true );
    echo '<input type="text" name="obirc_menu_price" value="' . esc_attr( $price ) . '" class="widefat" placeholder="' . esc_attr__( '$48', 'obydullah-restaurant-core' ) . '">';
}

function obirc_save_menu_item_meta( $post_id ) {
    if ( ! isset( $_POST['obirc_menu_item_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['obirc_menu_item_nonce'] ) ), 'obirc_menu_item_meta' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    if ( 'obirc_menu_item' !== get_post_type( $post_id ) ) {
        return;
    }
    if ( isset( $_POST['obirc_menu_subtitle'] ) ) {
        update_post_meta( $post_id, 'obirc_menu_subtitle', sanitize_textarea_field( wp_unslash( $_POST['obirc_menu_subtitle'] ) ) );
    }
    if ( isset( $_POST['obirc_menu_price'] ) ) {
        update_post_meta( $post_id, 'obirc_menu_price', sanitize_text_field( wp_unslash( $_POST['obirc_menu_price'] ) ) );
    }
}
add_action( 'save_post_obirc_menu_item', 'obirc_save_menu_item_meta' );


/* ======================================================
   5. Menu Area (Single Instance) + Meta Boxes
====================================================== */

function obirc_register_menu_area() {
    register_post_type( 'obirc_menu_area', array(
        'labels'        => array(
            'name'          => __( 'Menu Area', 'obydullah-restaurant-core' ),
            'singular_name' => __( 'Menu Area', 'obydullah-restaurant-core' ),
            'add_new_item'  => __( 'Edit Menu Area', 'obydullah-restaurant-core' ),
            'edit_item'     => __( 'Edit Menu Area', 'obydullah-restaurant-core' ),
        ),
        'public'           => false,
        'show_ui'          => true,
        'show_in_menu'     => 'obirc-restaurant-core',
        'menu_icon'        => 'dashicons-menu',
        'supports'         => array( 'title', 'thumbnail' ),
        'show_in_rest'     => true,
        'capability_type'  => 'post',
        'map_meta_cap'     => true,
    ) );
}
add_action( 'init', 'obirc_register_menu_area' );

function obirc_limit_menu_area() {
    global $pagenow;
if ( $pagenow === 'post-new.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'obirc_menu_area' ) {
        $existing = get_posts( array(
            'post_type'      => 'obirc_menu_area',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'fields'         => 'ids', 
        ) );
        if ( ! empty( $existing ) ) {
            $post_id = $existing[0];
            if ( current_user_can( 'edit_post', $post_id ) ) {
                wp_redirect( admin_url( 'post.php?post=' . $post_id . '&action=edit' ) );
                exit;
            }
        }
    }
}
add_action( 'admin_init', 'obirc_limit_menu_area' );

function obirc_add_menu_area_subtitle_meta() {
    add_meta_box(
        'menu_area_subtitle',
        __( 'Subtitle', 'obydullah-restaurant-core' ),
        'obirc_menu_area_subtitle_callback',
        'obirc_menu_area',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'obirc_add_menu_area_subtitle_meta' );

function obirc_menu_area_subtitle_callback( $post ) {
    wp_nonce_field( 'obirc_menu_area_meta', 'obirc_menu_area_nonce' );
    $subtitle = get_post_meta( $post->ID, 'obirc_menu_area_subtitle', true );
    echo '<textarea name="obirc_menu_area_subtitle" rows="2" class="large-text">' . esc_textarea( $subtitle ) . '</textarea>';
}

function obirc_save_menu_area_meta( $post_id ) {
    if ( ! isset( $_POST['obirc_menu_area_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['obirc_menu_area_nonce'] ) ), 'obirc_menu_area_meta' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    if ( get_post_type( $post_id ) !== 'obirc_menu_area' ) {
        return;
    }
    if ( isset( $_POST['obirc_menu_area_subtitle'] ) ) {
        update_post_meta( $post_id, 'obirc_menu_area_subtitle', sanitize_textarea_field( wp_unslash( $_POST['obirc_menu_area_subtitle'] ) ) );
    }
}
add_action( 'save_post_obirc_menu_area', 'obirc_save_menu_area_meta' );

/* ======================================================
   6. Testimonials CPT + Meta Boxes
====================================================== */

function obirc_register_testimonial() {
    register_post_type( 'obirc_testimonial', array(
        'labels' => array(
            'name'          => __( 'Testimonials', 'obydullah-restaurant-core' ),
            'singular_name' => __( 'Testimonial', 'obydullah-restaurant-core' ),
            'add_new_item'  => __( 'Add New Testimonial', 'obydullah-restaurant-core' ),
            'edit_item'     => __( 'Edit Testimonial', 'obydullah-restaurant-core' ),
        ),
        'public'          => true,
        'show_in_menu'    => 'obirc-restaurant-core',
        'menu_icon'       => 'dashicons-format-quote',
        'supports'        => array( 'title' ),
        'show_in_rest'    => true,                   
        'has_archive'     => false,
        'publicly_queryable' => true,
    ) );
}
add_action( 'init', 'obirc_register_testimonial' );

function obirc_add_testimonial_meta_boxes() {
    add_meta_box(
        'obirc_testimonial_quote',
        __( 'Quote', 'obydullah-restaurant-core' ),
        'obirc_testimonial_quote_callback',
        'obirc_testimonial',
        'normal',
        'high'
    );
    add_meta_box(
        'obirc_testimonial_role',
        __( 'Role / Title', 'obydullah-restaurant-core' ),
        'obirc_testimonial_role_callback',
        'obirc_testimonial',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'obirc_add_testimonial_meta_boxes' );

function obirc_testimonial_quote_callback( $post ) {
    wp_nonce_field( 'obirc_testimonial_meta', 'obirc_testimonial_nonce' );
    $quote = get_post_meta( $post->ID, 'obirc_testimonial_quote', true );
    echo '<textarea name="obirc_testimonial_quote" rows="4" class="large-text">' . esc_textarea( $quote ) . '</textarea>';
}

function obirc_testimonial_role_callback( $post ) {
    $role = get_post_meta( $post->ID, 'obirc_testimonial_role', true );
    echo '<input type="text" name="obirc_testimonial_role" value="' . esc_attr( $role ) . '" class="widefat" placeholder="' . esc_attr__( 'e.g., Food Critic', 'obydullah-restaurant-core' ) . '">';
}

function obirc_save_testimonial_meta( $post_id ) {
    if ( ! isset( $_POST['obirc_testimonial_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['obirc_testimonial_nonce'] ) ), 'obirc_testimonial_meta' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( get_post_type( $post_id ) !== 'obirc_testimonial' ) {
        return;
    }

    if ( isset( $_POST['obirc_testimonial_quote'] ) ) {
        update_post_meta( $post_id, 'obirc_testimonial_quote', sanitize_textarea_field( wp_unslash( $_POST['obirc_testimonial_quote'] ) ) );
    }

    if ( isset( $_POST['obirc_testimonial_role'] ) ) {
        update_post_meta( $post_id, 'obirc_testimonial_role', sanitize_text_field( wp_unslash( $_POST['obirc_testimonial_role'] ) ) );
    }
}
add_action( 'save_post_obirc_testimonial', 'obirc_save_testimonial_meta' );


/* ======================================================
   7. Testimonial Area (Single Instance)
====================================================== */

function obirc_register_testimonial_area() {
    register_post_type( 'obirc_testi_area', array(
        'labels'        => array(
            'name'          => __( 'Testimonial Area', 'obydullah-restaurant-core' ),
            'singular_name' => __( 'Testimonial Area', 'obydullah-restaurant-core' ),
            'add_new_item'  => __( 'Edit Testimonial Area', 'obydullah-restaurant-core' ),
            'edit_item'     => __( 'Edit Testimonial Area', 'obydullah-restaurant-core' ),
        ),
        'public'           => false,
        'show_ui'          => true,
        'show_in_menu'     => 'obirc-restaurant-core',
        'menu_icon'        => 'dashicons-menu',
        'supports'         => array( 'title', 'thumbnail' ),
        'show_in_rest'     => true,
        'capability_type'  => 'post',
        'map_meta_cap'     => true,
    ) );
}
add_action( 'init', 'obirc_register_testimonial_area' );


function obirc_limit_testimonial_area() {
    global $pagenow;
if ( $pagenow === 'post-new.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'obirc_testi_area' ) {
        $existing = get_posts( array(
            'post_type'      => 'obirc_testi_area',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'fields'         => 'ids', 
        ) );
        if ( ! empty( $existing ) ) {
            $post_id = $existing[0];
            if ( current_user_can( 'edit_post', $post_id ) ) {
                wp_redirect( admin_url( 'post.php?post=' . $post_id . '&action=edit' ) );
                exit;
            }
        }
    }
}
add_action( 'admin_init', 'obirc_limit_testimonial_area' );

/* ======================================================
   8. Opening Hours (Single Instance) + Repeater Hours
====================================================== */

function obirc_register_opening_hours_cpt() {
    register_post_type( 'obirc_opening_hours', array(
        'labels' => array(
            'name'          => __( 'Opening Hours', 'obydullah-restaurant-core' ),
            'singular_name' => __( 'Opening Hours', 'obydullah-restaurant-core' ),
            'add_new_item'  => __( 'Edit Opening Hours', 'obydullah-restaurant-core' ),
            'edit_item'     => __( 'Edit Opening Hours', 'obydullah-restaurant-core' ),
        ),
        'public'           => false,
        'show_ui'          => true,
        'show_in_menu'     => 'obirc-restaurant-core',
        'menu_icon'        => 'dashicons-clock',
        'supports'         => array( 'title' ),
        'show_in_rest'     => true,
        'capability_type'  => 'post',
        'map_meta_cap'     => true,
    ) );
}
add_action( 'init', 'obirc_register_opening_hours_cpt' );

function obirc_limit_opening_hours() {
    global $pagenow;
    if ( $pagenow === 'post-new.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'obirc_opening_hours' ) {
        $existing = get_posts( array(
            'post_type'      => 'obirc_opening_hours',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'fields'         => 'ids',
        ) );
        if ( ! empty( $existing ) ) {
            $post_id = $existing[0];
            if ( current_user_can( 'edit_post', $post_id ) ) {
                wp_redirect( admin_url( 'post.php?post=' . $post_id . '&action=edit' ) );
                exit;
            }
        }
    }
}
add_action( 'admin_init', 'obirc_limit_opening_hours' );

function obirc_enqueue_opening_hours_assets( $hook ) {
    global $post_type;
    if ( 'obirc_opening_hours' === $post_type && in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
        wp_enqueue_style(
            'obirc-admin-css',
            OBIRC_PLUGIN_URL . 'assets/css/opening-hours.css',
            array(),
            OBIRC_VERSION
        );
        wp_enqueue_script(
            'obirc-admin-js',
            OBIRC_PLUGIN_URL . 'assets/js/opening-hours.js',
            array( 'jquery' ),
            OBIRC_VERSION,
            true
        );
        wp_localize_script( 'obirc-admin-js', 'obirc_opening_hours', array(
            'dayPlaceholder' => __( 'Day(s)', 'obydullah-restaurant-core' ),
            'timePlaceholder' => __( 'Time', 'obydullah-restaurant-core' ),
            'removeText'      => __( 'Remove', 'obydullah-restaurant-core' ),
        ) );
    }
}
add_action( 'admin_enqueue_scripts', 'obirc_enqueue_opening_hours_assets' );

function obirc_add_opening_hours_meta_boxes() {
    add_meta_box(
        'obirc_opening_hours_repeater',
        __( 'Opening Hours', 'obydullah-restaurant-core' ),
        'obirc_render_opening_hours_repeater',
        'obirc_opening_hours',
        'normal',
        'high'
    );
    add_meta_box(
        'obirc_opening_hours_note',
        __( 'Note', 'obydullah-restaurant-core' ),
        'obirc_render_opening_hours_note',
        'obirc_opening_hours',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'obirc_add_opening_hours_meta_boxes' );

function obirc_render_opening_hours_repeater( $post ) {
    $hours = get_post_meta( $post->ID, 'obirc_opening_hours', true );
    if ( ! is_array( $hours ) ) {
        $hours = array(
            array( 'day' => __( 'Monday – Thursday', 'obydullah-restaurant-core' ), 'time' => __( '5 PM – 10 PM', 'obydullah-restaurant-core' ) ),
            array( 'day' => __( 'Friday', 'obydullah-restaurant-core' ), 'time' => __( '5 PM – 11 PM', 'obydullah-restaurant-core' ) ),
            array( 'day' => __( 'Saturday', 'obydullah-restaurant-core' ), 'time' => __( '12 PM – 11 PM', 'obydullah-restaurant-core' ) ),
            array( 'day' => __( 'Sunday', 'obydullah-restaurant-core' ), 'time' => __( '12 PM – 9 PM', 'obydullah-restaurant-core' ) ),
        );
    }
    wp_nonce_field( 'obirc_save_opening_hours', 'obirc_opening_hours_nonce' );
    ?>
<div id="obirc-hours-repeater">
    <?php foreach ( $hours as $item ) : ?>
    <div class="obirc-hours-row">
        <input type="text" name="obirc_hours_day[]" class="obirc-hours-day"
            value="<?php echo esc_attr( $item['day'] ); ?>"
            placeholder="<?php esc_attr_e( 'Day(s)', 'obydullah-restaurant-core' ); ?>">
        <input type="text" name="obirc_hours_time[]" class="obirc-hours-time"
            value="<?php echo esc_attr( $item['time'] ); ?>"
            placeholder="<?php esc_attr_e( 'Time', 'obydullah-restaurant-core' ); ?>">
        <button type="button"
            class="button obirc-remove-row"><?php esc_html_e( 'Remove', 'obydullah-restaurant-core' ); ?></button>
    </div>
    <?php endforeach; ?>
</div>
<button type="button" id="obirc-add-hours-row"
    class="button"><?php esc_html_e( 'Add new row', 'obydullah-restaurant-core' ); ?></button>
<?php
}

function obirc_render_opening_hours_note( $post ) {
    $note = get_post_meta( $post->ID, 'obirc_opening_hours_note', true );

    wp_nonce_field( 'obirc_save_opening_hours', 'obirc_opening_hours_nonce' );
    ?>
<textarea name="obirc_opening_hours_note" class="obirc-hours-note-textarea"
    rows="3"><?php echo esc_textarea( $note ); ?></textarea>
<p class="description">
    <?php esc_html_e( 'E.g., “Last reservation 30 minutes before closing”', 'obydullah-restaurant-core' ); ?></p>
<?php
}

function obirc_save_opening_hours_meta( $post_id ) {
    if ( ! isset( $_POST['obirc_opening_hours_nonce'] ) ||
         ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['obirc_opening_hours_nonce'] ) ), 'obirc_save_opening_hours' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( get_post_type( $post_id ) !== 'obirc_opening_hours' ) {
        return;
    }

    if ( isset( $_POST['obirc_hours_day'] ) && isset( $_POST['obirc_hours_time'] ) ) {
        $days  = array_map( 'sanitize_text_field', wp_unslash( $_POST['obirc_hours_day'] ) );
        $times = array_map( 'sanitize_text_field', wp_unslash( $_POST['obirc_hours_time'] ) );
        $hours = array();
        $count = count( $days );
        for ( $i = 0; $i < $count; $i++ ) {
            if ( ! empty( $days[ $i ] ) && ! empty( $times[ $i ] ) ) {
                $hours[] = array( 'day' => $days[ $i ], 'time' => $times[ $i ] );
            }
        }
        update_post_meta( $post_id, 'obirc_opening_hours', $hours );
    } else {
        update_post_meta( $post_id, 'obirc_opening_hours', array() );
    }

    if ( isset( $_POST['obirc_opening_hours_note'] ) ) {
        update_post_meta( $post_id, 'obirc_opening_hours_note', sanitize_textarea_field( wp_unslash( $_POST['obirc_opening_hours_note'] ) ) );
    }
}
add_action( 'save_post_obirc_opening_hours', 'obirc_save_opening_hours_meta' );


/* ===================================================================
   9. Table Reservations (Custom DB table, AJAX handler, Admin List)
======================================================================= */

function obirc_create_booking_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'obirc_restaurant_booking';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        email varchar(100) NOT NULL,
        phone varchar(50) NOT NULL,
        party tinyint(2) NOT NULL,
        booking_date date NOT NULL,
        booking_time time NOT NULL,
        notes text,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}
register_activation_hook( __FILE__, 'obirc_create_booking_table' );

function obirc_drop_booking_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'obirc_restaurant_booking';
    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
}
register_uninstall_hook( __FILE__, 'obirc_drop_booking_table' );


function obirc_handle_booking_submission() {
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'obirc_booking_nonce' ) ) {
        wp_send_json_error( array( 'error' => __( 'Security check failed.', 'obydullah-restaurant-core' ) ), 403 );
    }

    $name    = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
    $email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
    $phone   = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
    $party   = intval( $_POST['party'] ?? 0 );
    $date    = sanitize_text_field( wp_unslash( $_POST['date'] ?? '' ) );
    $time    = sanitize_text_field( wp_unslash( $_POST['time'] ?? '' ) );
    $notes   = sanitize_textarea_field( wp_unslash( $_POST['notes'] ?? '' ) );

    $errors = array();
    if ( empty( $name ) ) {
        $errors[] = __( 'Name is required.', 'obydullah-restaurant-core' );
    }
    if ( ! is_email( $email ) ) {
        $errors[] = __( 'Valid email is required.', 'obydullah-restaurant-core' );
    }
    if ( empty( $phone ) ) {
        $errors[] = __( 'Phone number is required.', 'obydullah-restaurant-core' );
    }
    if ( $party < 1 ) {
        $errors[] = __( 'Party size must be at least 1.', 'obydullah-restaurant-core' );
    }
    if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
        $errors[] = __( 'Invalid date format.', 'obydullah-restaurant-core' );
    }
    if ( ! preg_match( '/^\d{2}:\d{2}$/', $time ) ) {
        $errors[] = __( 'Invalid time format.', 'obydullah-restaurant-core' );
    }

    if ( ! empty( $errors ) ) {
        wp_send_json_error( array( 'errors' => $errors ) );
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'obirc_restaurant_booking';

    $result = $wpdb->insert(
        $table_name,
        array(
            'name'         => $name,
            'email'        => $email,
            'phone'        => $phone,
            'party'        => $party,
            'booking_date' => $date,
            'booking_time' => $time,
            'notes'        => $notes,
        ),
        array( '%s', '%s', '%s', '%d', '%s', '%s', '%s' )
    );

    if ( false === $result ) {
        wp_send_json_error( array( 'error' => __( 'Database error. Please try again.', 'obydullah-restaurant-core' ) ) );
    }

    wp_send_json_success( array( 'message' => __( 'Your reservation has been submitted. We’ll contact you shortly.', 'obydullah-restaurant-core' ) ) );
}
add_action( 'wp_ajax_obirc_booking', 'obirc_handle_booking_submission' );
add_action( 'wp_ajax_nopriv_obirc_booking', 'obirc_handle_booking_submission' );

function obirc_bookings_admin_menu() {
    add_submenu_page(
        'obirc-restaurant-core',
        __( 'Table Bookings', 'obydullah-restaurant-core' ),
        __( 'Table Bookings', 'obydullah-restaurant-core' ),
        'manage_options',
        'obirc-bookings',
        'obirc_render_bookings_page'
    );
}
add_action( 'admin_menu', 'obirc_bookings_admin_menu' );

function obirc_render_bookings_page() {
    if ( isset( $_POST['action'] ) && 'delete' === $_POST['action'] && isset( $_POST['booking_ids'] ) ) {
        check_admin_referer( 'bulk-bookings' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized.', 'obydullah-restaurant-core' ) );
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'obirc_restaurant_booking';
        $ids = array_map( 'intval', $_POST['booking_ids'] );
        if ( ! empty( $ids ) ) {
            $placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
            $wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE id IN ($placeholders)", $ids ) );
            echo '<div class="notice notice-success"><p>' . esc_html__( 'Bookings deleted.', 'obydullah-restaurant-core' ) . '</p></div>';
        }
    }

    if ( ! class_exists( 'OBIRC_Booking_List_Table' ) ) {
        require_once OBIRC_PLUGIN_DIR . 'includes/obirc-booking-list-table.php';
    }

    $bookings_table = new OBIRC_Booking_List_Table();
    $bookings_table->prepare_items();
    ?>
<div class="wrap">
    <h1><?php esc_html_e( 'Table Reservations', 'obydullah-restaurant-core' ); ?></h1>
    <form method="post">
        <?php $bookings_table->display(); ?>
        <?php wp_nonce_field( 'bulk-bookings' ); ?>
    </form>
</div>
<?php
}

/* ======================================================
   10. Footer Settings (Single Instance) + Meta Boxes
====================================================== */

function obirc_register_footer_settings() {
    register_post_type( 'obirc_footer', array(
        'labels' => array(
            'name'          => __( 'Footer Settings', 'obydullah-restaurant-core' ),
            'singular_name' => __( 'Footer Settings', 'obydullah-restaurant-core' ),
            'add_new_item'  => __( 'Edit Footer Settings', 'obydullah-restaurant-core' ),
            'edit_item'     => __( 'Edit Footer Settings', 'obydullah-restaurant-core' ),
        ),
        'public'           => false,
        'show_ui'          => true,
        'show_in_menu'     => 'obirc-restaurant-core',
        'menu_icon'        => 'dashicons-layout',
        'supports'         => array( 'title' ),
        'show_in_rest'     => true,
        'capability_type'  => 'post',
        'map_meta_cap'     => true,
    ) );
}
add_action( 'init', 'obirc_register_footer_settings' );

function obirc_limit_footer_settings() {
    global $pagenow;
    if ( $pagenow === 'post-new.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'obirc_footer' ) {
        $existing = get_posts( array(
            'post_type'      => 'obirc_footer',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'fields'         => 'ids',
        ) );
        if ( ! empty( $existing ) ) {
            $post_id = $existing[0];
            if ( current_user_can( 'edit_post', $post_id ) ) {
                wp_redirect( admin_url( 'post.php?post=' . $post_id . '&action=edit' ) );
                exit;
            }
        }
    }
}
add_action( 'admin_init', 'obirc_limit_footer_settings' );

function obirc_add_footer_meta_boxes() {
    add_meta_box( 'obirc_footer_logo', __( 'Logo & Tagline', 'obydullah-restaurant-core' ), 'obirc_footer_logo_callback', 'obirc_footer', 'normal', 'high' );
    add_meta_box( 'obirc_footer_social', __( 'Social Media URLs', 'obydullah-restaurant-core' ), 'obirc_footer_social_callback', 'obirc_footer', 'normal', 'high' );
    add_meta_box( 'obirc_footer_quick_links', __( 'Quick Links (repeater)', 'obydullah-restaurant-core' ), 'obirc_footer_links_callback', 'obirc_footer', 'normal', 'high' );
    add_meta_box( 'obirc_footer_contact', __( 'Contact Information', 'obydullah-restaurant-core' ), 'obirc_footer_contact_callback', 'obirc_footer', 'normal', 'high' );
    add_meta_box( 'obirc_footer_copyright', __( 'Copyright Text', 'obydullah-restaurant-core' ), 'obirc_footer_copyright_callback', 'obirc_footer', 'side', 'default' );
}
add_action( 'add_meta_boxes', 'obirc_add_footer_meta_boxes' );

function obirc_footer_logo_callback( $post ) {
    wp_nonce_field( 'obirc_footer_meta', 'obirc_footer_nonce' );
    $logo_text   = get_post_meta( $post->ID, 'obirc_footer_logo_text', true );
    $logo_accent = get_post_meta( $post->ID, 'obirc_footer_logo_accent', true );
    $tagline     = get_post_meta( $post->ID, 'obirc_footer_tagline', true );
    ?>
<p>
    <label
        for="obirc_footer_logo_text"><?php esc_html_e( 'Logo Base Text', 'obydullah-restaurant-core' ); ?></label><br>
    <input type="text" name="obirc_footer_logo_text" id="obirc_footer_logo_text"
        value="<?php echo esc_attr( $logo_text ); ?>" class="widefat">
</p>
<p>
    <label
        for="obirc_footer_logo_accent"><?php esc_html_e( 'Logo Accent Text (highlighted)', 'obydullah-restaurant-core' ); ?></label><br>
    <input type="text" name="obirc_footer_logo_accent" id="obirc_footer_logo_accent"
        value="<?php echo esc_attr( $logo_accent ); ?>" class="widefat">
</p>
<p>
    <label
        for="obirc_footer_tagline"><?php esc_html_e( 'Tagline / Description', 'obydullah-restaurant-core' ); ?></label><br>
    <textarea name="obirc_footer_tagline" id="obirc_footer_tagline" rows="3"
        class="large-text"><?php echo esc_textarea( $tagline ); ?></textarea>
</p>
<?php
}

function obirc_footer_social_callback( $post ) {
    wp_nonce_field( 'obirc_footer_meta', 'obirc_footer_nonce' );
    $social = get_post_meta( $post->ID, 'obirc_footer_social', true );
    if ( ! is_array( $social ) ) $social = array();
    ?>
<p>
    <label
        for="obirc_footer_social_instagram"><?php esc_html_e( 'Instagram URL', 'obydullah-restaurant-core' ); ?></label><br>
    <input type="text" name="obirc_footer_social[instagram]" id="obirc_footer_social_instagram"
        value="<?php echo esc_attr( $social['instagram'] ?? '' ); ?>" class="widefat">
</p>
<p>
    <label
        for="obirc_footer_social_facebook"><?php esc_html_e( 'Facebook URL', 'obydullah-restaurant-core' ); ?></label><br>
    <input type="text" name="obirc_footer_social[facebook]" id="obirc_footer_social_facebook"
        value="<?php echo esc_attr( $social['facebook'] ?? '' ); ?>" class="widefat">
</p>
<p>
    <label
        for="obirc_footer_social_x"><?php esc_html_e( 'X (Twitter) URL', 'obydullah-restaurant-core' ); ?></label><br>
    <input type="text" name="obirc_footer_social[x]" id="obirc_footer_social_x"
        value="<?php echo esc_attr( $social['x'] ?? '' ); ?>" class="widefat">
</p>
<?php
}

function obirc_footer_links_callback( $post ) {
    wp_nonce_field( 'obirc_footer_meta', 'obirc_footer_nonce' );
    $links = get_post_meta( $post->ID, 'obirc_footer_links', true );
    if ( ! is_array( $links ) ) $links = array();
    $next_index = count( $links );
    ?>
<div id="obirc-footer-links-repeater" class="obirc-repeater">
    <input type="hidden" id="obirc-footer-link-count" name="obirc_footer_link_count"
        value="<?php echo esc_attr( $next_index ); ?>">
    <?php foreach ( $links as $index => $link ) : ?>
    <div class="obirc-footer-link-row obirc-repeater-row" data-index="<?php echo esc_attr( $index ); ?>">
        <input type="text" name="obirc_footer_links[<?php echo esc_attr( $index ); ?>][text]" class="obirc-link-text"
            value="<?php echo esc_attr( $link['text'] ); ?>"
            placeholder="<?php esc_attr_e( 'Link text', 'obydullah-restaurant-core' ); ?>">
        <input type="text" name="obirc_footer_links[<?php echo esc_attr( $index ); ?>][url]" class="obirc-link-url"
            value="<?php echo esc_attr( $link['url'] ); ?>"
            placeholder="<?php esc_attr_e( 'URL', 'obydullah-restaurant-core' ); ?>">
        <button type="button"
            class="button obirc-remove-row"><?php esc_html_e( 'Remove', 'obydullah-restaurant-core' ); ?></button>
    </div>
    <?php endforeach; ?>
</div>
<button type="button" id="obirc-add-footer-link"
    class="button"><?php esc_html_e( 'Add Link', 'obydullah-restaurant-core' ); ?></button>
<?php
}

function obirc_footer_contact_callback( $post ) {
    wp_nonce_field( 'obirc_footer_meta', 'obirc_footer_nonce' );
    $address = get_post_meta( $post->ID, 'obirc_footer_address', true );
    $phone   = get_post_meta( $post->ID, 'obirc_footer_phone', true );
    $email   = get_post_meta( $post->ID, 'obirc_footer_email', true );
    ?>
<p>
    <label for="obirc_footer_address"><?php esc_html_e( 'Address', 'obydullah-restaurant-core' ); ?></label><br>
    <textarea name="obirc_footer_address" id="obirc_footer_address" rows="3"
        class="large-text"><?php echo esc_textarea( $address ); ?></textarea>
</p>
<p>
    <label for="obirc_footer_phone"><?php esc_html_e( 'Phone', 'obydullah-restaurant-core' ); ?></label><br>
    <input type="tel" name="obirc_footer_phone" id="obirc_footer_phone" value="<?php echo esc_attr( $phone ); ?>"
        class="widefat">
</p>
<p>
    <label for="obirc_footer_email"><?php esc_html_e( 'Email', 'obydullah-restaurant-core' ); ?></label><br>
    <input type="email" name="obirc_footer_email" id="obirc_footer_email" value="<?php echo esc_attr( $email ); ?>"
        class="widefat">
</p>
<?php
}

function obirc_footer_copyright_callback( $post ) {
    wp_nonce_field( 'obirc_footer_meta', 'obirc_footer_nonce' );
    $copyright = get_post_meta( $post->ID, 'obirc_footer_copyright', true );
    ?>
<p>
    <label
        for="obirc_footer_copyright"><?php esc_html_e( 'Copyright text', 'obydullah-restaurant-core' ); ?></label><br>
    <input type="text" name="obirc_footer_copyright" id="obirc_footer_copyright"
        value="<?php echo esc_attr( $copyright ); ?>" class="widefat">
</p>
<?php
}

function obirc_enqueue_footer_assets( $hook ) {
    global $post_type;
    if ( 'obirc_footer' === $post_type && in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
        wp_enqueue_style( 'obirc-footer-css', OBIRC_PLUGIN_URL . 'assets/css/footer-admin.css', array(), OBIRC_VERSION );
        wp_enqueue_script( 'obirc-footer-js', OBIRC_PLUGIN_URL . 'assets/js/footer-admin.js', array( 'jquery' ), OBIRC_VERSION, true );
        wp_localize_script( 'obirc-footer-js', 'obircFooterL10n', array(
            'linkTextPlaceholder' => __( 'Link text', 'obydullah-restaurant-core' ),
            'urlPlaceholder'      => __( 'URL', 'obydullah-restaurant-core' ),
            'removeText'          => __( 'Remove', 'obydullah-restaurant-core' ),
        ) );
    }
}
add_action( 'admin_enqueue_scripts', 'obirc_enqueue_footer_assets' );

function obirc_save_footer_meta( $post_id ) {
    if ( ! isset( $_POST['obirc_footer_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['obirc_footer_nonce'] ) ), 'obirc_footer_meta' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( get_post_type( $post_id ) !== 'obirc_footer' ) {
        return;
    }

    $logo_fields = array( 'obirc_footer_logo_text', 'obirc_footer_logo_accent', 'obirc_footer_tagline' );
    foreach ( $logo_fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_post_meta( $post_id, $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
        }
    }

    if ( isset( $_POST['obirc_footer_social'] ) && is_array( $_POST['obirc_footer_social'] ) ) {
        $social = array();
        foreach ( $_POST['obirc_footer_social'] as $key => $url ) {
            $social[ $key ] = sanitize_text_field( wp_unslash( $url ) );
        }
        update_post_meta( $post_id, 'obirc_footer_social', $social );
    }

    if ( isset( $_POST['obirc_footer_links'] ) && is_array( $_POST['obirc_footer_links'] ) ) {
        $links = array();
        foreach ( $_POST['obirc_footer_links'] as $link ) {
            $text = isset( $link['text'] ) ? sanitize_text_field( wp_unslash( $link['text'] ) ) : '';
            $url  = isset( $link['url'] )  ? sanitize_text_field( wp_unslash( $link['url'] ) ) : '';
            if ( ! empty( $text ) && ! empty( $url ) ) {
                $links[] = array( 'text' => $text, 'url' => $url );
            }
        }
        update_post_meta( $post_id, 'obirc_footer_links', $links );
    } else {

        update_post_meta( $post_id, 'obirc_footer_links', array() );
    }

    $contact_fields = array( 'obirc_footer_address', 'obirc_footer_phone', 'obirc_footer_email', 'obirc_footer_copyright' );
    foreach ( $contact_fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_post_meta( $post_id, $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
        }
    }
}
add_action( 'save_post_obirc_footer', 'obirc_save_footer_meta' );

/* ======================================================
   11. About Page (Single Instance) + Meta Boxes
====================================================== */

function obirc_register_about_page_cpt() {
    register_post_type( 'obirc_about_page', array(
        'labels' => array(
            'name'          => __( 'About Page', 'obydullah-restaurant-core' ),
            'singular_name' => __( 'About Page', 'obydullah-restaurant-core' ),
            'add_new_item'  => __( 'Edit About Page', 'obydullah-restaurant-core' ),
            'edit_item'     => __( 'Edit About Page', 'obydullah-restaurant-core' ),
        ),
        'public'           => false,
        'show_ui'          => true,
        'show_in_menu'     => 'obirc-restaurant-core',
        'menu_icon'        => 'dashicons-info',
        'menu_position'    => 65,
        'supports'         => array( 'title' ),
        'show_in_rest'     => true,
    ) );
}
add_action( 'init', 'obirc_register_about_page_cpt' );

function obirc_limit_about_page() {
    global $pagenow;

    if ( $pagenow === 'post-new.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'obirc_about_page' ) {

        $existing = get_posts( array(
            'post_type'      => 'obirc_about_page',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'fields'         => 'ids',
        ) );

        if ( ! empty( $existing ) ) {
            wp_redirect( admin_url( 'post.php?post=' . $existing[0] . '&action=edit' ) );
            exit;
        }
    }
}
add_action( 'admin_init', 'obirc_limit_about_page' );

function obirc_add_about_page_meta_boxes() {

    add_meta_box(
        'obirc_about_header',
        __( 'Header', 'obydullah-restaurant-core' ),
        'obirc_about_header_callback',
        'obirc_about_page'
    );

    add_meta_box(
        'obirc_about_text',
        __( 'Content', 'obydullah-restaurant-core' ),
        'obirc_about_text_callback',
        'obirc_about_page'
    );

    add_meta_box(
        'obirc_about_slider',
        __( 'Slider (repeatable)', 'obydullah-restaurant-core' ),
        'obirc_about_slider_callback',
        'obirc_about_page'
    );
}
add_action( 'add_meta_boxes', 'obirc_add_about_page_meta_boxes' );

function obirc_about_header_callback( $post ) {
    wp_nonce_field( 'obirc_about_page_meta', 'obirc_about_page_nonce' );

    $kicker = get_post_meta( $post->ID, 'obirc_about_kicker', true );
    $title  = get_post_meta( $post->ID, 'obirc_about_title', true );
    ?>
<p>
    <label for="obirc_about_kicker"><?php esc_html_e( 'Kicker', 'obydullah-restaurant-core' ); ?></label><br>
    <input type="text" name="obirc_about_kicker" id="obirc_about_kicker" value="<?php echo esc_attr( $kicker ); ?>"
        class="widefat">
</p>
<p>
    <label for="obirc_about_title"><?php esc_html_e( 'Main Title', 'obydullah-restaurant-core' ); ?></label><br>
    <input type="text" name="obirc_about_title" id="obirc_about_title" value="<?php echo esc_attr( $title ); ?>"
        class="widefat">
</p>
<?php
}

function obirc_about_text_callback( $post ) {

    $chef = get_post_meta( $post->ID, 'obirc_about_chef_story', true );
    $phil = get_post_meta( $post->ID, 'obirc_about_philosophy', true );
    ?>
<p>
    <label for="obirc_about_chef_story"><?php esc_html_e( 'Chef Story', 'obydullah-restaurant-core' ); ?></label><br>
    <textarea name="obirc_about_chef_story" id="obirc_about_chef_story" rows="6"
        class="large-text"><?php echo esc_textarea( $chef ); ?></textarea>
</p>
<p>
    <label for="obirc_about_philosophy"><?php esc_html_e( 'Philosophy', 'obydullah-restaurant-core' ); ?></label><br>
    <textarea name="obirc_about_philosophy" id="obirc_about_philosophy" rows="6"
        class="large-text"><?php echo esc_textarea( $phil ); ?></textarea>
</p>
<?php
}


function obirc_about_slider_callback( $post ) {

    $slides = get_post_meta( $post->ID, 'obirc_about_slides', true );
    if ( ! is_array( $slides ) ) {
        $slides = array();
    }
    $next_index = count( $slides );
    ?>
<div id="obirc-about-slides-repeater" class="obirc-slides-repeater">
    <input type="hidden" id="obirc-about-slide-count" name="obirc_about_slide_count"
        value="<?php echo esc_attr( $next_index ); ?>">
    <?php foreach ( $slides as $index => $slide ) : ?>
    <div class="obirc-slide-row" data-index="<?php echo esc_attr( $index ); ?>">
        <p>
            <label><?php esc_html_e( 'Title', 'obydullah-restaurant-core' ); ?></label><br>
            <input type="text" name="obirc_about_slides[<?php echo esc_attr( $index ); ?>][title]"
                value="<?php echo esc_attr( $slide['title'] ); ?>" class="widefat">
        </p>
        <p>
            <label><?php esc_html_e( 'Subtitle', 'obydullah-restaurant-core' ); ?></label><br>
            <input type="text" name="obirc_about_slides[<?php echo esc_attr( $index ); ?>][subtitle]"
                value="<?php echo esc_attr( $slide['subtitle'] ); ?>" class="widefat">
        </p>
        <div class="slide-image-wrapper">
            <label><?php esc_html_e( 'Background Image', 'obydullah-restaurant-core' ); ?></label><br>
            <input type="hidden" name="obirc_about_slides[<?php echo esc_attr( $index ); ?>][image]"
                class="slide-image-url" value="<?php echo esc_url( $slide['image'] ); ?>">
            <div class="image-preview">
                <?php if ( ! empty( $slide['image'] ) ) : ?>
                <img src="<?php echo esc_url( $slide['image'] ); ?>" class="preview-thumb">
                <?php endif; ?>
            </div>
            <button type="button"
                class="button select-slide-image"><?php esc_html_e( 'Select Image', 'obydullah-restaurant-core' ); ?></button>
            <button type="button"
                class="button remove-slide-image <?php echo empty( $slide['image'] ) ? 'hidden' : ''; ?>"><?php esc_html_e( 'Remove Image', 'obydullah-restaurant-core' ); ?></button>
        </div>
        <button type="button"
            class="button obirc-remove-slide-row mt-1"><?php esc_html_e( 'Remove Slide', 'obydullah-restaurant-core' ); ?></button>
    </div>
    <?php endforeach; ?>
</div>
<button type="button" id="obirc-add-about-slide"
    class="button"><?php esc_html_e( 'Add Slide', 'obydullah-restaurant-core' ); ?></button>
<?php
}

function obirc_enqueue_about_assets( $hook ) {
    global $post_type;
    if ( 'obirc_about_page' === $post_type && in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
        wp_enqueue_media();
        wp_enqueue_style( 'obirc-about-css', OBIRC_PLUGIN_URL . 'assets/css/about-admin.css', array(), OBIRC_VERSION );
        wp_enqueue_script( 'obirc-about-js', OBIRC_PLUGIN_URL . 'assets/js/about-admin.js', array( 'jquery' ), OBIRC_VERSION, true );
        wp_localize_script( 'obirc-about-js', 'obircAboutL10n', array(
            'titlePlaceholder'    => __( 'Title', 'obydullah-restaurant-core' ),
            'subtitlePlaceholder' => __( 'Subtitle', 'obydullah-restaurant-core' ),
            'imagePlaceholder'    => __( 'Background Image', 'obydullah-restaurant-core' ),
            'selectImage'         => __( 'Select Image', 'obydullah-restaurant-core' ),
            'removeImage'         => __( 'Remove Image', 'obydullah-restaurant-core' ),
            'removeText'          => __( 'Remove Slide', 'obydullah-restaurant-core' ),
        ) );
    }
}
add_action( 'admin_enqueue_scripts', 'obirc_enqueue_about_assets' );

function obirc_save_about_page_meta( $post_id ) {

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    if ( ! isset( $_POST['obirc_about_page_nonce'] ) ) return;

    $nonce = sanitize_text_field( wp_unslash( $_POST['obirc_about_page_nonce'] ) );

    if ( ! wp_verify_nonce( $nonce, 'obirc_about_page_meta' ) ) return;

    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( get_post_type( $post_id ) !== 'obirc_about_page' ) return;


    if ( isset( $_POST['obirc_about_kicker'] ) ) {
        update_post_meta( $post_id, 'obirc_about_kicker',
            sanitize_text_field( wp_unslash( $_POST['obirc_about_kicker'] ) )
        );
    }

    if ( isset( $_POST['obirc_about_title'] ) ) {
        update_post_meta( $post_id, 'obirc_about_title',
            sanitize_text_field( wp_unslash( $_POST['obirc_about_title'] ) )
        );
    }

    if ( isset( $_POST['obirc_about_chef_story'] ) ) {
        update_post_meta( $post_id, 'obirc_about_chef_story',
            sanitize_textarea_field( wp_unslash( $_POST['obirc_about_chef_story'] ) )
        );
    }

    if ( isset( $_POST['obirc_about_philosophy'] ) ) {
        update_post_meta( $post_id, 'obirc_about_philosophy',
            sanitize_textarea_field( wp_unslash( $_POST['obirc_about_philosophy'] ) )
        );
    }

    if ( isset( $_POST['obirc_about_slides'] ) && is_array( $_POST['obirc_about_slides'] ) ) {

        $slides = array();

        foreach ( $_POST['obirc_about_slides'] as $slide ) {

            $title    = isset( $slide['title'] ) ? sanitize_text_field( wp_unslash( $slide['title'] ) ) : '';
            $subtitle = isset( $slide['subtitle'] ) ? sanitize_text_field( wp_unslash( $slide['subtitle'] ) ) : '';
            $image    = isset( $slide['image'] ) ? esc_url_raw( wp_unslash( $slide['image'] ) ) : '';

            if ( empty( $title ) && empty( $subtitle ) && empty( $image ) ) {
                continue;
            }

            $slides[] = array(
                'title'    => $title,
                'subtitle' => $subtitle,
                'image'    => $image,
            );
        }

        update_post_meta( $post_id, 'obirc_about_slides', $slides );

    } else {
        update_post_meta( $post_id, 'obirc_about_slides', array() );
    }
}
add_action( 'save_post_obirc_about_page', 'obirc_save_about_page_meta' );

/* ======================================================
   12. Contact Page
====================================================== */

function obirc_register_contact_page_cpt() {
    register_post_type( 'obirc_contact_page', array(
        'labels' => array(
            'name'          => __( 'Contact Page', 'obydullah-restaurant-core' ),
            'singular_name' => __( 'Contact Page', 'obydullah-restaurant-core' ),
            'add_new_item'  => __( 'Edit Contact Page', 'obydullah-restaurant-core' ),
            'edit_item'     => __( 'Edit Contact Page', 'obydullah-restaurant-core' ),
        ),
        'public'           => false,
        'show_ui'          => true,
        'show_in_menu'     => 'obirc-restaurant-core',
        'menu_icon'        => 'dashicons-email',
        'menu_position'    => 66,
        'supports'         => array( 'title' ),
        'show_in_rest'     => true,
        'capability_type'  => 'post',
        'map_meta_cap'     => true,
    ) );
}
add_action( 'init', 'obirc_register_contact_page_cpt' );

function obirc_limit_contact_page() {
    global $pagenow;
    if ( $pagenow === 'post-new.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'obirc_contact_page' ) {
        $existing = get_posts( array(
            'post_type'      => 'obirc_contact_page',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'fields'         => 'ids',
        ) );
        if ( ! empty( $existing ) ) {
            $post_id = $existing[0];
            if ( current_user_can( 'edit_post', $post_id ) ) {
                wp_redirect( admin_url( 'post.php?post=' . $post_id . '&action=edit' ) );
                exit;
            }
        }
    }
}
add_action( 'admin_init', 'obirc_limit_contact_page' );

function obirc_add_contact_page_meta_boxes() {
    add_meta_box(
        'obirc_contact_page_settings',
        __( 'Contact Page Content', 'obydullah-restaurant-core' ),
        'obirc_contact_page_meta_callback',
        'obirc_contact_page',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'obirc_add_contact_page_meta_boxes' );

function obirc_contact_page_meta_callback( $post ) {
    wp_nonce_field( 'obirc_contact_page_meta', 'obirc_contact_page_nonce' );

    $address   = get_post_meta( $post->ID, 'obirc_contact_address', true );
    $phone     = get_post_meta( $post->ID, 'obirc_contact_phone', true );
    $email     = get_post_meta( $post->ID, 'obirc_contact_email', true );
    $map_embed = get_post_meta( $post->ID, 'obirc_contact_map_embed', true );
    $form_shortcode = get_post_meta( $post->ID, 'obirc_contact_form_shortcode', true );
    ?>
<p>
    <label for="obirc_contact_address"><?php esc_html_e( 'Address', 'obydullah-restaurant-core' ); ?></label><br>
    <textarea name="obirc_contact_address" id="obirc_contact_address" rows="3"
        class="large-text"><?php echo esc_textarea( $address ); ?></textarea>
    <span class="description"><?php esc_html_e( 'Full restaurant address.', 'obydullah-restaurant-core' ); ?></span>
</p>
<p>
    <label for="obirc_contact_phone"><?php esc_html_e( 'Phone Number', 'obydullah-restaurant-core' ); ?></label><br>
    <input type="tel" name="obirc_contact_phone" id="obirc_contact_phone" value="<?php echo esc_attr( $phone ); ?>"
        class="widefat">
</p>
<p>
    <label for="obirc_contact_email"><?php esc_html_e( 'Email Address', 'obydullah-restaurant-core' ); ?></label><br>
    <input type="email" name="obirc_contact_email" id="obirc_contact_email" value="<?php echo esc_attr( $email ); ?>"
        class="widefat">
</p>
<p>
    <label
        for="obirc_contact_map_embed"><?php esc_html_e( 'Google Maps Embed URL', 'obydullah-restaurant-core' ); ?></label><br>
    <input type="url" name="obirc_contact_map_embed" id="obirc_contact_map_embed"
        value="<?php echo esc_url( $map_embed ); ?>" class="widefat"
        placeholder="https://www.google.com/maps/embed?...">
    <span
        class="description"><?php esc_html_e( 'Paste the embed URL from Google Maps.', 'obydullah-restaurant-core' ); ?></span>
</p>
<p>
    <label
        for="obirc_contact_form_shortcode"><?php esc_html_e( 'Contact Form Shortcode', 'obydullah-restaurant-core' ); ?></label><br>
    <input type="text" name="obirc_contact_form_shortcode" id="obirc_contact_form_shortcode"
        value="<?php echo esc_attr( $form_shortcode ); ?>" class="widefat" placeholder="[contact-form-7 id=...]">
    <span
        class="description"><?php esc_html_e( 'If using a plugin like Contact Form 7, paste the shortcode here.', 'obydullah-restaurant-core' ); ?></span>
</p>
<?php
}

function obirc_save_contact_page_meta( $post_id ) {
    if ( ! isset( $_POST['obirc_contact_page_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['obirc_contact_page_nonce'] ) ), 'obirc_contact_page_meta' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( get_post_type( $post_id ) !== 'obirc_contact_page' ) {
        return;
    }

    if ( isset( $_POST['obirc_contact_address'] ) ) {
        update_post_meta( $post_id, 'obirc_contact_address', sanitize_textarea_field( wp_unslash( $_POST['obirc_contact_address'] ) ) );
    }

    if ( isset( $_POST['obirc_contact_phone'] ) ) {
        update_post_meta( $post_id, 'obirc_contact_phone', sanitize_text_field( wp_unslash( $_POST['obirc_contact_phone'] ) ) );
    }

    if ( isset( $_POST['obirc_contact_email'] ) ) {
        update_post_meta( $post_id, 'obirc_contact_email', sanitize_email( wp_unslash( $_POST['obirc_contact_email'] ) ) );
    }

    if ( isset( $_POST['obirc_contact_map_embed'] ) ) {
        update_post_meta( $post_id, 'obirc_contact_map_embed', esc_url_raw( wp_unslash( $_POST['obirc_contact_map_embed'] ) ) );
    }

    if ( isset( $_POST['obirc_contact_form_shortcode'] ) ) {
        update_post_meta( $post_id, 'obirc_contact_form_shortcode', sanitize_text_field( wp_unslash( $_POST['obirc_contact_form_shortcode'] ) ) );
    }
}
add_action( 'save_post_obirc_contact_page', 'obirc_save_contact_page_meta' );

/* ======================================================
   13. Contact Form 7 Support
====================================================== */

function obirc_get_first_cf7_shortcode() {
    if ( ! defined( 'WPCF7_VERSION' ) ) {
        return '';
    }

    $forms = get_posts( array(
        'post_type'      => 'wpcf7_contact_form',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
    ) );

    if ( empty( $forms ) ) {
        return '';
    }

    $form = $forms[0];
    return '[contact-form-7 id="' . (int) $form->ID . '" title="' . esc_attr( $form->post_title ) . '"]';
}