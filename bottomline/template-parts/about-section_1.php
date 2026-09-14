<?php
/** Structured content; all layout remains in this template. */
$data = bl_field('section_1', $args['source'] ?? get_the_ID());
if (!bl_has_content($data)) { return; }
?>
<section class="section">
<div class="container">
<div class="split">
<div class="split-content reveal">
<h2><?php echo bl_text(($data['heading'] ?? '')); ?></h2>
<p><?php echo bl_text(($data['description'] ?? '')); ?></p>
<p><?php echo bl_text(($data['description_2'] ?? '')); ?></p>
</div>
<div class="split-visual reveal" aria-hidden="true">
<?php bl_icon(($data['icon'] ?? '')); ?>
</div>
</div>
</div>
</section>
