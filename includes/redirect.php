<?php

class RCLP_Category_Latest_Post_Redirect {
  public static function init() {
    add_action( 'parse_request', array( __CLASS__, '_url_redirect' ) );
    add_filter( 'wp_get_nav_menu_items', array( __CLASS__, '_navbar_redirect' ), 11, 3 );
  }


  // URL query redirect
  public static function _url_redirect( $request ) {
    $latest_flag = filter_input( INPUT_GET, 'latest', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

    if ( is_admin() || null === $latest_flag || false === $latest_flag || ! isset( $request->query_vars['category_name'] ) ) {
      return;
    }

    $latest = new WP_Query(
      array(
        'category_name'       => sanitize_title_for_query( (string) $request->query_vars['category_name'] ),
        'posts_per_page'      => 1,
        'post_status'         => 'publish',
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
      )
    );

    if ( $latest->have_posts() && ! empty( $latest->posts[0]->ID ) ) {
      wp_safe_redirect( get_permalink( (int) $latest->posts[0]->ID ) );
      exit;
    }
  }


  // Update menu link
  public static function _navbar_redirect( $items, $menu, $args ) {
    foreach ( $items as $item ) {
      if ( ! empty( $item->redirect_latest_post ) && 'category' === $item->object ) {
        $item->url = add_query_arg( 'latest', '1', $item->url );
      }
    }

    return $items;
  }
}


RCLP_Category_Latest_Post_Redirect::init();
