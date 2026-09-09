<?php
/**
 * Pied de page du thème Aurora.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<footer class="footer" id="contact">
	<div class="container">
		<div class="footer-grid">
			<div>
				<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<span class="logo">✦</span>
					<span><?php bloginfo( 'name' ); ?></span>
				</a>
				<p><?php bloginfo( 'description' ); ?></p>
			</div>
			<div>
				<h4><?php esc_html_e( 'Navigation', 'aurora' ); ?></h4>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Accueil', 'aurora' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/#services' ) ); ?>"><?php esc_html_e( 'Services', 'aurora' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/#about' ) ); ?>"><?php esc_html_e( 'À propos', 'aurora' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/#pricing' ) ); ?>"><?php esc_html_e( 'Tarifs', 'aurora' ); ?></a></li>
				</ul>
			</div>
			<div>
				<h4><?php esc_html_e( 'Services', 'aurora' ); ?></h4>
				<ul>
					<li><a href="#"><?php esc_html_e( 'Design UI/UX', 'aurora' ); ?></a></li>
					<li><a href="#"><?php esc_html_e( 'Développement web', 'aurora' ); ?></a></li>
					<li><a href="#"><?php esc_html_e( 'SEO & performance', 'aurora' ); ?></a></li>
					<li><a href="#"><?php esc_html_e( 'Maintenance', 'aurora' ); ?></a></li>
				</ul>
			</div>
			<div>
				<h4><?php esc_html_e( 'Contact', 'aurora' ); ?></h4>
				<ul>
					<li>hello@example.ch</li>
					<li>+41 00 000 00 00</li>
					<li>Lausanne, Suisse</li>
				</ul>
			</div>
		</div>
		<div class="footer-bottom">
			<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> — <?php esc_html_e( 'Tous droits réservés.', 'aurora' ); ?></span>
			<span><?php esc_html_e( 'Fait avec ', 'aurora' ); ?>💜 <?php esc_html_e( 'pour un exercice WordPress', 'aurora' ); ?></span>
		</div>
	</div>
</footer>

<script>
(function () {
	// Navbar : fond au scroll.
	var nav = document.getElementById('aurora-nav');
	function onScroll() { nav.classList.toggle('scrolled', window.scrollY > 24); }
	window.addEventListener('scroll', onScroll, { passive: true });
	onScroll();

	// Révélation des sections au scroll.
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