<?php
/** Structured content; all layout remains in this template. */
$data = bl_field('section_2', $args['source'] ?? get_the_ID());
if (!bl_has_content($data)) { return; }
?>
<section class="section why-choose">
<div class="why-arcs" aria-hidden="true">
<?php bl_icon(($data['icon'] ?? '')); ?>
</div>
<div class="container">
<div class="why-grid">
<div class="why-intro reveal">
<h2><?php echo bl_text(($data['heading'] ?? '')); ?></h2>
<p class="lead"><?php echo bl_text(($data['lead'] ?? '')); ?></p>
<p><?php echo bl_text(($data['description'] ?? '')); ?></p>
</div>
<ul class="why-list reveal" role="list"><?php foreach (bl_rows(($data['why_list_reveal_items'] ?? '')) as $row4): if (!bl_has_content($row4)) { continue; } ?><li class="why-item">
<span class="why-check" aria-hidden="true">
<?php bl_icon(($row4['icon'] ?? '')); ?>
</span>
<span><?php echo bl_text(($row4['label'] ?? '')); ?></span>
</li><?php endforeach; ?></ul>
</div>
</div>
</section>
