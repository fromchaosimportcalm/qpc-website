<?php
/**
 * Pages: the content is the whole page body. Every section is a block or a
 * pattern from the "QPC Sections" category, so there is no extra wrapper here.
 */
get_header();

while ( have_posts() ) :
	the_post();
	the_content();
endwhile;

get_footer();
