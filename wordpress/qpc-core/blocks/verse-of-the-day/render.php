<?php
/**
 * Verse of the Day: all verses are sent with the page and the browser picks
 * today's one, so it stays correct even if the page is cached.
 */
defined( 'ABSPATH' ) || exit;
$qpc_verses = json_decode( (string) file_get_contents( QPC_CORE_DIR . '/data/verses.json' ), true ) ?: array();
?>
<section class="votd-section">
      <div class="votd-inner">
        <div class="votd-label">Verse of the Day</div>
        <blockquote class="votd-text" id="votdText">&nbsp;</blockquote>
        <cite class="votd-ref" id="votdRef"></cite>
      </div>
    </section>
<script type="application/json" id="qpc-verses"><?php echo wp_json_encode( $qpc_verses, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE ); ?></script>
<script>
(function () {
  var verses = JSON.parse(document.getElementById('qpc-verses').textContent || '[]');
  if (!verses.length) return;
  var now = new Date();
  var day = Math.floor((now - new Date(now.getFullYear(), 0, 0)) / 86400000);
  var v = verses[day % verses.length];
  var t = document.getElementById('votdText'), r = document.getElementById('votdRef');
  t.textContent = '\u201C' + v.text + '\u201D';
  r.textContent = v.reference + ' \u00B7 ESV';
  requestAnimationFrame(function () { t.classList.add('loaded'); r.classList.add('loaded'); });
})();
</script>
