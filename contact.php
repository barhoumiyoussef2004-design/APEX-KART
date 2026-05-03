<?php

require_once 'config.php';

$message = ''; 
$messageType = '';

// Traiter la soumission du formulaire de contact
if (isset($_POST['envoyer_message'])) {//Vérifie si le bouton “envoyer” a été cliqué
  //On récupère les champs:
  $nomComplet = $_POST['prenom'] . ' ' . $_POST['nom'];
  $email = $_POST['email'];
  $telephone = isset($_POST['telephone']) ? $_POST['telephone'] : '';//optionnel => Si le champ n’existe pas :on met une chaîne vide ''
  $sujet = isset($_POST['sujet']) ? $_POST['sujet'] : '';//optionnel
  $msg = $_POST['message'];

  try {
    // INSERT du message dans la table messages
    $stmt = $pdo->prepare("INSERT INTO messages (nom_complet, email, telephone, sujet, message) VALUES (:nom, :email, :tel, :sujet, :msg)");
    $stmt->execute(array(
      ':nom' => $nomComplet,
      ':email' => $email,
      ':tel' => $telephone,
      ':sujet' => $sujet,
      ':msg' => $msg
    ));

    $message = 'Merci ' . $_POST['prenom'] . ' ! Votre message a été envoyé avec succès. Nous vous répondrons dans les 24 heures.';
    $messageType = 'succes';
  } catch (PDOException $e) {
    $message = 'Erreur lors de l\'envoi : ' . $e->getMessage();
    $messageType = 'erreur';
  }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact — APEX KART Karting</title>
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
      <a href="contact.php" class="actif">Contact</a>
      <a href="reserver.php" class="nav-cta">Réserver ▶</a>
    </nav>
  </header>

  <main>
    <div id="top"></div>

    <div class="page-header">
      <div class="page-header-contenu">
        <span class="label-section" style="margin-bottom: 14px;">Nous joindre</span>
        <h1>CONTACT &amp;<br><span>ACCÈS</span></h1>
        <p>Notre équipe répond dans les 24 heures ouvrées. Pour une réservation urgente, appelez-nous directement.</p>
      </div>
    </div>

    <!--FORMULAIRE + COORDONNEES-->
    <section>
      <div class="grille-2">

        <!-- Formulaire de contact -->
        <div>
          <span class="label-section">Écrivez-nous</span>
          <h2 class="titre-section" style="margin-top: 10px; margin-bottom: 28px;">Envoyez un <span class="rouge">message</span></h2>

          <!-- Message de confirmation/erreur -->
          <?php if ($message !== '') { ?>
          <div style="padding: 1rem; margin-bottom: 1.5rem; border-radius: 8px;
              <?php if ($messageType === 'succes') echo 'background: rgba(0,255,136,0.1); border: 1px solid var(--vert-neon); color: var(--vert-neon);'; ?>
              <?php if ($messageType === 'erreur') echo 'background: rgba(255,51,102,0.1); border: 1px solid #ff3366; color: #ff3366;'; ?>">
            <?php echo $message; ?>
          </div>
          <?php } ?>

          <form method="post" action="contact.php" class="formulaire-bloc">

            <div class="form-ligne">
              <div class="form-groupe">
                <label for="prenom">Prénom *</label>
                <input type="text" id="prenom" name="prenom" placeholder="Lucas" required>
              </div>
              <div class="form-groupe">
                <label for="nom">Nom *</label>
                <input type="text" id="nom" name="nom" placeholder="Martin" required>
              </div>
            </div>

            <div class="form-groupe">
              <label for="email">Adresse email *</label>
              <input type="email" id="email" name="email" placeholder="lucas@exemple.tn" required>
            </div>

            <div class="form-groupe">
              <label for="telephone">Téléphone</label>
              <input type="tel" id="telephone" name="telephone" placeholder="+216 76 123 456">
            </div>

            <div class="form-groupe">
              <label for="sujet">Sujet de votre message</label>
              <select id="sujet" name="sujet">
                <option value="">— Choisissez un sujet —</option>
                <option value="reserver">Réservation &amp; disponibilités</option>
                <option value="groupe">Événement groupe / entreprise</option>
                <option value="competition">Compétition &amp; championnat</option>
                <option value="services">Question sur les services</option>
                <option value="partenariat">Partenariat / sponsoring</option>
                <option value="autre">Autre demande</option>
              </select>
            </div>

            <div class="form-groupe">
              <label for="message">Votre message *</label>
              <textarea id="message" name="message" placeholder="Décrivez votre demande en détail…" required></textarea>
            </div>

            <div class="form-groupe">
              <label class="choix-item">
                <input type="checkbox" name="rgpd" required>
                J'accepte que mes données soient utilisées pour traiter ma demande.
                <a href="#" class="lien-discret" style="margin-left: 4px;">Politique de confidentialité</a>
              </label>
            </div>

            <button type="submit" name="envoyer_message">Envoyer le message ▶</button>

          </form>
        </div>

        <!-- Coordonnées et accès -->
        <div style="display: flex; flex-direction: column; gap: 24px;">

          <!-- Bloc coordonnées -->
          <div style="background: var(--noir-2); border: 1px solid var(--bord); border-top: 3px solid var(--rouge); padding: 28px;">
            <span class="label-section" style="margin-bottom: 20px;">Informations pratiques</span>
            <ul class="coord-liste">
              <li>
                <span class="coord-icone">📍</span>
                <div>
                  <span class="coord-label">Adresse: </span>
                  <span class="coord-valeur">
                    Ctra A-384, Km 101 29320 Bizerte
                  </span>
                </div>
              </li>
              <li>
                <span class="coord-icone">📞</span>
                <div>
                  <span class="coord-label">Téléphone: </span>
                  <span class="coord-valeur">
                    <a href="tel:+21671440300">+216 71 440 300</a><br>
                    Lun–Ven : 10h–20h / Sam–Dim : 9h–21h
                  </span>
                </div>
              </li>
              <li>
                <span class="coord-icone">✉️</span>
                <div>
                  <span class="coord-label">Email: </span>
                  <span class="coord-valeur">
                    <a href="mailto:contact@apexkart.tn">contact@apexkart.tn</a><br>
                    Réponse sous 24h ouvrées
                  </span>
                </div>
              </li>
              <li>
                <span class="coord-icone">⏰</span>
                <div>
                  <span class="coord-label">Horaires d'ouverture:<br></span>
                  <span class="coord-valeur">
                    Lun – Ven : 10h00 – 22h00<br>
                    Samedi : 09h00 – 23h00<br>
                    Dimanche : 09h00 – 20h00<br>
                    <strong style="color: var(--rouge);">Ouvert 365 jours par an</strong>
                  </span>
                </div>
              </li>
            </ul>
          </div>

          <!-- Comment venir -->
          <div style="background: var(--noir-2); border: 1px solid var(--bord); padding: 28px;">
            <span class="label-section" style="margin-bottom: 18px;">Comment venir</span>
            <ul style="list-style: none; display: flex; flex-direction: column; gap: 14px;">
              <li style="display: flex; gap: 12px; align-items: flex-start;">
                <span style="font-size: 1.1rem; flex-shrink: 0;">🚗</span>
                <div>
                  <strong style="display: block; color: var(--blanc); font-size: 0.9rem; margin-bottom: 3px;">En voiture</strong>
                  <span style="font-size: 0.85rem; color: var(--gris-clair);">A86 sortie 7 — Parking gratuit 200 places</span>
                </div>
              </li>
              <li style="display: flex; gap: 12px; align-items: flex-start;">
                <span style="font-size: 1.1rem; flex-shrink: 0;">🚇</span>
                <div>
                  <strong style="display: block; color: var(--blanc); font-size: 0.9rem; margin-bottom: 3px;">RER B</strong>
                  <span style="font-size: 0.85rem; color: var(--gris-clair);">Gare La Courneuve-Aubervilliers (10 min à pied)</span>
                </div>
              </li>
              <li style="display: flex; gap: 12px; align-items: flex-start;">
                <span style="font-size: 1.1rem; flex-shrink: 0;">🚌</span>
                <div>
                  <strong style="display: block; color: var(--blanc); font-size: 0.9rem; margin-bottom: 3px;">Bus</strong>
                  <span style="font-size: 0.85rem; color: var(--gris-clair);">Lignes 170 & 252 — Arrêt "taher Haddad"</span>
                </div>
              </li>
            </ul>
          </div>

          <a href="reserver.php" class="btn btn-rouge" style="display: block; text-align: center; font-size: 1rem;">
            ▶ Réserver directement en ligne
          </a>

        </div>
      </div>
    </section>

    <!--FAQ-->
    <section class="section-fond-2">
      <div class="en-tete-section">
        <span class="label-section">Avant de venir</span>
        <h2 class="titre-section">Questions <span class="rouge">fréquentes</span></h2>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; max-width: 900px;">

        <details style="background: var(--noir-3); border: 1px solid var(--bord); border-left: 3px solid var(--rouge); padding: 18px 22px; border-radius: 2px; cursor: pointer;">
          <summary style="font-family: Arial, sans-serif; font-size: 0.95rem; font-weight: 700; text-transform: uppercase; color: var(--blanc); list-style: none;">
            Dois-je amener mon casque ?
          </summary>
          <p style="margin-top: 12px; color: var(--gris-clair); font-size: 0.88rem; line-height: 1.7;">
            Non, casques FIA et gants sont fournis et désinfectés. Un sous-casque est obligatoire
            (vendu 2€ sur place ou amenez le vôtre).
          </p>
        </details>

        <details style="background: var(--noir-3); border: 1px solid var(--bord); border-left: 3px solid var(--rouge); padding: 18px 22px; border-radius: 2px; cursor: pointer;">
          <summary style="font-family: Arial, sans-serif; font-size: 0.95rem; font-weight: 700; text-transform: uppercase; color: var(--blanc); list-style: none;">
            Y a-t-il un âge minimum ?
          </summary>
          <p style="margin-top: 12px; color: var(--gris-clair); font-size: 0.88rem; line-height: 1.7;">
            À partir de 8 ans sur nos karts enfants 100cc. Karts adultes à partir de 14 ans.
            Aucun permis de conduire requis.
          </p>
        </details>

        <details style="background: var(--noir-3); border: 1px solid var(--bord); border-left: 3px solid var(--rouge); padding: 18px 22px; border-radius: 2px; cursor: pointer;">
          <summary style="font-family: Arial, sans-serif; font-size: 0.95rem; font-weight: 700; text-transform: uppercase; color: var(--blanc); list-style: none;">
            Puis-je venir sans réservation ?
          </summary>
          <p style="margin-top: 12px; color: var(--gris-clair); font-size: 0.88rem; line-height: 1.7;">
            Oui, sous réserve de disponibilité. Nous recommandons de réserver en ligne,
            surtout le week-end et pendant les vacances.
          </p>
        </details>

        <details style="background: var(--noir-3); border: 1px solid var(--bord); border-left: 3px solid var(--rouge); padding: 18px 22px; border-radius: 2px; cursor: pointer;">
          <summary style="font-family: Arial, sans-serif; font-size: 0.95rem; font-weight: 700; text-transform: uppercase; color: var(--blanc); list-style: none;">
            Ouvre-t-on par mauvais temps ?
          </summary>
          <p style="margin-top: 12px; color: var(--gris-clair); font-size: 0.88rem; line-height: 1.7;">
            Circuit en plein air. Fermeture possible en cas de mauvaises conditions météorologiques.
          </p>
        </details>

      </div>
    </section>

    <div class="bande-cta">
      <h2>ON VOUS ATTEND SUR LA PISTE !</h2>
      <p>Réservez votre créneau en 2 minutes. Paiement sur place à votre arrivée.</p>
      <a href="reserver.php" class="btn-blanc">RÉSERVER MAINTENANT ▶</a>
    </div>
    <a href="#top" class="scroll-top">↑</a>

  </main>

  <footer>
    <div class="footer-grille">
      <div class="footer-col large">
        <span class="footer-logo">APEX <span>KART</span></span>
        <p class="footer-desc">Circuit de karting professionnel. 1200 mètres d'asphalte et des sensations garanties pour tous.</p>
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
        <span class="footer-col-titre">Contact direct</span>
        <ul class="footer-liens">
          <li><a href="tel:+21671440300">📞 +216 71 440 300</a></li>
          <li><a href="mailto:contact@apexkart.tn">✉️ contact@apexkart.tn</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <span class="footer-col-titre">Réserver</span>
        <ul class="footer-liens">
          <li><a href="reserver.php" style="color: var(--rouge);">→ Réservation en ligne</a></li>
          <li><a href="services.html" style="color: var(--gris-clair);">→ Voir les tarifs</a></li>
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
