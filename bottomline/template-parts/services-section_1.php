<?php
/** Structured content; all layout remains in this template. */
$data = bl_field('section_1', $args['source'] ?? get_the_ID());
if (!bl_has_content($data)) { return; }
?>
<section class="section">
<div class="container">
<div class="grid" style="gap: 24px;"><?php foreach (bl_rows(($data['grid_items'] ?? '')) as $row10): if (!bl_has_content($row10)) { continue; } ?><article class="service-card reveal">
<div class="card-icon">
<?php bl_icon(($row10['icon'] ?? '')); ?>
</div>
<div class="service-card-body">
<h3><?php echo bl_text(($row10['card_title'] ?? '')); ?></h3>
<p><?php echo bl_text(($row10['description'] ?? '')); ?></p>
</div>
</article><?php endforeach; ?></div>
</div>
</section>
