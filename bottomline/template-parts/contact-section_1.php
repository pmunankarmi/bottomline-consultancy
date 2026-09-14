<?php
/** Structured content; all layout remains in this template. */
$data = bl_field('section_1', $args['source'] ?? get_the_ID());
if (!bl_has_content($data)) { return; }
?>
<section class="section">
<div class="container">
<div class="contact-grid">
<div class="reveal">
<div class="contact-info-block">
<h4><?php echo bl_text(($data['label'] ?? '')); ?></h4>
<?php get_template_part('template-parts/contact-phone'); ?>
</div>
<div class="contact-info-block">
<h4><?php echo bl_text(($data['label_2'] ?? '')); ?></h4>
<?php get_template_part('template-parts/contact-email'); ?>
</div>
<div class="contact-info-block">
<h4><?php echo bl_text(($data['label_3'] ?? '')); ?></h4>
<?php get_template_part('template-parts/contact-website_label'); ?>
</div>
<div class="contact-info-block" style="margin-top: 40px;">
<h4><?php echo bl_text(($data['label_4'] ?? '')); ?></h4>
<?php get_template_part('template-parts/branches-contact'); ?>
</div>
</div>
<div class="reveal" id="contact-form">
<form class="form-card" id="contactForm" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"><?php bl_form_controls(); ?>
<h3 style="font-size: 1.25rem; margin-bottom: 8px;"><?php echo bl_text(($data['card_title'] ?? '')); ?></h3>
<p style="font-size: 0.9375rem; color: var(--muted); margin-bottom: 24px;"><?php echo bl_text(($data['description'] ?? '')); ?></p>
<div class="form-row">
<div class="field">
<label for="firstName"><?php echo bl_text(($data['label_5'] ?? '')); ?></label>
<input id="firstName" name="firstName" type="text" required="" autocomplete="given-name" value="<?php echo esc_attr(bl_form_old('firstName')); ?>" maxlength="254">
</div>
<div class="field">
<label for="lastName"><?php echo bl_text(($data['label_6'] ?? '')); ?></label>
<input id="lastName" name="lastName" type="text" required="" autocomplete="family-name" value="<?php echo esc_attr(bl_form_old('lastName')); ?>" maxlength="254">
</div>
</div>
<div class="field">
<label for="email"><?php echo bl_text(($data['label_7'] ?? '')); ?></label>
<input id="email" name="email" type="email" required="" autocomplete="email" value="<?php echo esc_attr(bl_form_old('email')); ?>" maxlength="254">
</div>
<div class="form-row">
<div class="field">
<label for="company"><?php echo bl_text(($data['label_8'] ?? '')); ?></label>
<input id="company" name="company" type="text" autocomplete="organization" value="<?php echo esc_attr(bl_form_old('company')); ?>" maxlength="254">
</div>
<div class="field">
<label for="phone"><?php echo bl_text(($data['label_9'] ?? '')); ?></label>
<input id="phone" name="phone" type="tel" autocomplete="tel" value="<?php echo esc_attr(bl_form_old('phone')); ?>" maxlength="254">
</div>
</div>
<div class="field">
<label for="interest"><?php echo bl_text(($data['label_10'] ?? '')); ?></label>
<select id="interest" name="interest">
<option value=""><?php echo bl_text(($data['option'] ?? '')); ?></option>








<?php get_template_part('template-parts/form-services'); ?></select>
</div>
<div class="field">
<label for="message"><?php echo bl_text(($data['label_11'] ?? '')); ?></label>
<textarea id="message" name="message" maxlength="10000" required="" placeholder="<?php echo esc_attr(($data['placeholder'] ?? '')); ?>"><?php echo esc_textarea(bl_form_old('message')); ?></textarea>
</div>
<button type="submit" class="btn btn-primary"> <?php echo bl_text(($data['btn_btn_primary'] ?? '')); ?> <?php bl_icon(($data['icon'] ?? '')); ?>
</button>

</form>
</div>
</div>
</div>
</section>
