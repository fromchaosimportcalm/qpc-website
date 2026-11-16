<?php
/**
 * Title: Page header — simple
 * Slug: qpc/page-header
 * Categories: qpc
 * Description: Green band for the top of a page: small label, big title and an intro sentence.
 */
?>
<!-- wp:group {"tagName":"header","className":"page-header","layout":{"type":"default"}} -->
<header class="wp-block-group page-header"><!-- wp:group {"className":"page-header-inner page-header-inner--single","layout":{"type":"default"}} -->
<div class="wp-block-group page-header-inner page-header-inner--single"><!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"page-eyebrow"} -->
<p class="page-eyebrow">Small label</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"page-title"} -->
<h1 class="wp-block-heading page-title">Page Title</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"page-lead"} -->
<p class="page-lead">One or two sentences introducing this page.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></header>
<!-- /wp:group -->
