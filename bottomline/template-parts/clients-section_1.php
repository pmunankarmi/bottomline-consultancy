<?php
if (!bl_rows(bl_field('clients', 'option'))) { return; }
/** Structured content; all layout remains in this template. */
$data = bl_field('section_1', $args['source'] ?? get_the_ID());

?>
<section class="section">
<div class="container">
<?php get_template_part('template-parts/clients'); ?>
</div>
</section>
