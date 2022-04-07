<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Autentify_Plugin {
	private static $instance;

  private function __construct() {
		$this->load_js_files();
		$this->set_order_columns();
		$this->set_order_column_values();
	}

  public static function get_instance() {
    if ( ! isset( self::$instance ) ) {
      self:$instance = new Autentify_Plugin();
    }
  }

	private function load_js_files() {
		if ( isset($_GET['post_type']) && $_GET['post_type'] == 'shop_order' && ! function_exists( 'autentify_admin_orders_script') ) {
			function autentify_admin_orders_script( $hook ) {
				if ( $hook != "edit.php" ) return;
				wp_enqueue_script( 'autentify_admin_orders_script', AUTENTIFY_ASSETS_URL . 'js/orders.js', array( 'jquery' ), '', true );
        wp_enqueue_style( 'autentify_admin_orders_style', AUTENTIFY_ASSETS_URL . 'css/order.css', array());
			}
			add_action( 'admin_enqueue_scripts', 'autentify_admin_orders_script' );
		}
	}

	private function set_order_columns(){
		if ( ! function_exists( 'autentify_set_order_columns' ) ) {
			function autentify_set_order_columns( $columns ) {
				$new_columns = ( is_array( $columns ) ) ? $columns : array();
				unset( $new_columns['order_actions'] );
				unset( $new_columns['order_total'] );

				$new_columns['autentify_autenti_mail_score'] = 'AutentiMail Score';
				$new_columns['autentify_autenti_mail_score_msg'] = 'Classificação de Risco';
				$new_columns['order_actions'] = $columns['order_actions'];
				$new_columns['order_total'] = 'Total';

				return $new_columns;
			}
			add_filter( 'manage_edit-shop_order_columns', 'autentify_set_order_columns' );
		}
	}

	private function set_order_column_values() {
		if ( ! function_exists( 'autentify_set_order_column_values' ) ) {
			function autentify_set_order_column_values( $column ) {
				global $post;

				$order = wc_get_order( $post->ID );
				$email = $order->get_billing_email();
				$admin_ajax_url = admin_url( "admin-ajax.php" );

				$autenti_mail_post_meta = get_post_meta( $order->get_id(), 'autenti_mail', true );
				$has_autenti_mail = isset( $autenti_mail_post_meta ) && !empty( $autenti_mail_post_meta );

				if ( $has_autenti_mail ) {
					$autenti_mail = Autentify_Autenti_Mail::with_encoded_json($autenti_mail_post_meta);
					if ( $column == 'autentify_autenti_mail_score' ) {
						echo $autenti_mail->get_risk_score_html();
					} elseif ( $column == 'autentify_autenti_mail_score_msg' ) {
						echo $autenti_mail->get_risk_score_msg_pt_br();
					}
				} else {
					if ( $column == 'autentify_autenti_mail_score' ) {
						$autenti_mail = new Autentify_Autenti_Mail( $email );
						echo $autenti_mail->get_check_btn_in_html( $order->get_id(), $admin_ajax_url );
					} elseif ( $column == 'autentify_autenti_mail_score_msg' ) {
						echo '<div class="autentify-check-status"><span>Não Solicitada</span></div>';
					}
				}
			}
			add_action( 'manage_shop_order_posts_custom_column', 'autentify_set_order_column_values', 2 );
		}
	}
}