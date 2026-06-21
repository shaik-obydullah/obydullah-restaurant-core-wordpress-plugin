<?php
/**
 * Booking List Table Class
 *
 * @package ObydullahRestaurantCore
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class OBIRC_Booking_List_Table extends WP_List_Table {

    public function get_columns() {
        return array(
            'cb'           => '<input type="checkbox" />',
            'id'           => __( 'ID', 'obydullah-restaurant-core' ),
            'name'         => __( 'Name', 'obydullah-restaurant-core' ),
            'email'        => __( 'Email', 'obydullah-restaurant-core' ),
            'phone'        => __( 'Phone', 'obydullah-restaurant-core' ),
            'party'        => __( 'Party', 'obydullah-restaurant-core' ),
            'booking_date' => __( 'Date', 'obydullah-restaurant-core' ),
            'booking_time' => __( 'Time', 'obydullah-restaurant-core' ),
            'notes'        => __( 'Notes', 'obydullah-restaurant-core' ),
            'created_at'   => __( 'Submitted', 'obydullah-restaurant-core' ),
        );
    }

    public function get_sortable_columns() {
        return array(
            'id'           => array( 'id', false ),
            'name'         => array( 'name', false ),
            'booking_date' => array( 'booking_date', false ),
            'created_at'   => array( 'created_at', false ),
        );
    }

    public function prepare_items() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'obirc_restaurant_booking';
        $per_page = 20;

        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );

        $paged   = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
        $offset  = ( $paged - 1 ) * $per_page;

        $orderby = isset( $_GET['orderby'] ) ? sanitize_sql_orderby( $_GET['orderby'] ) : 'id';
        $order   = isset( $_GET['order'] ) && 'ASC' === strtoupper( $_GET['order'] ) ? 'ASC' : 'DESC';

        $total_items = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );

        $this->items = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d",
                $per_page,
                $offset
            ),
            ARRAY_A
        );

        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil( $total_items / $per_page ),
        ) );
    }

    public function column_default( $item, $column_name ) {
        return esc_html( $item[ $column_name ] );
    }

    public function column_cb( $item ) {
        return sprintf( '<input type="checkbox" name="booking_ids[]" value="%s" />', $item['id'] );
    }

    public function get_bulk_actions() {
        return array(
            'delete' => __( 'Delete', 'obydullah-restaurant-core' ),
        );
    }
}