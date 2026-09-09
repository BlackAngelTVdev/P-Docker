<?php
/**
 * Page d'accueil spectaculaire du thème Aurora.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<!-- ============ HERO ============ -->
<section class="hero">
	<div class="hero-bg"></div>
	<div class="hero-grid"></div>
	<div class="aurora-blob b1"></div>
	<div class="aurora-blob b2"></div>

	<div class="container">
		<span class="eyebrow"><?php esc_html_e( 'Agence digitale · Lausanne', 'aurora' ); ?></span>
		<h1><?php esc_html_e( 'Nous donnons vie à des expériences web', 'aurora' ); ?> <span class="grad-text"><?php esc_html_e( 'inoubliables', 'aurora' ); ?></span></h1>
		<p class="lead"><?php esc_html_e( 'Design, développement et stratégie : nous transformons vos idées en produits digitaux rapides, élégants et performants.', 'aurora' ); ?></p>

		<div class="hero-btns">
			<a class="btn btn-primary" href="#services"><?php esc_html_e( 'Découvrir nos services', 'aurora' ); ?> →</a>
			<a class="btn btn-ghost" href="#pricing"><?php esc_html_e( 'Voir les tarifs', 'aurora' ); ?></a>
		</div>

		<div class="hero-stats">
			<div><div class="num">120+</div><div class="lbl"><?php esc_html_e( 'Projets livrés', 'aurora' ); ?></div></div>
			<div><div class="num">98 %</div><div class="lbl"><?php esc_html_e( 'Clients satisfaits', 'aurora' ); ?></div></div>
			<div><div class="num">15</div><div class="lbl"><?php esc_html_e( 'Années d’expérience', 'aurora' ); ?></div></div>
			<div><div class="num">24/7</div><div class="lbl"><?php esc_html_e( 'Support', 'aurora' ); ?></div></div>
		</div>

		<div class="marquee" aria-hidden="true">
			<div class="marquee-track">
				<span>✦ NOVA</span><span>✦ STUDIO</span><span>✦ DESIGN</span><span>✦ DEVELOPPEMENT</span><span>✦ SEO</span><span>✦ CLOUD</span>
				<span>✦ NOVA</span><span>✦ STUDIO</span><span>✦ DESIGN</span><span>✦ DEVELOPPEMENT</span><span>✦ SEO</span><span>✦ CLOUD</span>
			</div>
		</div>
	</div>
</section>

<!-- ============ SERVICES ============ -->
<section class="section" id="services">
	<div class="container">
		<div class="section-head center reveal">
			<span class="eyebrow"><?php esc_html_e( 'Nos services', 'aurora' ); ?></span>
			<h2><?php esc_html_e( 'Tout ce qu’il faut pour briller en ligne', 'aurora' ); ?></h2>
			<p><?php esc_html_e( 'De la première idée jusqu’à la mise en production, nous couvrons chaque étape de votre projet.', 'aurora' ); ?></p>
		</div>

		<div class="services-grid">
			<?php
			$services = array(
				array( 'icon' => '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v10a1 1 0 01-1 1H9l-4 4V6a1 1 0 011-1z"/></svg>', 'title' => 'Design UI/UX', 'text' => 'Des interfaces élégantes, pensées pour vos utilisateurs et converties pour votre business.' ),
				array( 'icon' => '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>', 'title' => 'Développement web', 'text' => 'Sites sur mesure, rapides et sécurisés : WordPress, headless, Progressive Web Apps.' ),
				array( 'icon' => '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v3m0 12v3M3 12h3m12 0h3M5.6 5.6l2.1 2.1m8.6 8.6l2.1 2.1m0-12.8l-2.1 2.1M7.7 16.3l-2.1 2.1"/></svg>', 'title' => 'SEO & performance', 'text' => 'Soyez visible sur Google et rapide partout. Lighthouse 90+, c’est notre baseline.' ),
				array( 'icon' => '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.99 7 7 0 00-13.24 2.5A4 4 0 003 15z"/></svg>', 'title' => 'Cloud & DevOps', 'text' => 'Déploiement Docker/Swarm, monitoring et haute disponibilité : votre site ne dort jamais.' ),
				array( 'icon' => '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>', 'title' => 'E-commerce', 'text' => 'Boutiques en ligne complètes : paiement, logistique, expérience d’achat fluide.' ),
				array( 'icon' => '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>', 'title' => 'Maintenance 24/7', 'text' => 'Mises à jour, sauvegardes et supervision : on s’occupe de tout, vous dormez tranquille.' ),
			);

			foreach ( $services as $s ) {
				echo '<div class="card reveal"><div class="icon">' . $s['icon'] . '</div><h3>' . esc_html( $s['title'] ) . '</h3><p>' . esc_html( $s['text'] ) . '</p></div>';
			}
			?>
		</div>
	</div>
</section>

<!-- ============ STATS BAND ============ -->
<section class="section" style="padding-top:0;">
	<div class="container">
		<div class="stats-band reveal">
			<div><div class="num grad-text">120+</div><div class="lbl"><?php esc_html_e( 'Projets', 'aurora' ); ?></div></div>
			<div><div class="num grad-text">40+</div><div class="lbl"><?php esc_html_e( 'Clients actifs', 'aurora' ); ?></div></div>
			<div><div class="num grad-text">8</div><div class="lbl"><?php esc_html_e( 'Récompenses', 'aurora' ); ?></div></div>
			<div><div class="num grad-text">2.4 s</div><div class="lbl"><?php esc_html_e( 'Temps de chargement moyen', 'aurora' ); ?></div></div>
		</div>
	</div>
</section>

<!-- ============ ABOUT / SPLIT ============ -->
<section class="section" id="about">
	<div class="container">
		<div class="split">
			<div class="split-visual reveal">
				<span class="badge">Create<br>&amp; Shine</span>
			</div>
			<div class="reveal">
				<span class="eyebrow"><?php esc_html_e( 'À propos', 'aurora' ); ?></span>
				<h2><?php esc_html_e( 'Une équipe, une obsession : la qualité', 'aurora' ); ?></h2>
				<p style="color:var(--text-dim);"><?php esc_html_e( 'Basés à Lausanne, nous sommes un studio indépendant qui accompagne startups et PME dans leur transformation digitale.', 'aurora' ); ?></p>
				<ul class="checks">
					<li><?php esc_html_e( 'Processus transparent : vous savez toujours où en est votre projet', 'aurora' ); ?></li>
					<li><?php esc_html_e( 'Code de qualité, testé et documenté', 'aurora' ); ?></li>
					<li><?php esc_html_e( 'Infrastructure cloud résiliente (Docker Swarm, sauvegardes)', 'aurora' ); ?></li>
					<li><?php esc_html_e( 'Accompagnement après livraison inclus', 'aurora' ); ?></li>
				</ul>
				<a class="btn btn-ghost" href="#contact"><?php esc_html_e( 'Rencontrer l’équipe', 'aurora' ); ?> →</a>
			</div>
		</div>
	</div>
</section>

<!-- ============ TEMOIGNAGES ============ -->
<section class="section" style="background:var(--bg-alt);">
	<div class="container">
		<div class="section-head center reveal">
			<span class="eyebrow"><?php esc_html_e( 'Ils nous font confiance', 'aurora' ); ?></span>
			<h2><?php esc_html_e( 'Ce que disent nos clients', 'aurora' ); ?></h2>
		</div>

		<div class="testi-grid">
			<?php
			$testis = array(
				array( 'who' => 'Sophie M.', 'role' => 'CEO · TechStart', 'text' => 'Notre site est passé de “on en a un” à “il est superbe”. Le trafic a doublé en trois mois. Merci !' ),
				array( 'who' => 'Julien R.', 'role' => 'Fondateur · ShopBox', 'text' => 'Une équipe réactive, des conseils précieux et une boutique en ligne enfin rapide. Je recommande les yeux fermés.' ),
				array( 'who' => 'Camille D.', 'role' => 'PM · Groupe Alpha', 'text' => 'La mise en production a été un sans-faute : zéro downtime, back-ups automatiques, suivi impeccable.' ),
			);

			foreach ( $testis as $t ) {
				$initial = mb_strtoupper( mb_substr( $t['who'], 0, 1 ) );
				echo '<div class="testi reveal"><div class="stars">★★★★★</div><p>' . esc_html( $t['text'] ) . '</p><div class="who"><div class="avatar">' . esc_html( $initial ) . '</div><div><strong>' . esc_html( $t['who'] ) . '</strong><span>' . esc_html( $t['role'] ) . '</span></div></div></div>';
			}
			?>
		</div>
	</div>
</section>

<!-- ============ PRICING ============ -->
<section class="section" id="pricing">
	<div class="container">
		<div class="section-head center reveal">
			<span class="eyebrow"><?php esc_html_e( 'Tarifs', 'aurora' ); ?></span>
			<h2><?php esc_html_e( 'Des formules simples, sans surprise', 'aurora' ); ?></h2>
		</div>

		<div class="pricing-grid">
			<div class="plan reveal">
				<h3><?php esc_html_e( 'Essentiel', 'aurora' ); ?></h3>
				<div class="price">CHF 990 <small>/ projet</small></div>
				<ul>
					<li><?php esc_html_e( 'Site vitrine 3 pages', 'aurora' ); ?></li>
					<li><?php esc_html_e( 'Thème sur mesure', 'aurora' ); ?></li>
					<li><?php esc_html_e( 'Responsive & accessible', 'aurora' ); ?></li>
					<li><?php esc_html_e( 'Livraison en 2 semaines', 'aurora' ); ?></li>
				</ul>
				<a class="btn btn-ghost" href="#contact"><?php esc_html_e( 'Choisir', 'aurora' ); ?></a>
			</div>

			<div class="plan featured reveal">
				<h3><?php esc_html_e( 'Business', 'aurora' ); ?></h3>
				<div class="price">CHF 2 900 <small>/ projet</small></div>
				<ul>
					<li><?php esc_html_e( 'Site complet jusqu’à 10 pages', 'aurora' ); ?></li>
					<li><?php esc_html_e( 'Design personnalisé avancé', 'aurora' ); ?></li>
					<li><?php esc_html_e( 'Optimisation SEO & perf', 'aurora' ); ?></li>
					<li><?php esc_html_e( 'Maintenance 3 mois offerte', 'aurora' ); ?></li>
				</ul>
				<a class="btn btn-primary" href="#contact"><?php esc_html_e( 'Choisir', 'aurora' ); ?> →</a>
			</div>

			<div class="plan reveal">
				<h3><?php esc_html_e( 'Premium', 'aurora' ); ?></h3>
				<div class="price">CHF 5 900 <small>/ projet</small></div>
				<ul>
					<li><?php esc_html_e( 'E-commerce ou app sur mesure', 'aurora' ); ?></li>
					<li><?php esc_html_e( 'Infrastructure cloud résiliente', 'aurora' ); ?></li>
					<li><?php esc_html_e( 'Formation & accompagnement', 'aurora' ); ?></li>
					<li><?php esc_html_e( 'Support prioritaire 24/7', 'aurora' ); ?></li>
				</ul>
				<a class="btn btn-ghost" href="#contact"><?php esc_html_e( 'Choisir', 'aurora' ); ?></a>
			</div>
		</div>
	</div>
</section>

<!-- ============ DERNIERS ARTICLES ============ -->
<?php
$latest = new WP_Query( array(
	'posts_per_page'      => 3,
	'post_status'         => 'publish',
	'ignore_sticky_posts' => true,
) );

if ( $latest->have_posts() ) :
	?>
	<section class="section" style="background:var(--bg-alt);">
		<div class="container">
			<div class="section-head center reveal">
				<span class="eyebrow"><?php esc_html_e( 'Le blog', 'aurora' ); ?></span>
				<h2><?php esc_html_e( 'Nos derniers articles', 'aurora' ); ?></h2>
			</div>
			<div class="posts-grid">
				<?php
				while ( $latest->have_posts() ) {
					$latest->the_post();
					?>
					<article class="post-card reveal">
						<a href="<?php the_permalink(); ?>">
							<div class="thumb"><span style="font-size:2.2rem;">✦</span></div>
							<div class="body">
								<div class="meta"><?php echo esc_html( get_the_date() ); ?></div>
								<h3><?php the_title(); ?></h3>
								<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18, '…' ) ); ?></p>
							</div>
						</a>
					</article>
					<?php
				}
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>
<?php endif; ?>

<!-- ============ CTA ============ -->
<section class="section">
	<div class="container">
		<div class="cta-band reveal">
			<h2><?php esc_html_e( 'Prêt à lancer votre projet ?', 'aurora' ); ?></h2>
			<p><?php esc_html_e( 'Parlons de vos objectifs autour d’un café (virtuel ou réel). Réponse garantie sous 24 h.', 'aurora' ); ?></p>
			<a class="btn btn-primary" href="mailto:hello@example.ch"><?php esc_html_e( 'Contactez-nous', 'aurora' ); ?> →</a>
		</div>
	</div>
</section>

<?php
get_footer();