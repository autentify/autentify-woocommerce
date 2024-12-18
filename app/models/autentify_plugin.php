<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Autentify_Plugin {
	private static $instance;

  private function __construct() {
		$this->load_js_files();
		$this->add_order_columns();
		$this->add_order_column_values();
	}

  public static function get_instance() {
    if ( ! isset( self::$instance ) ) {
      self:$instance = new Autentify_Plugin();
    }
  }

	private function load_js_files() {
		if ( isset( $_GET['page'] ) && $_GET['page'] === 'wc-orders' && ! function_exists( 'autentify_admin_orders_script' ) ) {
			function autentify_admin_orders_script( $hook ) {
				if ( $hook === 'admin.php') return;

				wp_enqueue_script( 'autentify_admin_orders_script', AUTENTIFY_ASSETS_URL . 'js/orders.js', array( 'jquery' ), '', true );
				wp_enqueue_script( 'autentify_admin_autenti_commerce_script', AUTENTIFY_ASSETS_URL . 'js/autenti_commerce.js', array( 'jquery' ), '', true );
        wp_enqueue_style( 'autentify_admin_orders_style', AUTENTIFY_ASSETS_URL . 'css/order.css', array());

				wp_localize_script('autentify_admin_autenti_commerce_script', 'autentify_ajax', [
					'ajax_url' => admin_url('admin-ajax.php')
				]);
			}
			add_action( 'admin_enqueue_scripts', 'autentify_admin_orders_script' );
		}
	}

	private function add_order_columns(){
		if ( ! function_exists( 'add_autentify_order_columns' ) ) {
			function add_autentify_order_columns( $columns ) {
				$new_columns = ( is_array( $columns ) ) ? $columns : array();
				unset( $new_columns['order_total'] );

				// $new_columns['autentify_autenti_mail_score'] = 'AutentiMail Score'; // Disabled AutentiMail
				// $new_columns['autentify_autenti_mail_score_msg'] = 'AutentiMail Risco'; // Disabled AutentiMail
				$new_columns['autentify_autenti_commerce_status'] = 'Antifraude';
				$new_columns['order_total'] = 'Total';

				return $new_columns;
			}
			add_filter( 'woocommerce_shop_order_list_table_columns', 'add_autentify_order_columns' );
		}
	}

	private function add_order_column_values() {
		if ( ! function_exists( 'add_autentify_order_column_values' ) ) {
			function add_autentify_order_column_values( $column, $order ) {
				// $email = $order->get_billing_email(); // Disabled AutentiMail
				// Autentify_Plugin::set_autenti_mail_order_columns($column, $order, $email, $admin_ajax_url); // Disabled AutentiMail
				Autentify_Plugin::set_autenti_commerce_order_columns($column, $order);
			}
			add_action( 'woocommerce_shop_order_list_table_custom_column', 'add_autentify_order_column_values', 10, 2 );
		}
	}

	public static function set_autenti_mail_order_columns($column, $order, $email, $admin_ajax_url) {
		$autenti_mail_post_meta = $order->get_meta( 'autenti_mail', true );
		$has_autenti_mail = isset( $autenti_mail_post_meta ) && ! empty( $autenti_mail_post_meta );

		// Adds waiting status when the check was requested more than AUTENTIFY_CHECK_TIMEOUT seconds ago.
		if ( $has_autenti_mail && property_exists( $autenti_mail_post_meta, 'status') && $autenti_mail_post_meta->status == 202 && ($autenti_mail_post_meta->created_at + AUTENTIFY_CHECK_TIMEOUT) > Time() ) {
			if ( $column == 'autentify_autenti_mail_score' ) {
				echo "Consultando...";
			} elseif ( $column == 'autentify_autenti_mail_score_msg' ) {
				echo '<div class="autentify-analysis-status"><span>Aguarde</span></div>';
			}
		} elseif ( $has_autenti_mail && ! isset( $autenti_mail_post_meta->status ) ) {
			$autenti_mail = Autentify_Autenti_Mail::with_encoded_json($autenti_mail_post_meta);
			if ( $column == 'autentify_autenti_mail_score' ) {
				echo $autenti_mail->get_risk_score_html();
			} elseif ( $column == 'autentify_autenti_mail_score_msg' ) {
				echo $autenti_mail->get_risk_score_msg_pt_br();
			}
		} else {
			if ( $column == 'autentify_autenti_mail_score' ) {
				$autenti_mail = new Autentify_Autenti_Mail( $email );
				echo $autenti_mail->get_check_btn_in_html( $order->get_id() );
			} elseif ( $column == 'autentify_autenti_mail_score_msg' ) {
				echo '<div class="autentify-analysis-status"><span>Não Solicitada</span></div>';
			}
		}
	}

	public static function set_autenti_commerce_order_columns($column, $order) {
		if ( $column != 'autentify_autenti_commerce_status' ) {
			return;
		}
		
		$autenti_commerce_post_meta = $order->get_meta( 'autenti_commerce', true );
		$has_autenti_commerce = isset( $autenti_commerce_post_meta ) && ! empty( $autenti_commerce_post_meta );

		if ( $has_autenti_commerce ) {
			$autenti_commerce = Autentify_Autenti_Commerce::with_encoded_json($autenti_commerce_post_meta);
			
			echo Autentify_Autenti_Commerce_Helper::get_instance()->get_status_html(
        $autenti_commerce
      );
		} else {
			$taxIdentifier = null;
			$customer = null;
			$autenti_commerce_order = null;

			$autenti_commerce = new Autentify_Autenti_Commerce( $taxIdentifier, $customer, $autenti_commerce_order );
			echo $autenti_commerce->get_check_btn_in_html( $order->get_id() );
		}
	}
}