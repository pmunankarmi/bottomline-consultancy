<?php
/** Structured content; all layout remains in this template. */
$data = bl_field('section_1', $args['source'] ?? get_the_ID());
if (!bl_has_content($data)) { return; }
?>
<section class="stats-section">
<div class="dot-grid" aria-hidden="true"></div>
<div class="container">
<div class="stats-grid"><?php foreach (bl_rows(($data['stats_grid_items'] ?? '')) as $row3): if (!bl_has_content($row3)) { continue; } ?><?php if (($row3['layout'] ?? '1') === '1'): ?><div class="stat reveal">
<div class="stat-num"><span data-count="<?php echo esc_attr(($row3['data_count'] ?? '')); ?>"><?php echo bl_text(($row3['data_count'] ?? '')); ?></span><span class="suffix">+</span></div>
<div class="stat-label"><?php echo bl_text(($row3['stat_label'] ?? '')); ?></div>
<div class="stat-sub"><?php echo bl_text(($row3['stat_sub'] ?? '')); ?></div>
</div><?php endif; ?><?php if (($row3['layout'] ?? '1') === '2'): ?><div class="stat reveal">
<div class="stat-num stat-num-text"><?php echo bl_text(($row3['stat_num_stat_num_text'] ?? '')); ?></div>
<div class="stat-label"><?php echo bl_text(($row3['stat_label'] ?? '')); ?></div>
<div class="stat-sub"><?php echo bl_text(($row3['stat_sub'] ?? '')); ?></div>
</div><?php endif; ?><?php if (($row3['layout'] ?? '1') === '3'): ?><div class="stat reveal">
<div class="stat-num"><?php echo bl_text(($row3['stat_num'] ?? '')); ?></div>
<div class="stat-label"><?php echo bl_text(($row3['stat_label'] ?? '')); ?></div>
<div class="stat-sub"><?php echo bl_text(($row3['stat_sub'] ?? '')); ?></div>
</div><?php endif; ?><?php endforeach; ?></div>
</div>
</section>
