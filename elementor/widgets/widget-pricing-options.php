<?php
namespace PM2020_Elementor;

use Elementor;
use Elementor\Controls_Manager;

class Pricing_Options extends Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'pm_pricing_options';
	}

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'EDD Pricing Options', 'pm2020' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'fa fa-money';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the oEmbed widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'general' ];
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'pm2020' ),
			]
		);

		$this->add_control(
			'download_id',
			[
				'label' => __( 'Download ID' ),
				'type' => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'demo_url',
			[
				'label' => __( 'Demo URL' ),
				'default' => '#',
				'type' => Controls_Manager::TEXT,
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render oEmbed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();
		$plugin_demo_url = $settings['demo_url'];

		?>
		<div class="plugin_pricing_wrapper">
			<?php
			if ( function_exists( 'edd_get_purchase_link' ) ) {
				echo edd_get_purchase_link(
					array(
						'download_id' => $settings['download_id'],
						'price' => false,
						'text' => 'Buy Now',
						'class' => 'btn btn-large',
						'direct' => true,
					)
				);
			}

			?>
			<?php if ( $plugin_demo_url ) { ?>
				<div class="plugin_demo_button">
					<a target="_blank" href="<?php echo $plugin_demo_url; ?>" class="btn button button-demo">Live Demo</a>
				</div>
			<?php } ?>
		</div>
		<?php

	}

}

Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Pricing_Options() );
