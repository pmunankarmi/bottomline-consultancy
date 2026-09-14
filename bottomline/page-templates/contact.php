<?php
/**
 * Template Name: Contact
 * Template Post Type: page
 *
 * The contact page layout.
 *
 * @package Bottomline
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<?php get_template_part( 'template-parts/page-header' ); ?>
	<?php
	$data = bl_field( 'section_1', get_the_ID() );
	if ( bl_has_content( $data ) ) :
		?>
		<section class="section">
			<div class="container">
				<div class="contact-grid">
					<div class="reveal">
						<div class="contact-info-block">
							<h4><?php echo bl_text( $data['label'] ?? '' ); ?></h4>
							<p>
								<a href="tel:<?php echo esc_attr( preg_replace( '/[^+0-9]/', '', (string) bl_field( 'phone', 'option' ) ) ); ?>"><?php echo bl_text( bl_field( 'phone', 'option' ) ); ?></a>
							</p>
						</div>
						<div class="contact-info-block">
							<h4><?php echo bl_text( $data['label_2'] ?? '' ); ?></h4>
							<p>
								<a href="mailto:<?php echo esc_attr( bl_field( 'email', 'option' ) ); ?>"><?php echo bl_text( bl_field( 'email', 'option' ) ); ?></a>
							</p>
						</div>
						<div class="contact-info-block">
							<h4><?php echo bl_text( $data['label_3'] ?? '' ); ?></h4>
							<p><?php echo bl_text( bl_field( 'website_label', 'option' ) ); ?></p>
						</div>
						<div class="contact-info-block" style="margin-top: 40px;">
							<h4><?php echo bl_text( $data['label_4'] ?? '' ); ?></h4>
							<div class="offices-list" style="margin-top:12px">
								<?php foreach ( bl_rows( bl_field( 'branches', 'option' ) ) as $branch ) : ?>
									<div class="office">
										<div class="pin" aria-hidden="true">
											<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
												<path d="M12 22s8-9 8-13a8 8 0 0 0-16 0c0 4 8 13 8 13z"/>
												<circle cx="12" cy="9" r="3"/>
											</svg>
										</div>
										<div>
											<a href="<?php echo esc_url( $branch['map_link'] ?? '' ); ?>" target="_blank" rel="noopener noreferrer">
												<div class="office-city"><?php echo bl_text( $branch['name'] ?? '' ); ?></div>
												<div class="office-address"><?php echo bl_text( $branch['address'] ?? '' ); ?></div>
											</a>
											<?php if ( ! empty( $branch['phone'] ) ) : ?>
												<a href="tel:<?php echo esc_attr( preg_replace( '/[^+0-9]/', '', $branch['phone'] ) ); ?>"><?php echo bl_text( $branch['phone'] ); ?></a>
											<?php endif; ?>
											<?php if ( ! empty( $branch['email'] ) ) : ?>
												<a href="mailto:<?php echo esc_attr( $branch['email'] ); ?>"><?php echo bl_text( $branch['email'] ); ?></a>
											<?php endif; ?>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
					<div class="reveal" id="contact-form">
						<form class="form-card" id="contactForm" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
							<?php bl_form_controls(); ?>
							<h3 style="font-size: 1.25rem; margin-bottom: 8px;"><?php echo bl_text( $data['card_title'] ?? '' ); ?></h3>
							<p style="font-size: 0.9375rem; color: var(--muted); margin-bottom: 24px;"><?php echo bl_text( $data['description'] ?? '' ); ?></p>
							<div class="form-row">
								<div class="field">
									<label for="firstName"><?php echo bl_text( $data['label_5'] ?? '' ); ?></label>
									<input id="firstName" name="firstName" type="text" required="" autocomplete="given-name" value="<?php echo esc_attr( bl_form_old( 'firstName' ) ); ?>" maxlength="254">
								</div>
								<div class="field">
									<label for="lastName"><?php echo bl_text( $data['label_6'] ?? '' ); ?></label>
									<input id="lastName" name="lastName" type="text" required="" autocomplete="family-name" value="<?php echo esc_attr( bl_form_old( 'lastName' ) ); ?>" maxlength="254">
								</div>
							</div>
							<div class="field">
								<label for="email"><?php echo bl_text( $data['label_7'] ?? '' ); ?></label>
								<input id="email" name="email" type="email" required="" autocomplete="email" value="<?php echo esc_attr( bl_form_old( 'email' ) ); ?>" maxlength="254">
							</div>
							<div class="form-row">
								<div class="field">
									<label for="company"><?php echo bl_text( $data['label_8'] ?? '' ); ?></label>
									<input id="company" name="company" type="text" autocomplete="organization" value="<?php echo esc_attr( bl_form_old( 'company' ) ); ?>" maxlength="254">
								</div>
								<div class="field">
									<label for="phone"><?php echo bl_text( $data['label_9'] ?? '' ); ?></label>
									<input id="phone" name="phone" type="tel" autocomplete="tel" value="<?php echo esc_attr( bl_form_old( 'phone' ) ); ?>" maxlength="254">
								</div>
							</div>
							<div class="field">
								<label for="interest"><?php echo bl_text( $data['label_10'] ?? '' ); ?></label>
								<select id="interest" name="interest">
									<option value=""><?php echo bl_text( $data['option'] ?? '' ); ?></option>
									<?php foreach ( bl_rows( bl_field( 'form_services', 'option' ) ) as $service ) : ?>
										<option value="<?php echo esc_attr( $service['label'] ?? '' ); ?>" <?php selected( bl_form_old( 'interest' ), $service['label'] ?? '' ); ?>><?php echo bl_text( $service['label'] ?? '' ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="field">
								<label for="message"><?php echo bl_text( $data['label_11'] ?? '' ); ?></label>
								<textarea id="message" name="message" maxlength="10000" required="" placeholder="<?php echo esc_attr( $data['placeholder'] ?? '' ); ?>"><?php echo esc_textarea( bl_form_old( 'message' ) ); ?></textarea>
							</div>
							<button type="submit" class="btn btn-primary"><?php echo bl_text( $data['btn_btn_primary'] ?? '' ); ?><?php bl_icon( $data['icon'] ?? '' ); ?></button>
						</form>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>
	<?php
endwhile;

get_footer();
