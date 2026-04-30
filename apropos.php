<?php
// =============================================
// APROPOS.PHP - À Propos (instructeurs depuis DB)
// =============================================

require_once 'config.php';

// Charger les instructeurs depuis la base de données
try {
    $stmt = $pdo->query("SELECT nom, specialite, bio FROM instructeurs ORDER BY date_embauche DESC");
    $instructeurs = $stmt->fetchAll();
} catch (PDOException $e) {
    $instructeurs = array();
}

// Charger les karts depuis la base de données
try {
    // Récupérer tous les karts sans GROUP BY pour éviter les erreurs SQL
    $stmt = $pdo->query("SELECT * FROM karts ORDER BY numero_flotte ASC");
    $karts_raw = $stmt->fetchAll();
    
    // Grouper manuellement en PHP
    $karts = array();
    for ($i = 0; $i < count($karts_raw); $i++) {
        $cle = $karts_raw[$i]['modele'] . '|' . $karts_raw[$i]['type'];
        if (isset($karts[$cle])) {
            $karts[$cle]['nb'] = $karts[$cle]['nb'] + 1;
        } else {
            $karts[$cle] = array(
                'modele' => $karts_raw[$i]['modele'],
                'type' => $karts_raw[$i]['type'],
                'puissance' => $karts_raw[$i]['puissance'],
                'nb' => 1
            );
        }
    }
    $karts = array_values($karts);
    
    // DEBUG: Affiche le nombre de karts trouvés
    // echo "<p style='color:red;'>Karts trouvés en base : " . count($karts_raw) . "</p>";
    
} catch (PDOException $e) {
    // Affiche l'erreur directement sur la page pour débogage
    echo "<div style='color: red; background: #300; padding: 20px; margin: 20px;'>Erreur Base de Données: " . $e->getMessage() . "</div>";
    $karts = array();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>À Propos — Apex Kart</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <header>
    <a href="index.html" class="logo">
      <span class="logo-point"></span>
      APEX <span>KART</span>
    </a>
    <nav>
      <a href="index.html">Accueil</a>
      <a href="apropos.php" class="actif">À Propos</a>
      <a href="piste.html">La Piste</a>
      <a href="services.html">Services</a>
      <a href="classements.php">Classements</a>
      <a href="galerie.html">Galerie</a>
      <a href="contact.php">Contact</a>
      <a href="reserver.php" class="nav-cta">Réserver ▶</a>
    </nav>
  </header>

  <main>
    <div id="top"></div>

    <div class="page-header">
      <div class="page-header-contenu">
        <span class="label-section" style="margin-bottom: 14px;">Notre histoire</span>
        <h1>À PROPOS DE<br><span>APEX KART</span></h1>
        <p>La destination ultime pour les passionnés de karting et les amateurs d'adrénaline</p>
      </div>
    </div>

    <!--HISTOIRE-->
    <section class="section-fond-2">
      <div class="grille-2">
        <div>
          <span class="label-section">Notre histoire</span>
          <h2 class="titre-section">L'HISTOIRE DE<br><span class="rouge">APEX KART</span></h2>
          <p style="color: var(--gris-clair); margin-bottom: 1rem; line-height: 1.8;">
            Fondé en 2015, Apex Kart est né d'une passion pour le sport automobile
            et d'une vision : créer l'expérience de karting ultime. Ce qui a commencé comme
            le rêve de deux passionnés de course est devenu l'une des installations de karting
            les plus avancées de la région.
          </p>
          <p style="color: var(--gris-clair); margin-bottom: 1rem; line-height: 1.8;">
            Nos fondateurs, d'anciens pilotes professionnels, ont conçu chaque aspect de la
            piste pour défier les pilotes de tous niveaux tout en maintenant les normes de
            sécurité les plus élevées.
          </p>
          <p style="color: var(--gris-clair); line-height: 1.8;">
            Aujourd'hui, Apex Kart continue de repousser les limites avec des technologies
            de pointe, des mises à niveau régulières de la piste et un engagement à offrir
            une expérience de course inoubliable à tous nos visiteurs.
          </p>
        </div>
        <div>
          <img src="media/circuit vue aerienne.jpg"
               alt="Circuit Apex Kart"
               style="border-radius: 12px; border: 2px solid rgba(0,255,136,0.2); width: 100%;">
        </div>
      </div>
    </section>

    <!--VALEURS-->
    <section>
      <div class="en-tete-section centre">
        <span class="label-section">Notre mission</span>
        <h2 class="titre-section">NOS <span class="rouge">VALEURS</span></h2>
      </div>

      <div class="grille-3">

        <div style="text-align: center; padding: 2rem;">
          <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--vert-neon), var(--cyan)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem;">
            😊
          </div>
          <h3 style="color: var(--vert-neon); margin-bottom: 1rem;">Passion pour la Course</h3>
          <p style="color: var(--gris-clair); line-height: 1.7;">
            Nous nous consacrons à partager le frisson et l'excitation du sport automobile
            avec tous ceux qui franchissent nos portes.
          </p>
        </div>

        <div style="text-align: center; padding: 2rem;">
          <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--vert-neon), var(--cyan)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem;">
            👥
          </div>
          <h3 style="color: var(--vert-neon); margin-bottom: 1rem;">Construction Communautaire</h3>
          <p style="color: var(--gris-clair); line-height: 1.7;">
            Créer un environnement accueillant où les passionnés de course peuvent se
            connecter, rivaliser et grandir ensemble.
          </p>
        </div>

        <div style="text-align: center; padding: 2rem;">
          <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--vert-neon), var(--cyan)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem;">
            ✅
          </div>
          <h3 style="color: var(--vert-neon); margin-bottom: 1rem;">Excellence</h3>
          <p style="color: var(--gris-clair); line-height: 1.7;">
            Maintenir les normes les plus élevées en matière d'équipement, de sécurité
            et de service client en tout temps.
          </p>
        </div>

      </div>
    </section>

    <!--ÉQUIPEMENT (Karts depuis la base de données)-->
    <section class="section-fond-2">
      <div class="en-tete-section centre">
        <span class="label-section">Notre flotte</span>
        <h2 class="titre-section">NOS <span class="rouge">KARTS</span></h2>
        <p class="sous-titre-section">Une flotte moderne et entretenue pour tous les niveaux.</p>
      </div>

      <?php if (count($karts) > 0) { ?>
      <div class="grille-3">
        <?php
        // Parcourir les karts groupés
        for ($i = 0; $i < count($karts); $i++) {
            $count = $karts[$i]['nb'];
        ?>
        <div class="carte" style="text-align: center; padding: 2.5rem;">
          
          <h3 style="color: var(--vert-neon); margin-bottom: 1rem; font-size: 1.5rem; text-transform: uppercase;">
            <?php echo htmlspecialchars($karts[$i]['modele']); ?>
          </h3>
          
          <div style="background: rgba(255,255,255,0.05); padding: 0.8rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.3rem;">
              <span style="color: var(--gris);">Type:</span>
              <span style="color: var(--blanc); font-weight: 700;"><?php echo $karts[$i]['type']; ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
              <span style="color: var(--gris);">Puissance:</span>
              <span style="color: var(--blanc); font-weight: 700;"><?php echo $karts[$i]['puissance']; ?></span>
            </div>
          </div>

          <p style="color: var(--gris); font-size: 0.9rem; margin: 0;">
            <?php echo $count; ?> kart<?php echo ($count > 1 ? 's' : ''); ?> encore disponible<?php echo ($count > 1 ? 's' : ''); ?>
          </p>
        </div>
        <?php } ?>
      </div>
      <?php } else { ?>
        <p style="text-align: center; color: var(--gris);">Aucun kart enregistré pour le moment.</p>
      <?php } ?>
    </section>

    <!--ÉQUIPE (instructeurs depuis la base de données)-->
    <?php if (count($instructeurs) > 0) { ?>
    <section class="section-fond-2">
      <div class="en-tete-section centre">
        <span class="label-section">Notre équipe</span>
        <h2 class="titre-section">NOS <span class="rouge">INSTRUCTEURS</span></h2>
        <p class="sous-titre-section">Des professionnels passionnés pour vous accompagner sur la piste.</p>
      </div>

      <div class="grille-3">
        <?php
        // Parcourir les instructeurs depuis la base de données
        for ($i = 0; $i < count($instructeurs); $i++) {
            $init = strtoupper(substr($instructeurs[$i]['nom'], 0, 1));
        ?>
        <div class="carte" style="text-align: center;">
          <div style="width: 80px; height: 80px; background: var(--rouge); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 2rem; font-family: Impact, sans-serif; color: var(--blanc);">
            <?php echo $init; ?>
          </div>
          <h3 style="color: var(--vert-neon);"><?php echo htmlspecialchars($instructeurs[$i]['nom']); ?></h3>
          <p style="color: var(--jaune); font-size: 0.85rem; font-weight: 700; margin-bottom: 1rem;"><?php echo htmlspecialchars($instructeurs[$i]['specialite']); ?></p>
          <p style="color: var(--gris-clair); font-size: 0.9rem; line-height: 1.7;"><?php echo htmlspecialchars($instructeurs[$i]['bio']); ?></p>
        </div>
        <?php } ?>
      </div>
    </section>
    <?php } ?>

    <!--CHIFFRES DU CIRCUIT-->
    <section class="section-fond-2">
      <div class="en-tete-section centre">
        <span class="label-section">Informations sur la piste</span>
        <h2 class="titre-section">CARACTÉRISTIQUES DU <span class="rouge">CIRCUIT</span></h2>
      </div>

      <div style="display: flex; flex-wrap: wrap; gap: 2px; background: var(--bord);">

        <div class="stat-cell" style="flex: 1; min-width: 180px; background: var(--noir-2); padding: 36px; text-align: center;">
          <span class="stat-number" data-count="1580" style="display: block; font-family: Impact, sans-serif; font-size: 3.5rem; font-weight: 900; color: var(--vert-neon); line-height: 1;">1580</span>
          <span style="display: block; font-size: 0.7rem; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: var(--gris); margin-top: 6px;">Mètres de piste</span>
        </div>

        <div class="stat-cell" style="flex: 1; min-width: 180px; background: var(--noir-2); padding: 36px; text-align: center;">
          <span class="stat-number" data-count="17" style="display: block; font-family: Impact, sans-serif; font-size: 3.5rem; font-weight: 900; color: var(--vert-neon); line-height: 1;">17</span>
          <span style="display: block; font-size: 0.7rem; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: var(--gris); margin-top: 6px;">Virages</span>
        </div>

        <div class="stat-cell" style="flex: 1; min-width: 180px; background: var(--noir-2); padding: 36px; text-align: center;">
          <span class="stat-number" data-count="25" style="display: block; font-family: Impact, sans-serif; font-size: 3.5rem; font-weight: 900; color: var(--vert-neon); line-height: 1;">25</span>
          <span style="display: block; font-size: 0.7rem; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: var(--gris); margin-top: 6px;">Karts professionnels</span>
        </div>

        <div class="stat-cell" style="flex: 1; min-width: 180px; background: var(--noir-2); padding: 36px; text-align: center;">
          <span class="stat-number" data-count="500" data-suffix="+" style="display: block; font-family: Impact, sans-serif; font-size: 3.5rem; font-weight: 900; color: var(--vert-neon); line-height: 1;">500+</span>
          <span style="display: block; font-size: 0.7rem; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: var(--gris); margin-top: 6px;">Courses complétées</span>
        </div>

        <div class="stat-cell" style="flex: 1; min-width: 180px; background: var(--noir-2); padding: 36px; text-align: center;">
          <span class="stat-number" data-count="100" data-suffix="%" style="display: block; font-family: Impact, sans-serif; font-size: 3.5rem; font-weight: 900; color: var(--jaune); line-height: 1;">100%</span>
          <span style="display: block; font-size: 0.7rem; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: var(--gris); margin-top: 6px;">Bilan de sécurité</span>
        </div>

      </div>
    </section>

    <div class="bande-cta">
      <h2>REJOIGNEZ-NOUS SUR LA PISTE</h2>
      <p>Vivez l'expérience Apex Kart. Réservez votre session dès aujourd'hui !</p>
      <a href="reserver.php" class="btn-blanc">RÉSERVER MAINTENANT ▶</a>
    </div>
    <a href="#top" class="scroll-top">↑</a>

  </main>

  <footer>
    <div class="footer-grille">
      <div class="footer-col large">
        <span class="footer-logo">APEX <span>KART</span></span>
        <p class="footer-desc">Circuit de karting professionnel. 1580 mètres d'asphalte et des sensations garanties.</p>
      </div>
      <div class="footer-col">
        <span class="footer-col-titre">Navigation</span>
        <ul class="footer-liens">
          <li><a href="index.html">Accueil</a></li>
          <li><a href="apropos.php">À Propos</a></li>
          <li><a href="piste.html">La Piste</a></li>
          <li><a href="services.html">Services</a></li>
          <li><a href="classements.php">Classements</a></li>
          <li><a href="galerie.html">Galerie</a></li>
          <li><a href="contact.php">Contact</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <span class="footer-col-titre">Contact</span>
        <ul class="footer-liens">
          <li><a href="tel:+21671440300">📞 +216 71 440 300</a></li>
          <li><a href="mailto:contact@apexkart.tn">✉️ contact@apexkart.tn</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <span class="footer-col-titre">Réserver</span>
        <ul class="footer-liens">
          <li><a href="reserver.php" style="color: var(--vert-neon);">→ Réservation en ligne</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bas">
      <span>© 2026 APEX KART. Tous droits réservés.</span>
      <span>apexkart.tn</span>
    </div>
  </footer>

  <script src="script.js"></script>
</body>
</html>
