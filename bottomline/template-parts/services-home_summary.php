<?php
/** Structured content; all layout remains in this template. */
$data = bl_field('home_summary', $args['source'] ?? get_the_ID());
if (!bl_has_content($data)) { return; }
?>
<section class="section section-soft">
<div class="container">
<div class="section-head center">
<h2><?php echo bl_text(($data['heading'] ?? '')); ?></h2>
<p><?php echo bl_text(($data['description'] ?? '')); ?></p>
</div>
<div class="bento"><?php foreach (bl_rows(($data['bento_items'] ?? '')) as $row6): if (!bl_has_content($row6)) { continue; } ?><?php if (($row6['layout'] ?? '1') === '1'): ?><article class="bento-tile featured reveal">
<div class="card-icon">
<?php bl_icon(($row6['icon'] ?? '')); ?>
</div>
<h3><?php echo bl_text(($row6['card_title'] ?? '')); ?></h3>
<p><?php echo bl_text(($row6['description'] ?? '')); ?></p>
<a href="<?php echo esc_url(get_permalink($args['source'])); ?>" class="bento-cta"> <?php echo bl_text(($row6['bento_cta'] ?? '')); ?> <?php bl_icon(($row6['icon_2'] ?? '')); ?>
</a>
</article><?php endif; ?><?php if (($row6['layout'] ?? '1') === '2'): ?><article class="bento-tile reveal">
<div class="card-icon">
<?php bl_icon(($row6['icon'] ?? '')); ?>
</div>
<h3><?php echo bl_text(($row6['card_title'] ?? '')); ?></h3>
<p><?php echo bl_text(($row6['description'] ?? '')); ?></p>
<span class="bento-meta"><?php echo bl_text(($row6['bento_meta'] ?? '')); ?></span>
</article><?php endif; ?><?php endforeach; ?></div>
<div style="text-align: center; margin-top: 36px;">
<a href="<?php echo esc_url(get_permalink($args['source'])); ?>" class="btn btn-ghost"> <?php echo bl_text(($data['btn_btn_ghost'] ?? '')); ?> <?php bl_icon(($data['icon'] ?? '')); ?>
</a>
</div>
</div>
</section>
