<?php
/** Structured content; all layout remains in this template. */
$data = bl_field('home_summary', $args['source'] ?? get_the_ID());
if (!bl_has_content($data)) { return; }
?>
<section class="section">
<div class="container">
<div class="image-split">
<div class="image-side reveal">
<?php bl_image(($data['image'] ?? ''), ''); ?>
<div class="image-badge">
<div class="image-badge-icon">
<?php bl_icon(($data['icon'] ?? '')); ?>
</div>
<div class="image-badge-text">
<strong><?php echo bl_text(($data['strong'] ?? '')); ?></strong>
<span><?php echo bl_text(($data['label'] ?? '')); ?></span>
</div>
</div>
</div>
<div class="reveal">
<h2 style="margin-bottom: 16px;"><?php echo bl_text(($data['heading'] ?? '')); ?></h2>
<p class="lead" style="margin-bottom: 32px;"><?php echo bl_text(($data['lead'] ?? '')); ?></p>
<div class="features-stacked"><?php foreach (bl_rows(($data['features_stacked_items'] ?? '')) as $row5): if (!bl_has_content($row5)) { continue; } ?><div class="feature">
<div class="feature-icon">
<?php bl_icon(($row5['icon'] ?? '')); ?>
</div>
<div class="feature-body">
<h4><?php echo bl_text(($row5['label'] ?? '')); ?></h4>
<p><?php echo bl_text(($row5['description'] ?? '')); ?></p>
</div>
</div><?php endforeach; ?></div>
<div style="margin-top: 32px;">
<a href="<?php echo esc_url(get_permalink($args['source'])); ?>" class="btn-arrow"><?php echo bl_text(($data['btn_arrow'] ?? '')); ?> <span class="arrow">→</span></a>
</div>
</div>
</div>
</div>
</section>
