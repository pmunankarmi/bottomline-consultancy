<?php
/** Structured content; all layout remains in this template. */
$data = bl_field('section_2', $args['source'] ?? get_the_ID());
if (!bl_has_content($data)) { return; }
?>
<section class="section section-soft process-section">
<div class="container">
<div class="section-head center">
<h2><?php echo bl_text(($data['heading'] ?? '')); ?></h2>
<p><?php echo bl_text(($data['description'] ?? '')); ?></p>
</div>
<div class="process-flow"><?php foreach (bl_rows(($data['process_flow_items'] ?? '')) as $row11): if (!bl_has_content($row11)) { continue; } ?><div class="process-step reveal">
<div class="process-num"><?php echo bl_text(($row11['process_num'] ?? '')); ?></div>
<h3><?php echo bl_text(($row11['card_title'] ?? '')); ?></h3>
<p><?php echo bl_text(($row11['description'] ?? '')); ?></p>
</div><?php endforeach; ?></div>
</div>
</section>
