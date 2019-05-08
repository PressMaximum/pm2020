<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PM2020_Elementor {

	private static $instance = null;

	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function init() {
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'widgets_registered' ) );
	}

	public function widgets_registered() {

		// We check if the Elementor plugin has been installed / activated.
		if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Widget_Base' ) ) {

			// We look for any theme overrides for this custom Elementor element.
			// If no theme overrides are found we use the default one in this plugin.
			$widgets = array( 'widget-pricing-options' );
			foreach ( $widgets as $base ) {
				$template_file = get_stylesheet_directory() . '/elementor/widgets/' . $base . '.php';
				if ( $template_file && is_readable( $template_file ) ) {
					require_once $template_file;
					
				}
			}
		}





	}
}

PM2020_Elementor::get_instance()->init();
