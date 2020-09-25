<?php
require_once( ABSPATH . '/wp-includes/pluggable.php' );

if ( ! function_exists( 'current_user_has_role' ) ) {
  function current_user_has_role( $role ){
    return user_has_role_by_user_id( get_current_user_id(), $role );
  }
}

if ( ! function_exists( 'get_user_roles_by_user_id' ) ) {
  function get_user_roles_by_user_id( $user_id ) {
    $user = get_userdata( $user_id );
    return empty( $user ) ? array() : $user->roles;
  }
}

if ( ! function_exists( 'user_has_role_by_user_id' ) ) {
  function user_has_role_by_user_id( $user_id, $role ) {
    $user_roles = get_user_roles_by_user_id( $user_id );
    if ( is_array( $role ) ) {
        return array_intersect( $role, $user_roles ) ? true : false;
    }
    return in_array( $role, $user_roles );
  }
}