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
		$this->set_autentify_user_check_post_bulk_option();
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

				$new_columns['autentify_autenti_mail_score'] = 'AM Score';
				$new_columns['autentify_autenti_mail_score_msg'] = 'AM Status';
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
				$has_email = isset( $email ) && !empty( $email );
				$admin_ajax_url = admin_url( "admin-ajax.php" );

				$autenti_mail_post_meta = get_post_meta( $order->id, 'autenti_mail', true );
				$has_autenti_mail = isset( $autenti_mail_post_meta ) && !empty( $autenti_mail_post_meta );

				if ( $has_autenti_mail ) {
					$autenti_mail = new Autentify_Autenti_Mail($autenti_mail_post_meta);
					if ( $column == 'autentify_autenti_mail_score' ) {
						echo $autenti_mail->get_risk_score_html();
					} elseif ( $column == 'autentify_autenti_mail_score_msg' ) {
						echo $autenti_mail->get_risk_score_msg_pt_br();
					}
				} else {
					$check_btn_with_email = "<a href='#' class='button button-primary'"
						. "onclick='startIndividualCheck(\"$order->ID\", \"$email\",\"$admin_ajax_url\")'>Iniciar Consulta</a>";
					$check_btn_without_email = "Sem e-mail";
					$check_btn = $has_email ? $check_btn_with_email : $check_btn_without_email;

					if ( $column == 'autentify_autenti_mail_score' ) {
						echo $check_btn;
					}
					if ( $column == 'autentify_autenti_mail_score_msg' ) {

						echo "Não Solicitada";
					}
				}
			}
			add_action( 'manage_shop_order_posts_custom_column', 'autentify_set_order_column_values', 2 );
		}
	}

	private function set_autentify_user_check_post_bulk_option() {
		if ( ! function_exists( 'autentify_user_check_post_bulk' ) ) {
			function autentify_user_check_post_bulk( $actions ) {
				$actions['autentify_user_check_post_bulk'] = __( 'Iniciar Consultas (Autentify)', 'woocommerce' );
				return $actions;
			}
			add_filter( 'bulk_actions-edit-shop_order', 'autentify_user_check_post_bulk', 20, 1 );
		}
	}
}