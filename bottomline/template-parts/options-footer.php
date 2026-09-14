<?php
/** Structured content; all layout remains in this template. */
$data = bl_field('footer', $args['source'] ?? get_the_ID());
if (!bl_has_content($data)) { return; }
?>
<footer class="site-footer">
<div class="container">
<div class="footer-grid">
<div class="footer-about">
<?php get_template_part('template-parts/footer-logo'); ?>
<p><?php echo bl_text(($data['description'] ?? '')); ?></p>
</div>
<div>
<div class="footer-heading"><?php echo bl_text(($data['footer_heading'] ?? '')); ?></div>
<?php get_template_part('template-parts/footer-menu'); ?>
</div>
<div>
<div class="footer-heading"><?php echo bl_text(($data['footer_heading_2'] ?? '')); ?></div>
<?php get_template_part('template-parts/branches-footer'); ?>
</div>
<?php get_template_part('template-parts/footer-contact'); ?>
</div>
<div class="footer-bottom">
<span>© <span id="year"></span> <?php echo bl_text(($data['label'] ?? '')); ?></span>
<span><?php echo bl_text(($data['label_2'] ?? '')); ?></span>
</div>
</div>
</footer>
