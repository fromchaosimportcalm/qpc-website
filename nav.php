<?php
// nav.php — included on every page
// $activePage is set in each page file before including this
// e.g. $activePage = 'home';
// Valid values: home, ministries, history, team, sermons, calendar, hymns, visit

function navLink($href, $label, $page, $activePage, $extraClass = '') {
    $class = '';
    $classes = [];
    if ($extraClass) $classes[] = $extraClass;
    if ($page === $activePage) $classes[] = 'active';
    $class = $classes ? ' class="' . implode(' ', $classes) . '"' : '';
    return "<li><a href=\"{$href}\"{$class}>{$label}</a></li>";
}
?>
<div class="nav-overlay" id="navOverlay"></div>
<nav>
  <a href="index.php" class="nav-logo">
    <img src="images/qpc_mini_transparent.png" alt="Queanbeyan Presbyterian Church" class="nav-logo-img">
  </a>
  <button class="nav-hamburger" id="navToggle" aria-label="Open menu">
    <span></span><span></span><span></span>
  </button>
  <ul class="nav-links" id="navLinks">
    <?= navLink('index.php',      'Home',            'home',       $activePage) ?>
    <?= navLink('ministries.php', 'Church Life',     'ministries', $activePage) ?>
    <?= navLink('history.php',    'Our History',     'history',    $activePage) ?>
    <?= navLink('team.php',       'Our Team',        'team',       $activePage) ?>
    <?= navLink('sermons.php',    'Sermons',         'sermons',    $activePage) ?>
    <?= navLink('calendar.php',   'Calendar',        'calendar',   $activePage) ?>
    <?= navLink('hymns.php',      'Hymns',           'hymns',      $activePage) ?>
    <?= navLink('visit.php',      'Plan Your Visit', 'visit',      $activePage, 'nav-cta') ?>
  </ul>
</nav>
