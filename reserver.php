<?php
// =============================================
// RESERVER.PHP - Réservation (services dynamiques)
// =============================================

require_once 'config.php';

// Charger les sessions depuis la base de données
try {
    $stmt = $pdo->query("SELECT id, nom_service, prix FROM services WHERE categorie = 'session'");
    $sessions = $stmt->fetchAll();
} catch (PDOException $e) {
    $sessions = array();
}

// Charger les options depuis la base de données
try {
    $stmt = $pdo->query("SELECT id, nom_service, prix FROM services WHERE categorie = 'option'");
    $options = $stmt->fetchAll();
} catch (PDOException $e) {
    $options = array();
}

// Charger les instructeurs disponibles
try {
    $stmt = $pdo->query("SELECT id, nom, specialite FROM instructeurs");
    $instructeurs = $stmt->fetchAll();
} catch (PDOException $e) {
    $instructeurs = array();
}

// Charger les types de karts disponibles (distincts)
try {
    $stmt = $pdo->query("SELECT DISTINCT modele, type, puissance FROM karts WHERE statut = 'Disponible' ORDER BY modele ASC");
    $karts = $stmt->fetchAll();
} catch (PDOException $e) {
    // En cas d'erreur, afficher pour débogage
    echo "<p style='color:red;'>Erreur karts: " . $e->getMessage() . "</p>";
    $karts = array();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Réserver — Apex Kart</title>
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
      <a href="classements.php">Classements</a>
      <a href="galerie.html">Galerie</a>
      <a href="contact.php">Contact</a>
      <a href="reserver.php" class="nav-cta actif">Réserver ▶</a>
    </nav>
  </header>

  <main>
    <div id="top"></div>

    <div class="page-header">
      <div class="page-header-contenu">
        <span class="label-section" style="margin-bottom: 14px;">Réservation</span>
        <h1>RÉSERVEZ VOTRE<br><span>SESSION DE COURSE</span></h1>
        <p>Remplissez le formulaire ci-dessous pour réserver votre place sur la piste</p>
      </div>
    </div>

    <section class="section-fond-2">
      <div class="grille-2">

        <!-- Formulaire de réservation -->
        <div>
          <span class="label-section">Formulaire de réservation</span>
          <h2 class="titre-section" style="margin-top: 1rem; margin-bottom: 2rem;">RÉSERVEZ <span class="rouge">MAINTENANT</span></h2>

          <form class="formulaire-bloc" method="post" action="traiter_reservation.php">

            <div class="form-groupe">
              <label for="nom">Nom complet *</label>
              <input type="text" id="nom" name="nom" placeholder="Jean Dupont" required>
            </div>

            <div class="form-groupe">
              <label for="email">Adresse email *</label>
              <input type="email" id="email" name="email" placeholder="jean@exemple.tn" required>
            </div>

            <div class="form-groupe">
              <label for="telephone">Numéro de téléphone *</label>
              <input type="tel" id="telephone" name="telephone" placeholder="+216 76 123 456" required>
            </div>

            <!-- Type de session — chargé depuis la base de données -->
            <div class="form-groupe">
              <label>Type de session *</label>
              <div style="display: flex; flex-direction: column; gap: 0.8rem; margin-top: 0.5rem;">
                <?php
                // Parcourir les sessions avec un loop
                for ($i = 0; $i < count($sessions); $i++) {
                    $checked = ($i === 1) ? ' checked' : ''; // Session adultes par défaut
                    // Extraire le slug (enfants, adultes, groupe) depuis le nom
                    $nom = strtolower($sessions[$i]['nom_service']);
                    $slug = '';
                    if (strpos($nom, 'enfant') !== false) $slug = 'enfants';
                    else if (strpos($nom, 'adulte') !== false) $slug = 'adultes';
                    else if (strpos($nom, 'groupe') !== false) $slug = 'groupe';
                    else $slug = $slug;

                    echo '<label class="choix-item">';
                    echo '<input type="radio" name="session" value="' . $slug . '" required' . $checked . '>';
                    echo '<div>';
                    echo '<div style="font-weight: 700; color: var(--blanc);">' . htmlspecialchars($sessions[$i]['nom_service']) . '</div>';
                    echo '<div style="font-size: 0.85rem; color: var(--gris);">' . $sessions[$i]['prix'] . ' DT</div>';
                    echo '</div>';
                    echo '</label>';
                }
                ?>
              </div>
            </div>

            <div class="form-ligne">
              <div class="form-groupe">
                <label for="date">Date préférée *</label>
                <input type="date" id="date" name="date" required>
              </div>
              <div class="form-groupe">
                <label for="creneau">Créneau horaire *</label>
                <select id="creneau" name="creneau" required>
                  <option value="">— Choisissez un créneau —</option>
                  <option value="10:00">10:00</option>
                  <option value="11:00">11:00</option>
                  <option value="12:00">12:00</option>
                  <option value="13:00">13:00</option>
                  <option value="14:00">14:00</option>
                  <option value="15:00">15:00</option>
                  <option value="16:00">16:00</option>
                  <option value="17:00">17:00</option>
                  <option value="18:00">18:00</option>
                  <option value="19:00">19:00</option>
                  <option value="20:00">20:00</option>
                </select>
              </div>
            </div>

            <div class="form-groupe">
              <label for="participants">Nombre de participants *</label>
              <input type="number" id="participants" name="participants" min="1" max="20" value="1" required>
            </div>

            <!-- Choix des Karts (par type avec quantité) -->
            <div class="form-groupe" id="kart-selection-group">
              <label>Type de karts <span style="color: var(--gris); font-weight: 400;">(Total max: <span id="max-karts">1</span>)</span></label>
              
              <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem;">
                <?php
                // Afficher chaque modèle de kart avec un compteur
                for ($i = 0; $i < count($karts); $i++) {
                ?>
                <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(255,255,255,0.03); padding: 0.5rem 1rem; border-radius: 6px;">
                  <div>
                    <div style="font-weight: 700; color: var(--blanc);">
                      <?php echo htmlspecialchars($karts[$i]['modele']); ?>
                    </div>
                    <div style="font-size: 0.8rem; color: var(--gris);">
                      <?php echo htmlspecialchars($karts[$i]['type']); ?> — <?php echo htmlspecialchars($karts[$i]['puissance']); ?>
                    </div>
                  </div>
                  
                  <input type="number" 
                         name="karts[<?php echo htmlspecialchars($karts[$i]['modele']); ?>]" 
                         value="0" min="0" max="20" 
                         class="kart-qty-input"
                         style="width: 60px; text-align: center; padding: 0.5rem; background: var(--noir-3); border: 1px solid var(--bord); color: var(--blanc); border-radius: 4px;">
                </div>
                <?php } ?>
              </div>
              
              <p id="kart-warning" style="color: #ff3366; font-size: 0.85rem; margin-top: 0.5rem; display: none;">
                ⚠ Le nombre total de karts choisis ne peut pas dépasser le nombre de participants.
              </p>
            </div>

            <!-- Script pour limiter le nombre total de karts -->
            <script>
            var participantsInput = document.getElementById('participants');
            var qtyInputs = document.querySelectorAll('.kart-qty-input');
            var maxKartsLabel = document.getElementById('max-karts');
            var warningMsg = document.getElementById('kart-warning');

            function checkKartLimits() {
                var max = parseInt(participantsInput.value);
                maxKartsLabel.textContent = max;
                
                var totalSelected = 0;
                for (var i = 0; i < qtyInputs.length; i++) {
                    totalSelected = totalSelected + parseInt(qtyInputs[i].value);
                }

                if (totalSelected > max) {
                    warningMsg.style.display = 'block';
                } else {
                    warningMsg.style.display = 'none';
                }
            }

            // Écouter les changements sur les inputs de karts
            for (var i = 0; i < qtyInputs.length; i++) {
                qtyInputs[i].oninput = function() {
                    checkKartLimits();
                };
            }

            // Écouter les changements sur le nombre de participants
            participantsInput.onchange = checkKartLimits;
            
            // Initialiser
            checkKartLimits();
            </script>

            <!-- Choix de l'Instructeur (affiché dynamiquement depuis la base) -->
            <div class="form-groupe">
              <label for="instructeur">Instructeur préféré (optionnel)</label>
              <select id="instructeur" name="instructeur">
                <option value="">— Aucun / Pas de coaching —</option>
                <?php
                // Parcourir les instructeurs
                for ($i = 0; $i < count($instructeurs); $i++) {
                    echo '<option value="' . $instructeurs[$i]['id'] . '">';
                    echo htmlspecialchars($instructeurs[$i]['nom']) . ' — ' . htmlspecialchars($instructeurs[$i]['specialite']);
                    echo '</option>';
                }
                ?>
              </select>
            </div>

            <!-- Services supplémentaires — chargés depuis la base de données -->
            <div class="form-groupe">
              <label>Services supplémentaires (optionnel)</label>
              <div style="display: flex; flex-direction: column; gap: 0.8rem; margin-top: 0.5rem;">
                <?php
                // Parcourir les options depuis la base
                for ($i = 0; $i < count($options); $i++) {
                    // Créer un nom de checkbox simple depuis le nom du service
                    $nom = $options[$i]['nom_service'];
                    $slug = strtolower($nom);
                    $slug = str_replace(' ', '_', $slug);
                    $slug = str_replace('\'', '', $slug);
                    // Map vers les noms utilisés dans traiter_reservation.php
                    $map = array(
                        'enregistrement_vidéo' => 'video',
                        'enregistrement_video' => 'video',
                        'forfait_multi-courses' => 'forfait',
                        'coaching_de_course' => 'coaching',
                        'anniversaire' => 'anniv',
                        'événement_d\'entreprise' => 'event_entreprise',
                        'événement_dentreprise' => 'event_entreprise',
                        'location_piste_privée' => 'location',
                        'location_piste_privee' => 'location'
                    );
                    $checkboxName = isset($map[$slug]) ? $map[$slug] : $slug;

                    echo '<label class="choix-item">';
                    echo '<input type="checkbox" name="' . $checkboxName . '" value="' . $checkboxName . '">';
                    echo htmlspecialchars($nom) . ' (+' . $options[$i]['prix'] . ' DT)';
                    echo '</label>';
                }
                ?>
              </div>
            </div>

            <!-- Instructeur optionnel (si coaching choisi) -->
            <?php if (count($instructeurs) > 0) { ?>
            <div class="form-groupe">
              <label for="instructeur">Instructeur préféré (optionnel)</label>
              <select id="instructeur" name="instructeur">
                <option value="">— Aucun / Pas de coaching —</option>
                <?php
                for ($i = 0; $i < count($instructeurs); $i++) {
                    echo '<option value="' . $instructeurs[$i]['id'] . '">' . htmlspecialchars($instructeurs[$i]['nom']) . ' — ' . htmlspecialchars($instructeurs[$i]['specialite']) . '</option>';
                }
                ?>
              </select>
            </div>
            <?php } ?>

            <div class="form-groupe">
              <label for="message">Demandes ou commentaires spéciaux</label>
              <textarea id="message" name="message" placeholder="Des exigences particulières ou des questions ?"></textarea>
            </div>

            <div class="form-groupe">
              <label class="choix-item">
                <input type="checkbox" name="conditions" required>
                J'accepte les
                <a href="#" style="color: var(--vert-neon);">conditions générales</a>
                et la
                <a href="#" style="color: var(--vert-neon);">politique de confidentialité</a>
              </label>
            </div>

            <button type="submit">SOUMETTRE LA RÉSERVATION ▶</button>

            <p style="text-align: center; font-size: 0.85rem; color: var(--gris); margin-top: 1rem;">
              * Champs obligatoires. Nous vous contacterons dans les 24 heures pour confirmer votre réservation.
            </p>

            <p style="text-align: center; margin-top: 1.5rem;">
              <a href="mes_reservations.php" style="color: var(--vert-neon); font-weight: 700;">→ Vérifier ou gérer mes réservations</a>
            </p>

          </form>
        </div>

        <!-- Informations complémentaires -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">

          <div style="background: var(--noir-2); border: 1px solid var(--bord); border-top: 3px solid var(--vert-neon); padding: 2rem; border-radius: 8px;">
            <span class="label-section" style="margin-bottom: 1.5rem;">Ce qu'il faut apporter</span>
            <ul class="liste-regles">
              <li><span>Pièce d'identité valide pour vérification de l'âge</span></li>
              <li><span>Chaussures fermées (obligatoire)</span></li>
              <li><span>Vêtements confortables</span></li>
              <li><span>Les cheveux longs doivent être attachés</span></li>
            </ul>
          </div>

          <div style="background: var(--noir-2); border: 1px solid var(--bord); border-top: 3px solid var(--cyan); padding: 2rem; border-radius: 8px;">
            <span class="label-section" style="margin-bottom: 1.5rem;">Politique d'annulation</span>
            <ul style="display: flex; flex-direction: column; gap: 0.8rem;">
              <li style="display: flex; gap: 0.8rem; color: var(--gris-clair); line-height: 1.7;">
                <span style="color: var(--vert-neon);">•</span>
                <span>Annulation gratuite jusqu'à 48 heures</span>
              </li>
              <li style="display: flex; gap: 0.8rem; color: var(--gris-clair); line-height: 1.7;">
                <span style="color: var(--vert-neon);">•</span>
                <span>Remboursement de 50% entre 24-48 heures</span>
              </li>
              <li style="display: flex; gap: 0.8rem; color: var(--gris-clair); line-height: 1.7;">
                <span style="color: var(--vert-neon);">•</span>
                <span>Aucun remboursement dans les 24 heures</span>
              </li>
              <li style="display: flex; gap: 0.8rem; color: var(--gris-clair); line-height: 1.7;">
                <span style="color: var(--vert-neon);">•</span>
                <span>Reprogrammation gratuite à tout moment</span>
              </li>
            </ul>
          </div>

          <div style="background: var(--noir-2); border: 1px solid var(--bord); border-top: 3px solid var(--jaune); padding: 2rem; border-radius: 8px;">
            <span class="label-section" style="margin-bottom: 1.5rem;">Conditions d'âge</span>
            <ul style="display: flex; flex-direction: column; gap: 0.8rem;">
              <li style="display: flex; gap: 0.8rem; color: var(--gris-clair); line-height: 1.7;">
                <span style="color: var(--jaune);">•</span>
                <span>Sessions enfants : 8-12 ans</span>
              </li>
              <li style="display: flex; gap: 0.8rem; color: var(--gris-clair); line-height: 1.7;">
                <span style="color: var(--jaune);">•</span>
                <span>Sessions adultes : 13+ ans</span>
              </li>
              <li style="display: flex; gap: 0.8rem; color: var(--gris-clair); line-height: 1.7;">
                <span style="color: var(--jaune);">•</span>
                <span>Taille minimale : 140 cm (4'7")</span>
              </li>
              <li style="display: flex; gap: 0.8rem; color: var(--gris-clair); line-height: 1.7;">
                <span style="color: var(--jaune);">•</span>
                <span>Autorisation parentale pour les -18 ans</span>
              </li>
            </ul>
          </div>

          <div style="background: var(--noir-2); border: 1px solid var(--bord); padding: 2rem; border-radius: 8px;">
            <span class="label-section" style="margin-bottom: 1.5rem;">Consignes de sécurité</span>
            <ul style="display: flex; flex-direction: column; gap: 0.8rem;">
              <li style="display: flex; gap: 0.8rem; color: var(--gris-clair); line-height: 1.7;">
                <span style="color: var(--vert-neon);">•</span>
                <span>Briefing de sécurité obligatoire</span>
              </li>
              <li style="display: flex; gap: 0.8rem; color: var(--gris-clair); line-height: 1.7;">
                <span style="color: var(--vert-neon);">•</span>
                <span>Tout l'équipement fourni</span>
              </li>
              <li style="display: flex; gap: 0.8rem; color: var(--gris-clair); line-height: 1.7;">
                <span style="color: var(--vert-neon);">•</span>
                <span>Suivez les instructions des commissaires</span>
              </li>
              <li style="display: flex; gap: 0.8rem; color: var(--gris-clair); line-height: 1.7;">
                <span style="color: var(--vert-neon);">•</span>
                <span>Alcool et drogues interdits</span>
              </li>
            </ul>
          </div>

        </div>
      </div>
    </section>

    <div class="bande-cta">
      <h2>BESOIN D'AIDE ?</h2>
      <p>Notre équipe est disponible pour répondre à toutes vos questions sur les réservations.</p>
      <a href="contact.php" class="btn-blanc">CONTACTEZ-NOUS ▶</a>
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
