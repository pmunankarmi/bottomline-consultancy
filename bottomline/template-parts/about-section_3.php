<?php
/** Structured content; all layout remains in this template. */
$data = bl_field('section_3', $args['source'] ?? get_the_ID());
if (!bl_has_content($data)) { return; }
?>
<section class="section">
<div class="container">
<div class="section-head center">
<h2><?php echo bl_text(($data['heading'] ?? '')); ?></h2>
<p><?php echo bl_text(($data['description'] ?? '')); ?></p>
</div>
<div class="targets"><?php foreach (bl_rows(($data['targets_items'] ?? '')) as $row9): if (!bl_has_content($row9)) { continue; } ?><div class="target reveal">
<div class="target-icon">
<?php bl_icon(($row9['icon'] ?? '')); ?>
</div>
<h4><?php echo bl_text(($row9['label'] ?? '')); ?></h4>
<p style="font-size: 0.9375rem; color: var(--muted);"><?php echo bl_text(($row9['description'] ?? '')); ?></p>
</div><?php endforeach; ?></div>
</div>
</section>
