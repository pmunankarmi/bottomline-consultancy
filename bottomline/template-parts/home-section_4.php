<?php
if (!(int) wp_count_posts('team')->publish) { return; }
/** Structured content; all layout remains in this template. */
$data = bl_field('section_4', $args['source'] ?? get_the_ID());
if (!bl_has_content($data)) { return; }
?>
<section class="section">
<div class="container">
<div class="section-head center">
<h2><?php echo bl_text(($data['heading'] ?? '')); ?></h2>
<p><?php echo bl_text(($data['description'] ?? '')); ?></p>
</div>
<?php get_template_part('template-parts/team-list'); ?>
<div style="text-align: center; margin-top: 36px;">
<a href="<?php echo esc_url(bl_link_url(($data['destination'] ?? ''))); ?>" class="btn btn-ghost"> <?php echo bl_text(($data['btn_btn_ghost'] ?? '')); ?> <?php bl_icon(($data['icon'] ?? '')); ?>
</a>
</div>
</div>
</section>
