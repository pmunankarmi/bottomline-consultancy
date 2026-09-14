<?php
if (!bl_rows(bl_field('branches', 'option'))) { return; }
/** Structured content; all layout remains in this template. */
$data = bl_field('section_8', $args['source'] ?? get_the_ID());
if (!bl_has_content($data)) { return; }
?>
<section class="precise-map-section">
<div class="container">
<div class="section-head center">
<h2><?php echo bl_text(($data['heading'] ?? '')); ?></h2>
<p><?php echo bl_text(($data['description'] ?? '')); ?></p>
</div>
<?php get_template_part('template-parts/branches-map'); ?>
</div>
</section>
