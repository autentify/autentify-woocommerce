<?php
add_action( 'admin_menu', 'autentify_create_settings_menu' );
if ( ! function_exists( 'autentify_create_settings_menu' ) ) {
	function autentify_create_settings_menu() {
		add_menu_page( 'Configurações - Autentify', 'Autentify', 'administrator', __FILE__, 'autentify_settings_page_content', 'dashicons-admin-generic' );
		add_action( 'admin_init', 'register_autentify_settings' );
	}
}

if ( ! function_exists( 'register_autentify_settings' ) ) {
	function register_autentify_settings() {
		register_setting( 'autentify_settings_group', 'autentify_api_token', 'autentify_api_token_validate' );
		// register_setting( 'autentify_settings_group', 'autentify_auto_order_check', 'autentify_auto_order_check_validate' );  // Disabled AutentiMail
	}
}

function autentify_api_token_validate( $input ) {
	$old_api_token_value = get_option('autentify_api_token');
	$new_api_token_value = sanitize_text_field( $input );

	if ( $new_api_token_value == $old_api_token_value ) {
		return $new_api_token_value;
	}

	if ( ! Autentify_Api::get_instance()->is_available() ) {
		$message = 'API não disponível. Aguarde alguns minutos, e tente novamente.';
		$type = 'warning';
		$code = 'autentify_api_offline';
	} else {
		Autentify_Auth::get_instance()->invalidate_bearer_token_cookie();

		if ( isset( $new_api_token_value ) ) {
			Autentify_Api::get_instance()->set_token( $new_api_token_value );
		}

		if ( ! Autentify_Auth::get_instance()->is_authenticated() ) {
			$message = 'Não foi possível autenticar seu API Token. Verifique se o API Token está '
					. 'correto em:  <a href="https://painel.autentify.com.br/developers/api_token" target="_blank">'
					. 'www.painel.autentify.com.br/developers/api_token</a>';
			$type = 'error';
			$code = 'invalid_autentify_api_token';
		} else {
			$message = "Autenticado com sucesso!";
			$type = 'success';
			$code = 'autentify_api_online';
		}
	}

	add_settings_error( 'autentify_settings_notice', $code, $message, $type );

	return $new_api_token_value;
}

function autentify_auto_order_check_validate( $input ) {
	$new_input_value = sanitize_text_field( $input );

	if ( ! empty( $new_input_value ) && $new_input_value == "on" ) {
		return "true";
	} else {
		return "false";
	}
}

if ( ! function_exists( 'autentify_settings_page_content' ) ) {
	function autentify_settings_page_content() {
		?>
		<div class="wrap">
			<h1>Configurações</h1>
			<?php autentify_settings_form_func(); ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'autentify_settings_form_func' ) ) {
	function autentify_settings_form_func() {
		?>
		<form method="post" action="options.php">
			<?php settings_fields( 'autentify_settings_group' ); ?>
			<?php settings_errors(); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row" style="padding-bottom: 0px;">
						API Token<span class="">*</span>
					</th>
					<td style="padding-bottom: 0px;">
						<input type="password" name="autentify_api_token"
							value="<?php echo esc_attr( get_option( 'autentify_api_token' ) ); ?>"
							class="regular-text" autocomplete="off"
						/>
					</td>
				</tr>
				<tr valign="top">
					<td scope="row" colspan="2" style="padding-top: 10px; padding-left: 0px;">
						Ainda não tem o seu API Token? Entre no link a seguir e obtenha o seu API Token <br />
						para que o plugin possa funcionar corretamente:
						<a href="https://painel.autentify.com.br/developers/api_token" target="_blank" title="Painel Autentify">
							www.painel.autentify.com.br/developers/api_token
						</a>
					</td>
				</tr>
				<!-- Disabled AutentiMail -->
				<!-- <tr valign="top">
					<th scope="row" style="padding-bottom: 0px;">
						Consultar automaticamente
					</th>
					<td style="padding-bottom: 0px;">
						<input type="checkbox" name="autentify_auto_order_check" class="checkbox"
								<?php //echo esc_attr( get_option( 'autentify_auto_order_check' ) == "true" ? "checked" : "" ); ?> />
					</td>
				</tr>
				<tr valign="top">
					<td scope="row" colspan="2" style="padding-top: 10px; padding-left: 0px;">
						Se a opção acima estiver selecionada, as consultas serão feitas automaticamente <br />
						quando o pedido receber o status "processando".
					</td>
				</tr> -->
			</table>
			<?php submit_button(); ?>
		</form>
		<?php
	}
}