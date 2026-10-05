<?php
/** Team archive with progressively enhanced pagination. @package Bottomline */
get_header();
$team_page = max( 1, (int) get_query_var( 'paged' ) );
$team_query = new WP_Query( array(
    'post_type' => 'team',
    'post_status' => 'publish',
    'posts_per_page' => max( 1, (int) get_query_var( 'posts_per_page', 10 ) ),
    'paged' => $team_page,
    'orderby' => array( 'menu_order' => 'ASC', 'ID' => 'ASC' ),
) );
?>
<section class="section" id="team-archive">
    <div class="container">
        <h1><?php post_type_archive_title(); ?></h1>
        <div class="team-grid" id="team-archive-grid">
            <?php while ( $team_query->have_posts() ) : $team_query->the_post(); ?>
                <?php get_template_part( 'template-parts/team-card' ); ?>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <div class="team-load-control" style="text-align:center;margin:32px 0">
            <p role="status" aria-live="polite" class="team-load-status"></p>
            <?php if ( $team_page < $team_query->max_num_pages ) : ?>
                <a class="team-load-next" href="<?php echo esc_url( get_pagenum_link( $team_page + 1 ) ); ?>" aria-controls="team-archive-grid">Load more team members</a>
            <?php endif; ?>
        </div>
    </div>
</section>
<script>
(function () {
    'use strict';
    const archive = document.getElementById('team-archive');
    const grid = archive.querySelector('.team-grid');
    const control = archive.querySelector('.team-load-control');
    const status = archive.querySelector('.team-load-status');
    const link = archive.querySelector('.team-load-next');
    if (!link || !window.fetch || !window.IntersectionObserver) return;
    let loading = false;
    let finished = false;
    const observer = new IntersectionObserver(function (entries) {
        if (entries.some(function (entry) { return entry.isIntersecting; })) loadNext();
    }, { rootMargin: '0px 0px 250px 0px' });
    async function loadNext() {
        if (loading || finished) return;
        loading = true;
        observer.unobserve(control);
        grid.setAttribute('aria-busy', 'true');
        link.setAttribute('aria-disabled', 'true');
        status.textContent = 'Loading more team members…';
        try {
            const response = await fetch(link.href, { credentials: 'same-origin' });
            if (!response.ok) throw new Error('Page unavailable');
            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            const nextGrid = page.querySelector('#team-archive-grid');
            if (!nextGrid || !nextGrid.children.length) throw new Error('Team members unavailable');
            const known = new Set(Array.from(grid.querySelectorAll('a[href]'), function (card) { return card.href; }));
            const cards = Array.from(nextGrid.children).filter(function (card) {
                const anchor = card.matches('a[href]') ? card : card.querySelector('a[href]');
                return !anchor || !known.has(anchor.href);
            });
            if (!cards.length) throw new Error('No new team members');
            cards.forEach(function (card) {
                const imported = document.importNode(card, true);
                imported.classList.remove('reveal');
                imported.querySelectorAll('.reveal').forEach(function (element) { element.classList.remove('reveal'); });
                grid.appendChild(imported);
            });
            const next = page.querySelector('.team-load-next');
            if (next) {
                link.href = next.href;
                status.textContent = cards.length + ' more team members loaded.';
                observer.observe(control);
            } else {
                finished = true;
                link.hidden = true;
                status.textContent = 'All team members loaded.';
                observer.disconnect();
            }
        } catch (error) {
            status.textContent = 'Could not load more team members. Select Load more to try again.';
        } finally {
            loading = false;
            grid.removeAttribute('aria-busy');
            link.removeAttribute('aria-disabled');
        }
    }
    link.addEventListener('click', function (event) {
        event.preventDefault();
        loadNext();
    });
    observer.observe(control);
}());
</script>
<?php get_footer(); ?>
