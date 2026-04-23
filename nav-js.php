<?php // nav-js.php — hamburger nav JavaScript, included once per page ?>
const toggle  = document.getElementById('navToggle');
const links   = document.getElementById('navLinks');
const overlay = document.getElementById('navOverlay');
function openMenu(){toggle.classList.add('open');links.classList.add('open');overlay.classList.add('open');document.body.style.overflow='hidden';}
function closeMenu(){toggle.classList.remove('open');links.classList.remove('open');overlay.classList.remove('open');document.body.style.overflow='';}
toggle.addEventListener('click',()=>links.classList.contains('open')?closeMenu():openMenu());
overlay.addEventListener('click',closeMenu);
links.querySelectorAll('a').forEach(a=>a.addEventListener('click',closeMenu));
