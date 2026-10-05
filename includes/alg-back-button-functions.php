<?php
/**
 * Back Button Widget - Functions.
 *
 * @version 1.8.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\Back_Button_Widget\Functions
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'alg_back_button_register_wp_widget' ) ) {
	/**
	 * Register Alg_Back_Button_WP_Widget widget.
	 *
	 * @version 1.8.0
	 * @since   1.0.0
	 */
	function alg_back_button_register_wp_widget() {
		register_widget( 'Alg_Back_Button_WP_Widget' );
	}
}
add_action( 'widgets_init', 'alg_back_button_register_wp_widget' );

if ( ! function_exists( 'alg_back_button' ) ) {
	/**
	 * Back button function.
	 *
	 * @version 1.8.0
	 * @since   1.0.0
	 *
	 * @param string $label The label for the back button.
	 * @param array  $args  The arguments for the back button.
	 *
	 * @todo (dev) Move `$label` param to `$args`.
	 * @todo (dev) Add `title` param.
	 * @todo (dev) https://wordpress.org/support/topic/any-way-to-edit-or-use-this-to-add-a-custom-javascript-link/
	 * @todo (feature) Disable button on "no history".
	 * @todo (feature) Predefined CSS styles.
	 * @todo (dev) Option to output `<button>` (instead of `<input type="button">`).
	 * @todo (dev) Color picker.
	 * @todo (feature) Option to enable/disable confirmation (and option for confirmation text).
	 */
	function alg_back_button( $label, $args = array() ) {
		$default_args = array(
			'class'                   => '',
			'style'                   => '',
			'type'                    => 'input',
			'js_func'                 => 'back',
			'hide_on_front_page'      => 'no',
			'hide_on_url_param'       => '',
			'hide_on_url_param_value' => '',
			'show_on_url_param'       => '',
			'show_on_url_param_value' => '',
		);

		$args = array_replace( $default_args, $args );

		if ( apply_filters( 'alg_back_button_widget_do_hide', false, $args ) ) {
			return '';
		}

		$label       = (
			'' === (string) $label ?
			__( 'Back', 'back-button-widget' ) :
			do_shortcode( $label )
		);
		$js_function = (
			'back' === $args['js_func'] ?
			'back()' :
			'go(-1)'
		);

		switch ( $args['type'] ) {

			case 'href':
				return sprintf(
					'javascript:history.%s',
					$js_function
				);

			case 'simple':
				return sprintf(
					'<a href="javascript:history.%s" class="alg_back_button_simple %s" style="%s">%s</a>',
					$js_function,
					esc_attr( $args['class'] ),
					esc_attr( $args['style'] ),
					wp_kses_post( $label )
				);

			default: // 'input'
				return sprintf(
					'<input type="button" value="%s" class="alg_back_button_input %s" style="%s" onclick="window.history.%s" />',
					esc_attr( $label ),
					esc_attr( $args['class'] ),
					esc_attr( $args['style'] ),
					$js_function
				);

		}
	}
}

if ( ! function_exists( 'alg_back_button_shortcode' ) ) {
	/**
	 * Back button shortcode function.
	 *
	 * @version 1.8.0
	 * @since   1.0.0
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @todo (dev) Remove `shortcode_atts()`?
	 * @todo (dev) Add `fa` to the widget params (and maybe to the function params as well).
	 */
	function alg_back_button_shortcode( $atts ) {
		$defaults = array(
			'label'                   => __( 'Back', 'back-button-widget' ),
			'class'                   => '',
			'style'                   => '',
			'type'                    => 'input',
			'js_func'                 => 'back',
			'hide_on_front_page'      => 'no',
			'hide_on_url_param'       => '',
			'hide_on_url_param_value' => '',
			'show_on_url_param'       => '',
			'show_on_url_param_value' => '',
			'lang'                    => '',
			'not_lang_text'           => '',
			'fa'                      => '', // e.g., `fas fa-angle-double-left`.
			'fa_template'             => '%icon%',
			'before'                  => '',
			'after'                   => '',
		);

		$atts = shortcode_atts( $defaults, $atts, 'alg_back_button' );

		if ( ! empty( $atts['fa'] ) ) {
			// Font Awesome.
			$atts['label'] = str_replace(
				'%icon%',
				'<i class="' . esc_attr( $atts['fa'] ) . '"></i>',
				wp_kses_post( $atts['fa_template'] )
			);
			$atts['type']  = 'simple';
		} elseif (
			! empty( $atts['not_lang_text'] ) &&
			! empty( $atts['lang'] ) &&
			(
				! defined( 'ICL_LANGUAGE_CODE' ) ||
				! in_array(
					strtolower( ICL_LANGUAGE_CODE ),
					array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ),
					true
				)
			)
		) {
			// Language.
			$atts['label'] = wp_kses_post( $atts['not_lang_text'] );
		}

		$back_button = alg_back_button( $atts['label'], $atts );
		return (
			$back_button ?
			wp_kses_post( $atts['before'] ) .
			wp_kses(
				$back_button,
				alg_back_button_get_allowed_html_frontend(),
				array_merge( wp_allowed_protocols(), array( 'javascript' ) )
			) .
			wp_kses_post( $atts['after'] ) :
			''
		);
	}
}
add_shortcode( 'alg_back_button', 'alg_back_button_shortcode' );

if ( ! function_exists( 'alg_back_button_translate_shortcode' ) ) {
	/**
	 * Back button translate shortcode function.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Content enclosed by the shortcode.
	 *
	 * @return string Translated text based on the current language.
	 *
	 * @version 1.8.0
	 * @since   1.1.0
	 */
	function alg_back_button_translate_shortcode( $atts, $content = '' ) {
		// E.g.: `[alg_back_button_translate lang="FR" lang_text="Retour" not_lang_text="Back"]`.
		if (
			isset( $atts['lang_text'] ) &&
			isset( $atts['not_lang_text'] ) &&
			! empty( $atts['lang'] )
		) {
			return (
				(
					! defined( 'ICL_LANGUAGE_CODE' ) ||
					! in_array(
						strtolower( ICL_LANGUAGE_CODE ),
						array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ),
						true
					)
				) ?
				wp_kses_post( $atts['not_lang_text'] ) :
				wp_kses_post( $atts['lang_text'] )
			);
		}

		// E.g.: `[alg_back_button_translate lang="FR"]Retour[/alg_back_button_translate][alg_back_button_translate lang="DE"]Zurück[/alg_back_button_translate][alg_back_button_translate not_lang="FR,DE"]Back[/alg_back_button_translate]`.
		return (
			(
				(
					! empty( $atts['lang'] ) &&
					(
						! defined( 'ICL_LANGUAGE_CODE' ) ||
						! in_array(
							strtolower( ICL_LANGUAGE_CODE ),
							array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ),
							true
						)
					)
				) ||
				(
					! empty( $atts['not_lang'] ) &&
					(
						defined( 'ICL_LANGUAGE_CODE' ) &&
						in_array(
							strtolower( ICL_LANGUAGE_CODE ),
							array_map( 'trim', explode( ',', strtolower( $atts['not_lang'] ) ) ),
							true
						)
					)
				)
			) ?
			'' :
			wp_kses_post( $content )
		);
	}
}
add_shortcode( 'alg_back_button_translate', 'alg_back_button_translate_shortcode' );

if ( ! function_exists( 'alg_back_button_get_allowed_html_frontend' ) ) {
	/**
	 * Get allowed HTML for frontend.
	 *
	 * @return array Allowed HTML tags and attributes for frontend.
	 *
	 * @version 1.8.0
	 * @since   1.8.0
	 */
	function alg_back_button_get_allowed_html_frontend() {
		$allowed_html = wp_kses_allowed_html( 'post' );

		$allowed_html['input'] = array(
			'class'   => true,
			'style'   => true,
			'type'    => true,
			'value'   => true,
			'onclick' => true,
		);

		return $allowed_html;
	}
}

if ( ! function_exists( 'alg_back_button_get_allowed_html_backend' ) ) {
	/**
	 * Get allowed HTML for backend.
	 *
	 * @return array Allowed HTML tags and attributes for backend.
	 *
	 * @version 1.8.0
	 * @since   1.8.0
	 */
	function alg_back_button_get_allowed_html_backend() {
		$allowed_html = wp_kses_allowed_html( 'post' );

		$allowed_html['option'] = array(
			'value'    => true,
			'selected' => true,
		);

		$allowed_html['select'] = array(
			'multiple' => true,
			'class'    => true,
			'style'    => true,
			'id'       => true,
			'name'     => true,
			'min'      => true,
			'max'      => true,
			'disabled' => true,
		);

		$allowed_html['input'] = array(
			'class'    => true,
			'style'    => true,
			'type'     => true,
			'id'       => true,
			'name'     => true,
			'value'    => true,
			'min'      => true,
			'max'      => true,
			'disabled' => true,
		);

		return $allowed_html;
	}
}
