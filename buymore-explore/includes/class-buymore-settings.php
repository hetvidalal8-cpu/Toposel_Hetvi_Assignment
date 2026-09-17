<?php
/**
 * Settings screen for the chrome around the grid (header, labels, colours).
 *
 * Everything that is not repeatable content lives here, in one option row,
 * validated through the Settings API.
 *
 * @package BuyMore_Explore
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

/**
 * Registers the BuyMore settings page.
 */
class BuyMore_Settings {

	const OPTION_KEY = 'buymore_settings';
	const PAGE_SLUG  = 'buymore-settings';

	/**
	 * Hook into WordPress.
	 *
	 * @return void
	 */
	public function hooks(): void {
		add_action( 'admin_init', array( $this, 'register' ) );
		add_action( 'admin_menu', array( $this, 'add_page' ), 20 );
	}

	/**
	 * Default values, also used as the field list.
	 *
	 * @return array<string,string>
	 */
	public static function defaults(): array {
		return array(
			'brand_name'          => 'BuyMore',
			'stat_value'          => '37',
			'stat_label'          => 'Orders',
			'stat_meta'           => 'Last 7 days',
			'view_primary'        => 'Dashboard',
			'view_secondary'      => 'Website',
			'cart_label'          => 'Cart',
			'team_extra'          => '8',
			'user_name'           => 'Ryana',
			'user_avatar'         => '',
			'page_title'          => 'Explore',
			'filter_all_label'    => 'All',
			'filters_label'       => 'Filters',
			'search_placeholder'  => 'Search products',
			'quick_actions_title' => 'Quick actions',
			'orders_title'        => 'Last orders',
			'orders_link_label'   => 'See all',
			'orders_link_url'     => '',
			'logout_label'        => 'Log out',
			'logout_url'          => '',
			'currency'            => '$',
			'accent'              => '#1f57ef',
			'canvas'              => '#d9ecd0',
		);
	}

	/**
	 * Field groups for rendering.
	 *
	 * @return array<string,array<string,array<string,string>>>
	 */
	private function fields(): array {
		return array(
			'buymore_header'  => array(
				'title'  => __( 'Top bar', 'buymore-explore' ),
				'fields' => array(
					'brand_name'     => array( __( 'Store name', 'buymore-explore' ), 'text', '' ),
					'stat_value'     => array( __( 'Headline number', 'buymore-explore' ), 'text', __( 'Clear this field to show the live count of published orders instead.', 'buymore-explore' ) ),
					'stat_label'     => array( __( 'Number label', 'buymore-explore' ), 'text', '' ),
					'stat_meta'      => array( __( 'Number sub-label', 'buymore-explore' ), 'text', '' ),
					'view_primary'   => array( __( 'First view tab', 'buymore-explore' ), 'text', '' ),
					'view_secondary' => array( __( 'Second view tab', 'buymore-explore' ), 'text', '' ),
					'cart_label'     => array( __( 'Cart button text', 'buymore-explore' ), 'text', '' ),
					'team_extra'     => array( __( 'Extra team members', 'buymore-explore' ), 'number', __( 'Shown as "+8" beside the shared avatars.', 'buymore-explore' ) ),
					'user_name'      => array( __( 'Signed-in name', 'buymore-explore' ), 'text', '' ),
					'user_avatar'    => array( __( 'Avatar image URL', 'buymore-explore' ), 'url', __( 'Paste a media library URL. Falls back to the initial of the name.', 'buymore-explore' ) ),
				),
			),
			'buymore_toolbar' => array(
				'title'  => __( 'Explore toolbar', 'buymore-explore' ),
				'fields' => array(
					'page_title'         => array( __( 'Section heading', 'buymore-explore' ), 'text', '' ),
					'filter_all_label'   => array( __( '"Everything" tab text', 'buymore-explore' ), 'text', __( 'The remaining tabs come from the Audiences taxonomy.', 'buymore-explore' ) ),
					'filters_label'      => array( __( 'Filters button text', 'buymore-explore' ), 'text', '' ),
					'search_placeholder' => array( __( 'Search placeholder', 'buymore-explore' ), 'text', '' ),
				),
			),
			'buymore_sidebar' => array(
				'title'  => __( 'Sidebar', 'buymore-explore' ),
				'fields' => array(
					'quick_actions_title' => array( __( 'Quick actions heading', 'buymore-explore' ), 'text', __( 'Menu items are managed in Appearance → Menus.', 'buymore-explore' ) ),
					'orders_title'        => array( __( 'Recent orders heading', 'buymore-explore' ), 'text', '' ),
					'orders_link_label'   => array( __( 'Orders link text', 'buymore-explore' ), 'text', '' ),
					'orders_link_url'     => array( __( 'Orders link URL', 'buymore-explore' ), 'url', '' ),
					'logout_label'        => array( __( 'Sign-out text', 'buymore-explore' ), 'text', '' ),
					'logout_url'          => array( __( 'Sign-out URL', 'buymore-explore' ), 'url', __( 'Defaults to the WordPress log-out link.', 'buymore-explore' ) ),
				),
			),
			'buymore_theme'   => array(
				'title'  => __( 'Colour and currency', 'buymore-explore' ),
				'fields' => array(
					'accent'   => array( __( 'Accent colour', 'buymore-explore' ), 'color', __( 'Used for the active menu pill and price chips.', 'buymore-explore' ) ),
					'canvas'   => array( __( 'Page backdrop', 'buymore-explore' ), 'color', '' ),
					'currency' => array( __( 'Currency symbol', 'buymore-explore' ), 'text', '' ),
				),
			),
		);
	}

	/**
	 * Register the option, sections and fields.
	 *
	 * @return void
	 */
	public function register(): void {
		register_setting(
			'buymore_settings_group',
			self::OPTION_KEY,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => self::defaults(),
			)
		);

		foreach ( $this->fields() as $section_id => $section ) {
			add_settings_section( $section_id, $section['title'], '__return_false', self::PAGE_SLUG );

			foreach ( $section['fields'] as $key => $field ) {
				add_settings_field(
					$key,
					$field[0],
					array( $this, 'render_field' ),
					self::PAGE_SLUG,
					$section_id,
					array(
						'key'   => $key,
						'type'  => $field[1],
						'help'  => $field[2],
						'label_for' => 'buymore-' . $key,
					)
				);
			}
		}
	}

	/**
	 * Add the settings page under Settings.
	 *
	 * @return void
	 */
	public function add_page(): void {
		add_options_page(
			__( 'BuyMore dashboard', 'buymore-explore' ),
			__( 'BuyMore dashboard', 'buymore-explore' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_page' )
		);

		add_submenu_page(
			'buymore',
			__( 'Settings', 'buymore-explore' ),
			__( 'Settings', 'buymore-explore' ),
			'manage_options',
			'options-general.php?page=' . self::PAGE_SLUG
		);
	}

	/**
	 * Render a single field.
	 *
	 * @param array<string,string> $args Field args.
	 * @return void
	 */
	public function render_field( array $args ): void {
		$key   = $args['key'];
		$value = (string) buymore_get_option( $key );
		$name  = self::OPTION_KEY . '[' . $key . ']';
		$id    = 'buymore-' . $key;

		switch ( $args['type'] ) {
			case 'color':
				printf(
					'<input type="color" id="%s" name="%s" value="%s">',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( $value )
				);
				break;

			case 'number':
				printf(
					'<input type="number" min="0" step="1" id="%s" name="%s" value="%s" class="small-text">',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( $value )
				);
				break;

			case 'url':
				printf(
					'<input type="url" id="%s" name="%s" value="%s" class="regular-text">',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( $value )
				);
				break;

			default:
				printf(
					'<input type="text" id="%s" name="%s" value="%s" class="regular-text">',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( $value )
				);
		}

		if ( ! empty( $args['help'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $args['help'] ) );
		}
	}

	/**
	 * Render the page wrapper.
	 *
	 * @return void
	 */
	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		echo '<div class="wrap buymore-settings">';
		echo '<h1>' . esc_html__( 'BuyMore dashboard', 'buymore-explore' ) . '</h1>';
		printf(
			'<p class="buymore-settings__hint">%s <code>[buymore_dashboard]</code></p>',
			esc_html__( 'Place the dashboard on any page with this shortcode:', 'buymore-explore' )
		);
		echo '<form action="options.php" method="post">';
		settings_fields( 'buymore_settings_group' );
		do_settings_sections( self::PAGE_SLUG );
		submit_button();
		echo '</form></div>';
	}

	/**
	 * Sanitise the whole option array against the known field list.
	 *
	 * Unknown keys are dropped rather than stored.
	 *
	 * @param mixed $input Raw submitted value.
	 * @return array<string,string>
	 */
	public function sanitize( $input ): array {
		$input  = is_array( $input ) ? $input : array();
		$output = array();

		foreach ( $this->fields() as $section ) {
			foreach ( $section['fields'] as $key => $field ) {
				$raw = isset( $input[ $key ] ) ? (string) $input[ $key ] : '';

				switch ( $field[1] ) {
					case 'url':
						$output[ $key ] = esc_url_raw( $raw );
						break;

					case 'color':
						$output[ $key ] = (string) sanitize_hex_color( $raw );
						break;

					case 'number':
						$output[ $key ] = '' === trim( $raw ) ? '' : (string) absint( $raw );
						break;

					default:
						$output[ $key ] = sanitize_text_field( $raw );
				}
			}
		}

		return $output;
	}
}
