<?php
/** Structured content; all layout remains in this template. */
$data = bl_field('section_2', $args['source'] ?? get_the_ID());
if (!bl_has_content($data)) { return; }
?>
<section class="section section-soft">
<div class="container">
<div class="section-head">
<h2><?php echo bl_text(($data['heading'] ?? '')); ?></h2>
</div>
<div class="feature-list"><?php foreach (bl_rows(($data['feature_list_items'] ?? '')) as $row8): if (!bl_has_content($row8)) { continue; } ?><div class="feature reveal">
<h4><?php echo bl_text(($row8['label'] ?? '')); ?></h4>
<p><?php echo bl_text(($row8['description'] ?? '')); ?></p>
</div><?php endforeach; ?></div>
</div>
</section>
