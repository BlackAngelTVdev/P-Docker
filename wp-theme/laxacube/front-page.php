<?php
/**
 * Page d'accueil LaxaCube - reproduction fidèle du site original.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$assets = get_template_directory_uri() . '/assets/img/';

get_header();
?>

<main class="laxa-main">

	<!-- ============ HERO ============ -->
	<section class="intro" id="accueil">
		<div class="biglogo reveal">
			<img src="<?php echo esc_url( $assets . 'logo.png' ); ?>" alt="Logo du serveur LaxaCube">
		</div>
		<h1>LaxaCube PVP Faction</h1>
		<p>Bienvenue sur LaxaCube, le serveur faction PvP ultime pour les passionnés de jeux et les joueurs compétitifs ! Allie-toi avec tes amis pour dominer le champ de bataille, ou joue en solo pour prouver tes talents de guerrier ultime. Fais preuve de stratégie, conquiers les territoires ennemis et défends les tiens avec une détermination sans faille.</p>
		<div class="player-count"><span class="dot"></span> <span class="num-label">0 Joueurs en ligne actuellement</span></div>
		<br>
		<a href="#rejnnoindre" class="btn btn-primary-glow">JOUEZ MAINTENANT !</a>
	</section>

	<!-- ============ NOUVEAUTE / OPTIMISATION ============ -->
	<div class="double">
		<div class="block reveal">
			<div class="ico">
				<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
			</div>
			<div>
				<h2>Nouveauté</h2>
				<p>Partez à l'aventure chaque semaine avec nos quêtes passionnantes ! Chaque semaine, une nouvelle quête sera disponible, vous emmenant dans un voyage unique rempli de défis, d'énigmes et de récompenses.</p>
				<p>Mais ce n'est pas tout ! Nous avons également ajouté de nouvelles mécaniques pour enrichir l'expérience de jeu. L'une d'elles est l'ajout de spawners, qui apportent un tout nouveau niveau d'excitation à vos quêtes. Grâce aux spawners, vous ferez face à des ennemis inattendus et à des obstacles imprévus, rendant chaque quête encore plus palpitante.</p>
				<p>Préparez-vous à explorer de nouveaux territoires, à percer des mystères et à mettre vos compétences à l'épreuve dans notre monde de quêtes en constante évolution. Ne manquez pas nos nouvelles quêtes et mécaniques, soyez prêt à vous lancer !</p>
			</div>
		</div>

		<div class="block reveal">
			<div class="ico">
				<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
			</div>
			<div>
				<h2>Optimisation</h2>
				<p>Avec notre serveur stable et rapide, vous pouvez vous attendre à une expérience fluide et sans accroc, sans problèmes techniques ni erreurs. Notre serveur fiable garantit que vos données sont toujours stockées en toute sécurité et facilement accessibles, vous offrant ainsi une tranquillité d'esprit.</p>
				<p>Vous bénéficierez également de vitesses de chargement ultra-rapides, garantissant une expérience agréable et vous permettant de rester pleinement impliqué.</p>
			</div>
		</div>
	</div>

	<!-- ============ REJOINDRE ============ -->
	<h2 class="rejoindre" id="rejnnoindre">Rejoindre le jeu</h2>
	<p class="subrejoindre">Voici les étapes à suivre pour rejoindre le serveur :</p>

	<div class="launcher-note reveal">
		<strong>0. Launcher</strong>
		<span>Tu peux télécharger le launcher de LaxaCube gratuitement <a href="https://github.com/BlackAngelTVdev/LaxaCube-Launcher/releases/latest" target="_blank" rel="noopener">juste ici</a> (c'est pas obligatoire, juste tu as des modes utiles)</span>
	</div>

	<div class="steps-container">
		<div class="step reveal">
			<img src="<?php echo esc_url( $assets . 'step1.png' ); ?>" alt="Lancer Minecraft">
			<h3>1. Lance Minecraft Java</h3>
			<p>Actuellement, notre serveur fonctionne uniquement en 1.16.5, une version qui offre un bon équilibre entre stabilité et compatibilité des plugins.</p>
		</div>
		<div class="step reveal">
			<img src="<?php echo esc_url( $assets . 'step2.png' ); ?>" alt="Ajouter le serveur">
			<h3>2. Ajoute le serveur</h3>
			<p>Va dans Multijoueur, puis clique sur Ajouter un serveur. Copie-colle l'adresse IP dans le champ Adresse du serveur : <strong>laxacube.ch</strong></p>
		</div>
		<div class="step reveal">
			<img src="<?php echo esc_url( $assets . 'step3.png' ); ?>" alt="Rejoindre le serveur">
			<h3>3. Amuse-toi !</h3>
			<p>Clique sur Rejoindre et amuse-toi avec les autres joueurs !</p>
		</div>
	</div>

	<ul class="features reveal">
		<li><span class="f-ico">🏰🏗️</span> <span><strong>Des builds magnifiques</strong> – Des constructions épiques qui vont te laisser sans voix !</span></li>
		<li><span class="f-ico">⚔️🛡️</span> <span><strong>Des items de dingue</strong> – Des équipements et objets exclusifs pour pimenter ton aventure !</span></li>
		<li><span class="f-ico">🐉🔥</span> <span><strong>Des spawners ultra cool</strong> – Gère des quantités de mobes folles et farms comme un pro !</span></li>
	</ul>

	<!-- ============ EQUIPE ============ -->
	<h2 class="team-title" id="team">Notre Équipe</h2>
	<div class="team">
		<div class="team-member reveal">
			<img src="<?php echo esc_url( $assets . 'ba.png' ); ?>" alt="Membre BlackAngel_TV_">
			<h3>BlackAngel_TV_</h3>
			<div class="role">Propriétaire · Développeur</div>
		</div>
		<div class="team-member reveal">
			<img src="<?php echo esc_url( $assets . 'zr.png' ); ?>" alt="Membre Zarroc12">
			<h3>Zarroc12</h3>
			<div class="role">Propriétaire</div>
		</div>
		<div class="team-member reveal">
			<img src="<?php echo esc_url( $assets . 'fl.png' ); ?>" alt="Membre Flumpy">
			<h3>Flumpy</h3>
			<div class="role">Modérateur</div>
		</div>
	</div>

	<!-- ============ GALERIE DE BUILDS ============ -->
	<h2 class="team-title" id="galerie">Galerie de builds</h2>
	<div class="builds-grid">
		<div class="build-card g1 reveal"><span class="tag">Spawn</span><span class="emoji">🏰</span><h3>Le Hub</h3><p>Le spawn épique où tout commence : portails, échoppes et accueil des joueurs.</p></div>
		<div class="build-card g2 reveal"><span class="tag">PvP</span><span class="emoji">⚔️</span><h3>Arena PvP</h3><p>Affronte les meilleurs combattants du serveur dans une arène spectaculaire.</p></div>
		<div class="build-card g3 reveal"><span class="tag">Farm</span><span class="emoji">🐉</span><h3>Spawner World</h3><p>Des spawners ultra cool pour gérer des quantités de mobs et farms comme un pro.</p></div>
		<div class="build-card g4 reveal"><span class="tag">Faction</span><span class="emoji">🏗️</span><h3>Quartiers de factions</h3><p>Des territoires à conquérir et fortifier avec ta team, chaque semaine.</p></div>
		<div class="build-card g5 reveal"><span class="tag">Quêtes</span><span class="emoji">🗺️</span><h3>Monde des quêtes</h3><p>Chaque semaine, une nouvelle quête remplie de défis, d'énigmes et de récompenses.</p></div>
		<div class="build-card g6 reveal"><span class="tag">Events</span><span class="emoji">🌌</span><h3>Events communautaires</h3><p>Des événements réguliers organisés par l'équipe pour toute la communauté.</p></div>
	</div>
	<div class="builds-cta"><a href="<?php echo esc_url( home_url( '/images/' ) ); ?>">Voir la galerie complète →</a></div>

	<!-- ============ CTA ============ -->
	<div class="cta-final reveal">
		<h2>Rejoins la communauté !</h2>
		<p>Viens discuter avec les autres joueurs sur Discord et découvre notre boutique pour soutenir le serveur.</p>
		<div class="btns">
			<a class="btn" href="https://bit.ly/discord-laxacube" target="_blank" rel="noopener">Rejoindre le Discord</a>
			<a class="btn" href="https://laxacube.craftingstore.net/" target="_blank" rel="noopener" style="background:#2b2929;box-shadow:0 6px 20px rgba(0,0,0,.3);">Visiter la Boutique</a>
		</div>
	</div>

</main>

<?php
get_footer();