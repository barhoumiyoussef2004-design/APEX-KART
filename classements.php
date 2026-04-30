<?php
// =============================================
// CLASSEMENTS.PHP - Classement dynamique (performances DB)
// =============================================

require_once 'config.php';

// Récupérer le meilleur temps de chaque pilote (JOIN pilotes + performances)
try {
    $sql = "SELECT p.nom_complet,
                   MIN(pr.temps_tour) AS meilleur_temps,
                   pr.kart_utilise,
                   pr.date_performance,
                   COUNT(pr.id) AS nb_sessions
            FROM performances pr
            JOIN pilotes p ON pr.id_pilote = p.id
            GROUP BY pr.id_pilote
            ORDER BY meilleur_temps ASC";
    
    $stmt = $pdo->query($sql);
    $classement = $stmt->fetchAll();
    
    // Trouver le record absolu (1er du classement)
    $recordTemps = '';
    $recordPilote = '';
    $recordDate = '';
    $recordKart = '';
    if (count($classement) > 0) {
        $recordTemps = $classement[0]['meilleur_temps'];
        $recordPilote = $classement[0]['nom_complet'];
        $recordDate = $classement[0]['date_performance'];
        $recordKart = $classement[0]['kart_utilise'];
    }
} catch (PDOException $e) {
    $classement = array();
    $recordTemps = 'N/A';
}

// Fonction pour formater le temps (afficher depuis TIME(3))
function afficherTemps($temps) {
    // Le temps vient de MySQL TIME(3) au format HH:MM:SS.mmm
    // On affiche MM:SS.mmm
    return $temps;
}

// Fonction pour déterminer la catégorie selon le nombre de sessions
function getCategorie($nbSessions) {
    if ($nbSessions >= 40) return 'Expert';
    if ($nbSessions >= 10) return 'Senior';
    if ($nbSessions >= 5) return 'Junior';
    return 'Débutant';
}

// Fonction pour la couleur du badge catégorie
function getCategorieClass($categorie) {
    if ($categorie === 'Expert') return 'badge-expert';
    if ($categorie === 'Senior') return 'badge-senior';
    if ($categorie === 'Junior') return 'badge-junior';
    return 'badge-debutant';
}

// Fonction pour calculer les points (simple : 1000 - temps en centièmes)
function calculerPoints($tempsStr, $nbSessions) {
    // Convertir temps en centièmes pour le calcul
    // Format: HH:MM:SS.mmm → total millisecondes
    $parts = explode(':', $tempsStr);
    if (count($parts) === 3) {
        $heures = intval($parts[0]);
        $minutes = intval($parts[1]);
        $secondes = floatval($parts[2]);
        $totalMs = ($heures * 3600 + $minutes * 60 + $secondes) * 1000;
        // Points = (75000 - totalMs) / 100 + (nbSessions * 5)
        $points = floor((75000 - $totalMs) / 100 + ($nbSessions * 5));
        if ($points < 0) $points = 0;
        return $points;
    }
    return 0;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Classements Pilotes — APEX KART Karting</title>
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
      <a href="apropos.php">À Propos</a>
      <a href="piste.html">La Piste</a>
      <a href="services.html">Services</a>
      <a href="classements.php" class="actif">Classements</a>
      <a href="galerie.html">Galerie</a>
      <a href="contact.php">Contact</a>
      <a href="reserver.php" class="nav-cta">Réserver ▶</a>
    </nav>
  </header>

  <main>
    <div id="top"></div>

    <div class="page-header">
      <div class="page-header-contenu">
        <span class="label-section" style="margin-bottom: 14px;">Saison 2026</span>
        <h1>CLASSEMENT<br><span>GÉNÉRAL</span></h1>
        <p>Tableau mis à jour après chaque session. Le record du circuit est indiqué par ⚡.</p>
      </div>
    </div>

    <!--BANDE RECORD (données depuis la base)-->
    <?php if ($recordTemps !== '') { ?>
    <div style="background: var(--noir-3); border-bottom: 1px solid var(--bord); padding: 20px 4%; display: flex; gap: 40px; flex-wrap: wrap; align-items: center;">
      <div>
        <span style="display: block; font-size: 0.65rem; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: var(--gris); margin-bottom: 4px;">⚡ Record de la saison</span>
        <span style="font-family: Impact, sans-serif; font-size: 2.2rem; font-weight: 900; color: var(--jaune);"><?php echo afficherTemps($recordTemps); ?></span>
      </div>
      <div>
        <span style="display: block; font-size: 0.65rem; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: var(--gris); margin-bottom: 4px;">Pilote</span>
        <span style="font-family: Impact, sans-serif; font-size: 1.5rem; font-weight: 700; text-transform: uppercase; color: var(--blanc);"><?php echo htmlspecialchars($recordPilote); ?></span>
      </div>
      <div>
        <span style="display: block; font-size: 0.65rem; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: var(--gris); margin-bottom: 4px;">Date</span>
        <span style="font-size: 0.95rem; color: var(--gris-clair); font-family: monospace;"><?php echo $recordDate; ?></span>
      </div>
      <div>
        <span style="display: block; font-size: 0.65rem; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: var(--gris); margin-bottom: 4px;">Kart</span>
        <span style="font-size: 0.95rem; color: var(--gris-clair); font-family: monospace;"><?php echo htmlspecialchars($recordKart); ?></span>
      </div>
    </div>
    <?php } ?>

    <!--PODIUM VISUEL (Top 3 depuis la base)-->
    <?php if (count($classement) >= 3) { ?>
    <section class="section-fond-2">
      <div class="en-tete-section centre">
        <span class="label-section">Top 3 de la saison</span>
        <h2 class="titre-section">Le <span class="rouge">Podium</span></h2>
      </div>

      <div class="podium-conteneur">

        <?php
        // 2e place
        $pilote2 = $classement[1];
        $pts2 = calculerPoints($pilote2['meilleur_temps'], $pilote2['nb_sessions']);
        $init2 = strtoupper(substr($pilote2['nom_complet'], 0, 1));
        ?>
        <div class="podium-pilote podium-2e">
          <div class="podium-avatar"><?php echo $init2; ?></div>
          <div class="podium-nom"><?php echo htmlspecialchars($pilote2['nom_complet']); ?></div>
          <div class="podium-chrono"><?php echo afficherTemps($pilote2['meilleur_temps']); ?></div>
          <div class="podium-pts"><?php echo $pts2; ?> pts</div>
          <div class="podium-marche podium-marche-2">
            <span class="podium-medaille">🥈</span>
            <span class="podium-rang-num">2</span>
          </div>
        </div>

        <?php
        // 1re place
        $pilote1 = $classement[0];
        $pts1 = calculerPoints($pilote1['meilleur_temps'], $pilote1['nb_sessions']);
        $init1 = strtoupper(substr($pilote1['nom_complet'], 0, 1));
        ?>
        <div class="podium-pilote podium-1er">
          <div class="podium-couronne">👑</div>
          <div class="podium-avatar podium-avatar-or"><?php echo $init1; ?></div>
          <div class="podium-nom podium-nom-or"><?php echo htmlspecialchars($pilote1['nom_complet']); ?></div>
          <div class="podium-chrono podium-chrono-or">⚡ <?php echo afficherTemps($pilote1['meilleur_temps']); ?></div>
          <div class="podium-pts"><?php echo $pts1; ?> pts</div>
          <div class="podium-marche podium-marche-1">
            <span class="podium-medaille">🥇</span>
            <span class="podium-rang-num">1</span>
          </div>
        </div>

        <?php
        // 3e place
        $pilote3 = $classement[2];
        $pts3 = calculerPoints($pilote3['meilleur_temps'], $pilote3['nb_sessions']);
        $init3 = strtoupper(substr($pilote3['nom_complet'], 0, 1));
        ?>
        <div class="podium-pilote podium-3e">
          <div class="podium-avatar podium-avatar-bro"><?php echo $init3; ?></div>
          <div class="podium-nom"><?php echo htmlspecialchars($pilote3['nom_complet']); ?></div>
          <div class="podium-chrono"><?php echo afficherTemps($pilote3['meilleur_temps']); ?></div>
          <div class="podium-pts"><?php echo $pts3; ?> pts</div>
          <div class="podium-marche podium-marche-3">
            <span class="podium-medaille">🥉</span>
            <span class="podium-rang-num">3</span>
          </div>
        </div>

      </div>
    </section>
    <?php } ?>

    <!--CLASSEMENT COMPLET (données depuis la base)-->
    <section>
      <div class="en-tete-section">
        <span class="label-section">Résultats complets</span>
        <h2 class="titre-section">Classement <span class="rouge">général</span></h2>
        <p class="sous-titre-section"><?php echo count($classement); ?> pilotes enregistrés cette saison. Classement par meilleur temps.</p>
      </div>

      <!-- Légende catégories -->
      <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 24px; padding: 16px; background: var(--noir-2); border: 1px solid var(--bord); border-radius: 2px;">
        <span style="font-size: 0.75rem; color: var(--gris); font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-right: 8px;">Catégories :</span>
        <span class="badge-cat badge-debutant">Débutant</span>
        <span style="font-size: 0.8rem; color: var(--gris);">Moins de 10 sessions</span>
        <span style="color: var(--bord); margin: 0 4px;">|</span>
        <span class="badge-cat badge-junior">Junior</span>
        <span style="font-size: 0.8rem; color: var(--gris);">8–16 ans</span>
        <span style="color: var(--bord); margin: 0 4px;">|</span>
        <span class="badge-cat badge-senior">Senior</span>
        <span style="font-size: 0.8rem; color: var(--gris);">16+ ans, 10–40 sessions</span>
        <span style="color: var(--bord); margin: 0 4px;">|</span>
        <span class="badge-cat badge-expert">Expert</span>
        <span style="font-size: 0.8rem; color: var(--gris);">40+ sessions</span>
      </div>

      <?php if (count($classement) > 0) { ?>
      <div style="overflow-x: auto;">
        <table>
          <thead>
            <tr>
              <th style="width: 50px;">#</th>
              <th>Pilote</th>
              <th>Catégorie</th>
              <th>Meilleur temps</th>
              <th>Points</th>
              <th>Sessions</th>
              <th>Kart préféré</th>
            </tr>
          </thead>
          <tbody>

            <?php
            // Parcourir le classement avec fetch() dans un loop
            for ($i = 0; $i < count($classement); $i++) {
                $pilote = $classement[$i];
                $rang = $i + 1;
                $categorie = getCategorie($pilote['nb_sessions']);
                $catClass = getCategorieClass($categorie);
                $points = calculerPoints($pilote['meilleur_temps'], $pilote['nb_sessions']);
                
                // Classement de la cellule rang
                $rangClass = 'rang';
                if ($rang === 1) $rangClass .= ' or';
                else if ($rang === 2) $rangClass .= ' arg';
                else if ($rang === 3) $rangClass .= ' bro';

                // Style chrono (record pour le 1er)
                $chronoClass = 'chrono';
                if ($rang === 1) $chronoClass .= ' record';
            ?>
            <tr>
              <td><span class="<?php echo $rangClass; ?>"><?php echo $rang; ?></span></td>
              <td><div class="pilote-nom"><?php echo htmlspecialchars($pilote['nom_complet']); ?></div></td>
              <td><span class="badge-cat <?php echo $catClass; ?>"><?php echo $categorie; ?></span></td>
              <td><span class="<?php echo $chronoClass; ?>"><?php if ($rang === 1) echo '⚡ '; ?><?php echo afficherTemps($pilote['meilleur_temps']); ?></span></td>
              <td style="font-family: Impact, sans-serif; font-size: 1.1rem; font-weight: 700; color: var(--blanc);"><?php echo $points; ?></td>
              <td style="color: var(--gris); font-family: monospace;"><?php echo $pilote['nb_sessions']; ?></td>
              <td style="color: var(--gris);"><?php echo htmlspecialchars($pilote['kart_utilise']); ?></td>
            </tr>
            <?php } ?>

          </tbody>
        </table>
      </div>
      <?php } else { ?>
      <p style="text-align: center; color: var(--gris); padding: 3rem;">Aucune performance enregistrée pour le moment.</p>
      <?php } ?>

      <p style="margin-top: 14px; font-size: 0.75rem; color: var(--gris); font-family: monospace;">
        ⚡ = Record de la saison | Points calculés selon les temps et le nombre de sessions | Mis à jour chaque lundi
      </p>
    </section>

    <!--REJOINDRE LE CLASSEMENT-->
    <section class="section-fond-2">
      <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 24px;">
        <div>
          <span class="label-section">Votre tour</span>
          <h2 class="titre-section" style="margin-top: 10px;">Entrez dans<br>le <span class="rouge">classement</span></h2>
          <p style="color: var(--gris-clair); margin-top: 14px; max-width: 500px; line-height: 1.8; font-size: 0.95rem;">
            Réservez une session Race Day pour intégrer le classement officiel de la saison 2026.
            Votre meilleur chrono sera enregistré et vous recevrez votre classement par email.
          </p>
        </div>
        <div style="text-align: center;">
          <a href="reserver.php" class="btn btn-rouge" style="display: inline-block; margin-bottom: 12px; font-size: 1.1rem;">Réserver un Race Day →</a>
          <p style="font-size: 0.8rem; color: var(--gris);">45DT / session · Classement officiel inclus</p>
        </div>
      </div>
    </section>

    <div class="bande-cta">
      <h2>BATTEZ LE RECORD : <?php echo $recordTemps !== '' ? $recordTemps : '---'; ?></h2>
      <p>Pensez-vous pouvoir faire mieux que <?php echo $recordPilote !== '' ? htmlspecialchars($recordPilote) : 'personne'; ?> ? La piste vous attend.</p>
      <a href="reserver.php" class="btn-blanc">RELEVER LE DÉFI ▶</a>
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
          <li><a href="reserver.php" style="color: var(--rouge);">→ Réservation en ligne</a></li>
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
