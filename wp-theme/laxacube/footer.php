<?php
/**
 * Pied de page du thème LaxaCube.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$assets = get_template_directory_uri() . '/assets/img/';
?>

<a id="chat-launcher" href="https://bit.ly/discord-laxacube" target="_blank" rel="noopener" title="Discord LaxaCube" aria-label="Discord LaxaCube">
	<svg fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.97-4.03 9-9 9-1.14 0-2.23-.21-3.21-.6L4 22l1.65-3.4A9 9 0 113 12c0-4.97 4.03-9 9-9s9 4.03 9 9z"/></svg>
</a>

<footer class="site-footer">
	<div>
		<img src="<?php echo esc_url( $assets . 'laxapouruptime.png' ); ?>" alt="Logo LaxaCube">
		<div><strong>LaxaCube</strong> — <?php bloginfo( 'description' ); ?></div>
	</div>
	<nav aria-label="Navigation pied de page">
		<ul>
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>#accueil">Accueil</a></li>
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>#rejnnoindre">Rejoindre</a></li>
			<li><a href="<?php echo esc_url( home_url( '/wiki/' ) ); ?>">Wiki</a></li>
			<li><a href="<?php echo esc_url( home_url( '/images/' ) ); ?>">Images</a></li>
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>#team">Équipe</a></li>
			<li><a href="https://bit.ly/discord-laxacube" target="_blank" rel="noopener">Discord</a></li>
		</ul>
	</nav>
	<div>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> LaxaCube — <?php esc_html_e( 'Serveur Minecraft Faction PvP', 'laxacube' ); ?></div>
</footer>

<!-- Compteur de joueurs en temps reel (API publique mcsrvstat) -->
<script>
(function () {
	var el = document.querySelector('.player-count');
	if (!el) return;
	var dot = el.querySelector('.dot');
	var label = el.querySelector('.num-label');
	function set(n, online) {
		if (dot) dot.className = 'dot' + (online ? '' : ' offline');
		if (label) label.textContent = (online ? n : 0) + ' Joueur' + (n > 1 ? 's' : '') + ' en ligne actuellement';
	}
	fetch('https://api.mcsrvstat.us/3/laxacube.ch')
		.then(function (r) { return r.json(); })
		.then(function (d) {
			if (d && d.online === true) {
				set((d.players && d.players.online) || 0, true);
			} else {
				set(0, false);
			}
		})
		.catch(function () { /* garde l'etat par defaut */ });
})();
</script>

<script>
(function () {
	var els = document.querySelectorAll('.reveal');
	if ('IntersectionObserver' in window && els.length) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (e) {
				if (e.isIntersecting) { e.target.classList.add('visible'); io.unobserve(e.target); }
			});
		}, { threshold: 0.12 });
		els.forEach(function (el) { io.observe(el); });
	} else {
		els.forEach(function (el) { el.classList.add('visible'); });
	}
})();
</script>

<?php wp_footer(); ?>
</body>
</html>