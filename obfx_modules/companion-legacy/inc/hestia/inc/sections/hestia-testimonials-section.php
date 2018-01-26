<?php
/**
 * Testimonials section for the homepage.
 *
 * @package Hestia
 * @since Hestia 1.0
 */

if ( ! function_exists( 'hestia_testimonials' ) ) :
	/**
	 * Testimonials section content.
	 * This function can be called from a shortcode too.
	 * When it's called as shortcode, the title and the subtitle shouldn't appear and it should be visible all the time,
	 * it shouldn't matter if is disable on front page.
	 *
	 * @since Hestia 1.0
	 * @modified 1.1.51
	 */
	function hestia_testimonials( $is_shortcode = false ) {

		// When this function is called from selective refresh, $is_shortcode gets the value of WP_Customize_Selective_Refresh object. We don't need that.
		if ( ! is_bool( $is_shortcode ) ) {
			$is_shortcode = false;
		}

		$default_title = '';
		$default_subtitle = '';
		$default_content = '';
		if ( current_user_can( 'edit_theme_options' ) ) {
			$default_title    = esc_html__( 'What clients say', 'themeisle-companion' );
			$default_subtitle = esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'themeisle-companion' );
			$default_content  = hestia_get_testimonials_default();
		}
		$hestia_testimonials_title    = get_theme_mod( 'hestia_testimonials_title', $default_title );
		$hestia_testimonials_subtitle = get_theme_mod( 'hestia_testimonials_subtitle', $default_subtitle );
		if ( $is_shortcode ) {
			$hestia_testimonials_title = '';
			$hestia_testimonials_subtitle = '';
		}

		$hestia_testimonials_content  = get_theme_mod( 'hestia_testimonials_content', $default_content );

		$hide_section = get_theme_mod( 'hestia_testimonials_hide', false );
		$section_is_empty = empty( $hestia_testimonials_title ) && empty( $hestia_testimonials_subtitle ) && empty( $hestia_testimonials_content );

		/* Don't show section if Disable section is checked or it doesn't have any content. Show it if it's called as a shortcode */
		if ( ( $is_shortcode === false ) && ( $section_is_empty || (bool) $hide_section === true ) ) {
			if ( is_customize_preview() ) {
				echo '<section class="hestia-testimonials" id="testimonials" data-sorder="hestia_testimonials" style="display: none"></section>';
			}
			return;
		}

		$wrapper_class = $is_shortcode === true ? 'is-shortcode' : '';
		$container_class = $is_shortcode === true ? '' : 'container';

		hestia_before_testimonials_section_trigger();
		?>
		<section class="hestia-testimonials <?php echo esc_attr( $wrapper_class ); ?>" id="testimonials" data-sorder="hestia_testimonials">
			<?php hestia_before_testimonials_section_content_trigger(); ?>
			<div class="<?php echo esc_attr( $container_class ); ?>">
				<?php
				hestia_top_testimonials_section_content_trigger();
				if ( $is_shortcode === false ) {
				?>
					<div class="row">
						<div class="col-md-8 col-md-offset-2 text-center">
							<?php
							if ( ! empty( $hestia_testimonials_title ) || is_customize_preview() ) {
								echo '<h2 class="hestia-title">' . themeisle_hestia_sanitize_string( $hestia_testimonials_title ) . '</h2>';
							}
							if ( ! empty( $hestia_testimonials_subtitle ) || is_customize_preview() ) {
								echo '<h5 class="description">' . themeisle_hestia_sanitize_string( $hestia_testimonials_subtitle ) . '</h5>';
							}
							?>
						</div>
					</div>
					<?php
				}
				hestia_testimonials_content( $hestia_testimonials_content );
				hestia_bottom_testimonials_section_content_trigger();
				?>
			</div>
			<?php hestia_after_testimonials_section_content_trigger(); ?>
		</section>
		<?php
		hestia_after_testimonials_section_trigger();
	}
endif;


/**
 * Display content for testimonials section.
 *
 * @since 1.1.31
 * @access public
 * @param string $hestia_testimonials_content Section content in json format.
 * @param bool   $is_callback Flag to check if it's callback or not.
 */
function hestia_testimonials_content( $hestia_testimonials_content, $is_callback = false ) {

	if ( ! $is_callback ) {
	?>
		<div class="hestia-testimonials-content">
		<?php
	}
	if ( ! empty( $hestia_testimonials_content ) ) :
		$hestia_testimonials_content = json_decode( $hestia_testimonials_content );
		if ( ! empty( $hestia_testimonials_content ) ) {
			echo '<div class="row">';
			foreach ( $hestia_testimonials_content as $testimonial_item ) :
				$image = ! empty( $testimonial_item->image_url ) ? apply_filters( 'hestia_translate_single_string', $testimonial_item->image_url, 'Testimonials section' ) : '';
				$title = ! empty( $testimonial_item->title ) ? apply_filters( 'hestia_translate_single_string', $testimonial_item->title, 'Testimonials section' ) : '';
				$subtitle = ! empty( $testimonial_item->subtitle ) ? apply_filters( 'hestia_translate_single_string', $testimonial_item->subtitle, 'Testimonials section' ) : '';
				$text = ! empty( $testimonial_item->text ) ? apply_filters( 'hestia_translate_single_string', $testimonial_item->text, 'Testimonials section' ) : '';
				$link = ! empty( $testimonial_item->link ) ? apply_filters( 'hestia_translate_single_string', $testimonial_item->link, 'Testimonials section' ) : '';
				?>
				<div class="col-xs-12 col-ms-6 col-sm-6 <?php echo apply_filters( 'hestia_testimonials_per_row_class','col-md-4' ); ?>">
					<div class="card card-testimonial card-plain">
						<?php if ( ! empty( $image ) ) : ?>
							<div class="card-avatar">
								<?php
								if ( ! empty( $link ) ) :
									$link_html = '<a href="' . esc_url( $link ) . '"';
									if ( function_exists( 'hestia_is_external_url' ) ) {
										$link_html .= hestia_is_external_url( $link );
									}
									$link_html .= '>';
									echo wp_kses_post( $link_html );
								endif;
								?>
									<img class="img"
										 src="<?php echo esc_url( $image ); ?>"
										<?php
										if ( ! empty( $title ) ) :
											?>
											alt="<?php echo esc_attr( $title ); ?>" title="<?php echo esc_attr( $title ); ?>" <?php endif; ?> />
									<?php if ( ! empty( $link ) ) : ?>
								</a>
							<?php endif; ?>
							</div>
						<?php endif; ?>
						<div class="content">
							<?php if ( ! empty( $title ) ) : ?>
								<h4 class="card-title"><?php echo esc_html( $title ); ?></h4>
							<?php endif; ?>
							<?php if ( ! empty( $subtitle ) ) : ?>
								<h6 class="category text-muted"><?php echo esc_html( $subtitle ); ?></h6>
							<?php endif; ?>
							<?php if ( ! empty( $text ) ) : ?>
								<p class="card-description"><?php echo wp_kses_post( html_entity_decode( $text ) ); ?></p>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<?php
			endforeach;
			echo '</div>';
		}// End if().
	endif;
	if ( ! $is_callback ) {
	?>
		</div>
		<?php
	}
}


/**
 * Get default values for testimonials section.
 *
 * @since 1.1.31
 * @access public
 */
function hestia_get_testimonials_default() {
	return apply_filters(
		'hestia_testimonials_default_content', json_encode(
			array(
				array(
					'image_url' => get_template_directory_uri() . '/assets/img/5.jpg',
					'title'     => esc_html__( 'Inverness McKenzie', 'themeisle-companion' ),
					'subtitle'  => esc_html__( 'Business Owner', 'themeisle-companion' ),
					'text'      => esc_html__( '"We have no regrets! After using your product my business skyrocketed! I made back the purchase price in just 48 hours! I couldn\'t have asked for more than this."', 'themeisle-companion' ),
					'id'        => 'customizer_repeater_56d7ea7f40d56',
				),
				array(
					'image_url' => get_template_directory_uri() . '/assets/img/6.jpg',
					'title'     => esc_html__( 'Hanson Deck', 'themeisle-companion' ),
					'subtitle'  => esc_html__( 'Independent Artist', 'themeisle-companion' ),
					'text'      => esc_html__( '"Your company is truly upstanding and is behind its product 100 percent. Hestia is worth much more than I paid. I like Hestia more each day because it makes easier."', 'themeisle-companion' ),
					'id'        => 'customizer_repeater_56d7ea7f40d66',
				),
				array(
					'image_url' => get_template_directory_uri() . '/assets/img/7.jpg',
					'title'     => esc_html__( 'Natalya Undergrowth', 'themeisle-companion' ),
					'subtitle'  => esc_html__( 'Freelancer', 'themeisle-companion' ),
					'text'      => esc_html__( '"Thank you for making it painless, pleasant and most of all hassle free! I am so pleased with this product. Dude, your stuff is great! I will refer everyone I know."', 'themeisle-companion' ),
					'id'        => 'customizer_repeater_56d7ea7f40d76',
				),
			)
		)
	);
}

if ( function_exists( 'hestia_testimonials' ) ) {
	$section_priority = apply_filters( 'hestia_section_priority', 45, 'hestia_testimonials' );
	add_action( 'hestia_sections', 'hestia_testimonials', absint( $section_priority ) );
	if ( function_exists( 'hestia_testimonials_register_strings' ) ) {
		add_action( 'after_setup_theme', 'hestia_testimonials_register_strings', 11 );
	}
}
