<?php
/**
 * Declarative meta boxes for the dashboard content types.
 *
 * Fields are described once in schema() and both rendered and saved from that
 * description, so adding a field is a one-line change.
 *
 * @package BuyMore_Explore
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

/**
 * Renders and saves the custom fields.
 */
class BuyMore_Meta_Boxes {

	const NONCE = 'buymore_meta_nonce';

	/**
	 * Hook into WordPress.
	 *
	 * @return void
	 */
	public function hooks(): void {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
	}

	/**
	 * Field definitions per post type.
	 *
	 * @return array<string,array<int,array<string,mixed>>>
	 */
	public static function schema(): array {
		return array(
			BuyMore_Post_Types::PRODUCT => array(
				array(
					'key'   => '_buymore_eyebrow',
					'label' => __( 'Label above the name', 'buymore-explore' ),
					'type'  => 'text',
					'help'  => __( 'Shown in grey above the product name, e.g. "Our Picks".', 'buymore-explore' ),
				),
				array(
					'key'   => '_buymore_price',
					'label' => __( 'Price', 'buymore-explore' ),
					'type'  => 'number',
					'help'  => __( 'Numbers only — the currency symbol is set in BuyMore settings.', 'buymore-explore' ),
				),
				array(
					'key'   => '_buymore_swatches',
					'label' => __( 'Colour options', 'buymore-explore' ),
					'type'  => 'text',
					'help'  => __( 'Comma separated hex values, e.g. #f4c2c2, #1f1f1f. Up to four are shown.', 'buymore-explore' ),
				),
				array(
					'key'   => '_buymore_badge',
					'label' => __( 'Corner badge', 'buymore-explore' ),
					'type'  => 'text',
					'help'  => __( 'Optional short text pinned to the image, e.g. "New in".', 'buymore-explore' ),
				),
				array(
					'key'     => '_buymore_bg',
					'label'   => __( 'Image backdrop', 'buymore-explore' ),
					'type'    => 'color',
					'default' => '#f6e3dc',
				),
				array(
					'key'   => '_buymore_link',
					'label' => __( 'Links to', 'buymore-explore' ),
					'type'  => 'url',
				),
			),
			BuyMore_Post_Types::CARD    => array(
				array(
					'key'     => '_buymore_slot',
					'label'   => __( 'Position in the grid', 'buymore-explore' ),
					'type'    => 'select',
					'options' => 'slots',
					'help'    => __( 'Each position renders once. If two cards share a position the most recent one wins.', 'buymore-explore' ),
				),
				array(
					'key'     => '_buymore_style',
					'label'   => __( 'Card style', 'buymore-explore' ),
					'type'    => 'select',
					'options' => array(
						'promo'      => __( 'Promo — headline plus button', 'buymore-explore' ),
						'feature'    => __( 'Feature — headline over a photo', 'buymore-explore' ),
						'favourites' => __( 'Favourites — product strip with arrows', 'buymore-explore' ),
					),
				),
				array(
					'key'   => '_buymore_eyebrow',
					'label' => __( 'Kicker', 'buymore-explore' ),
					'type'  => 'text',
				),
				array(
					'key'   => '_buymore_subtitle',
					'label' => __( 'Supporting line', 'buymore-explore' ),
					'type'  => 'text',
				),
				array(
					'key'   => '_buymore_cta_label',
					'label' => __( 'Button text', 'buymore-explore' ),
					'type'  => 'text',
					'help'  => __( 'Leave empty to show the round arrow button instead.', 'buymore-explore' ),
				),
				array(
					'key'   => '_buymore_cta_url',
					'label' => __( 'Button links to', 'buymore-explore' ),
					'type'  => 'url',
				),
				array(
					'key'     => '_buymore_bg',
					'label'   => __( 'Background', 'buymore-explore' ),
					'type'    => 'color',
					'default' => '#b8e186',
				),
				array(
					'key'     => '_buymore_fg',
					'label'   => __( 'Text colour', 'buymore-explore' ),
					'type'    => 'color',
					'default' => '#16211a',
				),
			),
			BuyMore_Post_Types::ORDER   => array(
				array(
					'key'     => '_buymore_order_label',
					'label'   => __( 'Action text', 'buymore-explore' ),
					'type'    => 'text',
					'default' => 'view order',
				),
				array(
					'key'   => '_buymore_link',
					'label' => __( 'Links to', 'buymore-explore' ),
					'type'  => 'url',
				),
			),
		);
	}

	/**
	 * Register one meta box per post type.
	 *
	 * @return void
	 */
	public function add_meta_boxes(): void {
		foreach ( array_keys( self::schema() ) as $post_type ) {
			add_meta_box(
				'buymore-details',
				__( 'Dashboard details', 'buymore-explore' ),
				array( $this, 'render' ),
				$post_type,
				'normal',
				'high'
			);
		}
	}

	/**
	 * Render the fields for the current post type.
	 *
	 * @param WP_Post $post Current post.
	 * @return void
	 */
	public function render( WP_Post $post ): void {
		$fields = self::schema()[ $post->post_type ] ?? array();

		if ( ! $fields ) {
			return;
		}

		wp_nonce_field( self::NONCE, self::NONCE );

		echo '<div class="buymore-fields">';

		foreach ( $fields as $field ) {
			$value = get_post_meta( $post->ID, $field['key'], true );

			if ( '' === $value && isset( $field['default'] ) ) {
				$value = $field['default'];
			}

			$id = 'bm-' . sanitize_key( $field['key'] );

			printf( '<p class="buymore-field buymore-field--%s">', esc_attr( $field['type'] ) );
			printf( '<label for="%s"><strong>%s</strong></label>', esc_attr( $id ), esc_html( $field['label'] ) );

			switch ( $field['type'] ) {
				case 'select':
					$options = 'slots' === ( $field['options'] ?? '' )
						? BuyMore_Post_Types::card_slots()
						: (array) ( $field['options'] ?? array() );

					printf( '<select id="%s" name="%s">', esc_attr( $id ), esc_attr( $field['key'] ) );
					echo '<option value="">' . esc_html__( '— not placed —', 'buymore-explore' ) . '</option>';

					foreach ( $options as $option_value => $option_label ) {
						printf(
							'<option value="%s" %s>%s</option>',
							esc_attr( (string) $option_value ),
							selected( $value, $option_value, false ),
							esc_html( (string) $option_label )
						);
					}

					echo '</select>';
					break;

				case 'color':
					printf(
						'<input type="color" id="%1$s" name="%2$s" value="%3$s"> <input type="text" class="buymore-hex" value="%3$s" data-for="%1$s" aria-label="%4$s" size="9">',
						esc_attr( $id ),
						esc_attr( $field['key'] ),
						esc_attr( (string) $value ),
						esc_attr__( 'Hex value', 'buymore-explore' )
					);
					break;

				case 'number':
					printf(
						'<input type="number" step="0.01" min="0" id="%s" name="%s" value="%s">',
						esc_attr( $id ),
						esc_attr( $field['key'] ),
						esc_attr( (string) $value )
					);
					break;

				default:
					printf(
						'<input type="%s" class="widefat" id="%s" name="%s" value="%s">',
						'url' === $field['type'] ? 'url' : 'text',
						esc_attr( $id ),
						esc_attr( $field['key'] ),
						esc_attr( (string) $value )
					);
			}

			if ( ! empty( $field['help'] ) ) {
				printf( '<span class="buymore-help">%s</span>', esc_html( $field['help'] ) );
			}

			echo '</p>';
		}

		echo '</div>';
	}

	/**
	 * Persist submitted values.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @return void
	 */
	public function save( int $post_id, WP_Post $post ): void {
		$fields = self::schema()[ $post->post_type ] ?? array();

		if ( ! $fields ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$nonce = isset( $_POST[ self::NONCE ] ) ? sanitize_text_field( wp_unslash( (string) $_POST[ self::NONCE ] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, self::NONCE ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		foreach ( $fields as $field ) {
			$key = $field['key'];

			if ( ! isset( $_POST[ $key ] ) ) {
				continue;
			}

			$raw = wp_unslash( (string) $_POST[ $key ] );

			switch ( $field['type'] ) {
				case 'url':
					$value = esc_url_raw( $raw );
					break;

				case 'number':
					$value = '' === trim( $raw ) ? '' : (string) round( (float) $raw, 2 );
					break;

				case 'color':
					$value = (string) sanitize_hex_color( $raw );
					break;

				case 'select':
					$allowed = 'slots' === ( $field['options'] ?? '' )
						? array_keys( BuyMore_Post_Types::card_slots() )
						: array_keys( (array) ( $field['options'] ?? array() ) );
					$value   = in_array( $raw, $allowed, true ) ? $raw : '';
					break;

				default:
					$value = sanitize_text_field( $raw );
			}

			if ( '' === $value ) {
				delete_post_meta( $post_id, $key );
			} else {
				update_post_meta( $post_id, $key, $value );
			}
		}
	}

	/**
	 * Load the small admin stylesheet and hex-sync script on edit screens only.
	 *
	 * @param string $hook Current admin page.
	 * @return void
	 */
	public function admin_assets( string $hook ): void {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php', 'toplevel_page_buymore', 'settings_page_buymore-settings' ), true ) ) {
			return;
		}

		wp_enqueue_style( 'buymore-admin', BUYMORE_URL . 'assets/css/admin.css', array(), BUYMORE_VERSION );
		wp_enqueue_script( 'buymore-admin', BUYMORE_URL . 'assets/js/admin.js', array(), BUYMORE_VERSION, true );
	}
}
