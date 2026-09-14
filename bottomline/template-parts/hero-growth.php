<?php $dashboard = $args["data"] ?? []; ?><div class="hero-dash growth-grid" aria-label="<?php echo esc_attr(($dashboard['aria_label'] ?? '')); ?>"><?php foreach (bl_rows(($dashboard['hero_dash_growth_grid_items'] ?? '')) as $row2): if (!bl_has_content($row2)) { continue; } $svg_id = wp_unique_id('bl-chart-'); ?><?php if (($row2['layout'] ?? '1') === '1'): ?><div class="gg-tile">
<div class="gg-head"><span class="gg-label"><?php echo bl_text(($row2['gg_label'] ?? '')); ?></span><span class="gg-delta up">▲ <span class="num" data-target="<?php echo esc_attr(($row2['data_target'] ?? '')); ?>"><?php echo esc_html(bl_number(($row2['data_target'] ?? ''))); ?></span>%</span></div>
<div class="gg-val"><?php echo bl_text(($row2['gg_val'] ?? '')); ?> <span class="num" data-target="<?php echo esc_attr(($row2['data_target_2'] ?? '')); ?>"><?php echo esc_html(bl_number(($row2['data_target_2'] ?? ''))); ?></span><span class="gg-unit"><?php echo bl_text(($row2['gg_unit'] ?? '')); ?></span></div>
<svg class="gg-chart" viewBox="0 0 160 46" preserveAspectRatio="none">
<defs><linearGradient id="<?php echo esc_attr($svg_id . 'ggf1'); ?>" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#4dd499" stop-opacity="0.45"></stop><stop offset="100%" stop-color="#4dd499" stop-opacity="0"></stop></linearGradient></defs>
<path d="M 4 40 L 30 34 L 56 30 L 82 22 L 108 18 L 134 10 L 156 4 L 156 46 L 4 46 Z" fill="url(#<?php echo esc_attr($svg_id . 'ggf1'); ?>)"></path>
<polyline points="4,40 30,34 56,30 82,22 108,18 134,10 156,4" fill="none" stroke="#4dd499" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></polyline>
</svg>
</div><?php endif; ?><?php if (($row2['layout'] ?? '1') === '2'): ?><div class="gg-tile">
<div class="gg-head"><span class="gg-label"><?php echo bl_text(($row2['gg_label'] ?? '')); ?></span><span class="gg-delta up">▲ <span class="num" data-target="<?php echo esc_attr(($row2['data_target'] ?? '')); ?>"><?php echo esc_html(bl_number(($row2['data_target'] ?? ''))); ?></span>%</span></div>
<div class="gg-val"><span class="num" data-target="<?php echo esc_attr(($row2['data_target_2'] ?? '')); ?>"><?php echo esc_html(bl_number(($row2['data_target_2'] ?? ''))); ?></span><span class="gg-unit"><?php echo bl_text(($row2['gg_unit'] ?? '')); ?></span></div>
<svg class="gg-chart" viewBox="0 0 160 46" preserveAspectRatio="none">
<g fill="#4dd499">
<rect x="6" y="34" width="14" height="10" rx="2"></rect>
<rect x="28" y="30" width="14" height="14" rx="2"></rect>
<rect x="50" y="26" width="14" height="18" rx="2"></rect>
<rect x="72" y="22" width="14" height="22" rx="2"></rect>
<rect x="94" y="16" width="14" height="28" rx="2"></rect>
<rect x="116" y="10" width="14" height="34" rx="2"></rect>
<rect x="138" y="4" width="14" height="40" rx="2"></rect>
</g>
</svg>
</div><?php endif; ?><?php if (($row2['layout'] ?? '1') === '3'): ?><div class="gg-tile">
<div class="gg-head"><span class="gg-label"><?php echo bl_text(($row2['gg_label'] ?? '')); ?></span><span class="gg-delta up">▲ <span class="num" data-target="<?php echo esc_attr(($row2['data_target'] ?? '')); ?>"><?php echo esc_html(bl_number(($row2['data_target'] ?? ''))); ?></span><?php echo bl_text(($row2['gg_delta_up'] ?? '')); ?></span></div>
<div class="gg-val"><span class="num" data-target="<?php echo esc_attr(($row2['data_target_2'] ?? '')); ?>"><?php echo esc_html(bl_number(($row2['data_target_2'] ?? ''))); ?></span><span class="gg-unit">%</span></div>
<svg class="gg-chart" viewBox="0 0 160 46" preserveAspectRatio="none">
<defs><linearGradient id="<?php echo esc_attr($svg_id . 'ggf3'); ?>" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#7de3b3" stop-opacity="0.45"></stop><stop offset="100%" stop-color="#7de3b3" stop-opacity="0"></stop></linearGradient></defs>
<path d="M 4 38 L 26 36 L 48 30 L 70 28 L 92 20 L 114 16 L 136 8 L 156 6 L 156 46 L 4 46 Z" fill="url(#<?php echo esc_attr($svg_id . 'ggf3'); ?>)"></path>
<polyline points="4,38 26,36 48,30 70,28 92,20 114,16 136,8 156,6" fill="none" stroke="#7de3b3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></polyline>
</svg>
</div><?php endif; ?><?php if (($row2['layout'] ?? '1') === '4'): ?><div class="gg-tile">
<div class="gg-head"><span class="gg-label"><?php echo bl_text(($row2['gg_label'] ?? '')); ?></span><span class="gg-delta up">▲ <span class="num" data-target="<?php echo esc_attr(($row2['data_target'] ?? '')); ?>"><?php echo esc_html(bl_number(($row2['data_target'] ?? ''))); ?></span>%</span></div>
<div class="gg-val"><?php echo bl_text(($row2['gg_val'] ?? '')); ?> <span class="num" data-target="<?php echo esc_attr(($row2['data_target_2'] ?? '')); ?>"><?php echo esc_html(bl_number(($row2['data_target_2'] ?? ''))); ?></span><span class="gg-unit"><?php echo bl_text(($row2['gg_unit'] ?? '')); ?></span></div>
<svg class="gg-chart" viewBox="0 0 160 46" preserveAspectRatio="none">
<polyline points="4,10 30,14 56,12 82,20 108,26 134,34 156,40" fill="none" stroke="#e9b872" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="3 3" opacity="0.55"></polyline>
<polyline points="4,20 30,24 56,26 82,32 108,36 134,40 156,42" fill="none" stroke="#4dd499" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></polyline>
</svg>
</div><?php endif; ?><?php endforeach; ?></div>