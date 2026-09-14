<?php
if (!bl_rows(bl_field('testimonials', 'option'))) { return; }
/** Structured content; all layout remains in this template. */
$data = bl_field('section_9', $args['source'] ?? get_the_ID());
if (!bl_has_content($data)) { return; }
?>
<section class="testimonial-section">
<div class="container">
<div class="section-head center" style="margin-bottom: 40px;"><h2><?php echo bl_text(($data['heading'] ?? '')); ?></h2></div>
<?php get_template_part('template-parts/testimonials'); ?>
</div>
</section>
