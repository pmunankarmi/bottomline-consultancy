<?php
/** Structured content; all layout remains in this template. */
$data = bl_field('section_0', $args['source'] ?? get_the_ID());
if (!bl_has_content($data)) { return; }
?>
<section class="page-hero">
<svg class="page-hero-arcs" viewBox="0 0 600 600" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
<defs>
<linearGradient id="teamArc1" x1="0%" y1="0%" x2="100%" y2="100%">
<stop offset="0%" stop-color="#009A66" stop-opacity="0.7"></stop>
<stop offset="100%" stop-color="#00553A" stop-opacity="0.5"></stop>
</linearGradient>
<linearGradient id="teamArc2" x1="0%" y1="0%" x2="100%" y2="100%">
<stop offset="0%" stop-color="#009A66" stop-opacity="0.3"></stop>
<stop offset="100%" stop-color="#009A66" stop-opacity="0.1"></stop>
</linearGradient>
</defs>
<path d="M 600 0 A 600 600 0 0 0 0 600 L 0 400 A 400 400 0 0 1 400 0 Z" fill="url(#teamArc1)"></path>
<path d="M 600 100 A 500 500 0 0 0 100 600 L 100 480 A 380 380 0 0 1 480 100 Z" fill="url(#teamArc2)"></path>
</svg>
<div class="container">
<h1><?php the_title(); ?></h1>
<p><?php echo bl_text(($data['description'] ?? '')); ?></p>
</div>
</section>
