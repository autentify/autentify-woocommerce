<?php
add_action( 'admin_menu', 'autentify_create_settings_menu' );
if ( ! function_exists( 'autentify_create_settings_menu' ) ) {
	function autentify_create_settings_menu() {
		add_menu_page( 'Configurações - Autentify', 'Autentify', 'administrator', __FILE__, 'autentify_settings_form_func', 'dashicons-admin-generic' );
		add_action( 'admin_init', 'register_autentify_settings' );
	}
}

if ( ! function_exists( 'register_autentify_settings' ) ) {
	function register_autentify_settings() {
		register_setting( 'autentify_settings_group', 'autentify_api_token', 'autentify_api_token_validate' );
	}
}

function autentify_api_token_validate($input) {
	$old_api_token_value = get_option('autentify_api_token');
	$new_api_token_value = sanitize_text_field( $input );

	if( isset( $new_api_token_value ) && $new_api_token_value != $old_api_token_value ) {
		Autentify_Auth::get_instance()->invalidate_bearer_token_cookie();
		Autentify_Api::get_instance()->set_token( $new_api_token_value );
	}

	if ( ! Autentify_Auth::get_instance()->is_authenticated() ) {
		$message = 'Não foi possível autenticar seu API Token. Verifique se ele está correto em: '
				. '<a href="https://painel.autentify.com.br/developers/api_token" target="_blank">'
				. 'www.painel.autentify.com.br/developers/api_token</a>';
		$type = 'error';
		$code = 'invalid_autentify_api_token';
	} else {
		$message = 'API Token autenticado com sucesso!';
		$type = 'success';
		$code = 'valid_autentify_api_token';
	}

	add_settings_error( 'autentify_settings_notice', $code, $message, $type );

	return $new_api_token_value;
}

if ( ! function_exists( 'autentify_settings_form_func' ) ) {
	function autentify_settings_form_func() {
		?>
		<div class="wrap">
			<h1>Configurações</h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'autentify_settings_group' ); ?>
				<?php do_settings_sections( 'autentify_settings_group' ); ?>
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
					<tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}