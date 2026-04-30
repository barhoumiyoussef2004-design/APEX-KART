<?php
// =============================================
// MES_RESERVATIONS.PHP - Voir, modifier, annuler
// =============================================

// Inclure la connexion
require_once 'config.php';

$message = '';
$messageType = '';
$reservations = array();
$email = '';

// Si l'email est passé dans l'URL (depuis confirmation.php)
if (isset($_GET['email'])) {
    $email = $_GET['email'];
}

// =============================================
// TRAITER LA RECHERCHE PAR EMAIL
// =============================================
if (isset($_POST['chercher_email'])) {
    $email = $_POST['email'];
    
    try {
        // SELECT avec JOIN pour trouver les réservations d'un pilote
        $sql = "SELECT r.id, r.id_pilote, r.date_session, r.heure_session, r.statut,
                       p.nom_complet, p.email, p.telephone
                FROM reservations r
                JOIN pilotes p ON r.id_pilote = p.id
                WHERE p.email = :email
                ORDER BY r.date_session DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':email' => $email));
        $reservations = $stmt->fetchAll();
        
        if (count($reservations) === 0) {
            $message = 'Aucune réservation trouvée pour cet email.';
            $messageType = 'info';
        }
    } catch (PDOException $e) {
        $message = 'Erreur : ' . $e->getMessage();
        $messageType = 'erreur';
    }
}

// =============================================
// TRAITER LA MODIFICATION DU TÉLÉPHONE
// =============================================
if (isset($_POST['modifier_telephone'])) {
    $idPilote = $_POST['pilote_id'];
    $nouveauTelephone = $_POST['nouveau_telephone'];
    
    try {
        // UPDATE sur la table pilotes
        $stmt = $pdo->prepare("UPDATE pilotes SET telephone = :telephone WHERE id = :id");
        $stmt->execute(array(
            ':telephone' => $nouveauTelephone,
            ':id' => $idPilote
        ));
        
        $message = 'Téléphone modifié avec succès !';
        $messageType = 'succes';
        
        // Recharger les réservations
        $sql = "SELECT r.id, r.id_pilote, r.date_session, r.heure_session, r.statut,
                       p.nom_complet, p.email, p.telephone
                FROM reservations r
                JOIN pilotes p ON r.id_pilote = p.id
                WHERE p.email = :email
                ORDER BY r.date_session DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':email' => $email));
        $reservations = $stmt->fetchAll();
        
    } catch (PDOException $e) {
        $message = 'Erreur : ' . $e->getMessage();
        $messageType = 'erreur';
    }
}

// =============================================
// TRAITER L'ANNULATION DE RÉSERVATION
// =============================================
if (isset($_POST['annuler'])) {
    $idReservation = $_POST['reservation_id'];
    
    try {
        // UPDATE pour changer le statut à 'annule' (plutôt que DELETE)
        $stmt = $pdo->prepare("UPDATE reservations SET statut = 'annule' WHERE id = :id");
        $stmt->execute(array(':id' => $idReservation));
        
        $message = 'Réservation annulée avec succès.';
        $messageType = 'succes';
        
        // Recharger les réservations
        $sql = "SELECT r.id, r.id_pilote, r.date_session, r.heure_session, r.statut,
                       p.nom_complet, p.email, p.telephone
                FROM reservations r
                JOIN pilotes p ON r.id_pilote = p.id
                WHERE p.email = :email
                ORDER BY r.date_session DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':email' => $email));
        $reservations = $stmt->fetchAll();
        
    } catch (PDOException $e) {
        $message = 'Erreur : ' . $e->getMessage();
        $messageType = 'erreur';
    }
}

// =============================================
// TRAITER LA CONFIRMATION DE RÉSERVATION
// =============================================
if (isset($_POST['confirmer'])) {
    $idReservation = $_POST['reservation_id'];
    
    try {
        // UPDATE pour changer le statut à 'confirme'
        $stmt = $pdo->prepare("UPDATE reservations SET statut = 'confirme' WHERE id = :id");
        $stmt->execute(array(':id' => $idReservation));
        
        $message = 'Réservation confirmée !';
        $messageType = 'succes';
        
        // Recharger les réservations
        $sql = "SELECT r.id, r.id_pilote, r.date_session, r.heure_session, r.statut,
                       p.nom_complet, p.email, p.telephone
                FROM reservations r
                JOIN pilotes p ON r.id_pilote = p.id
                WHERE p.email = :email
                ORDER BY r.date_session DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':email' => $email));
        $reservations = $stmt->fetchAll();
        
    } catch (PDOException $e) {
        $message = 'Erreur : ' . $e->getMessage();
        $messageType = 'erreur';
    }
}

// Labels pour affichage
$labelsStatut = array(
    'confirme' => 'Confirmée ✅',
    'en_attente' => 'En attente ⏳',
    'annule' => 'Annulée ❌'
);

$couleursStatut = array(
    'confirme' => 'var(--vert-neon)',
    'en_attente' => 'var(--jaune)',
    'annule' => '#ff3366'
);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Réservations — Apex Kart</title>
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
            <a href="reserver.php" class="nav-cta">Réserver ▶</a>
        </nav>
    </header>

    <main>
        <div id="top"></div>

        <div class="page-header">
            <div class="page-header-contenu">
                <span class="label-section" style="margin-bottom: 14px;">Mes Réservations</span>
                <h1>VÉRIFIEZ ET<br><span>GÉREZ VOS RÉSERVATIONS</span></h1>
                <p>Entrez votre email pour retrouver toutes vos réservations</p>
            </div>
        </div>

        <section class="section-fond-2">
            <div style="max-width: 900px; margin: 0 auto;">

                <!-- Formulaire de recherche par email -->
                <div class="formulaire-bloc" style="margin-bottom: 2rem;">
                    <span class="label-section">Rechercher</span>
                    <h2 class="titre-section" style="margin-top: 1rem; margin-bottom: 2rem;">TROUVER <span class="rouge">MA RÉSERVATION</span></h2>

                    <form method="post" action="mes_reservations.php">
                        <div style="display: flex; gap: 1rem; align-items: flex-end;">
                            <div class="form-groupe" style="flex: 1;">
                                <label for="email">Adresse email utilisée lors de la réservation *</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="jean@exemple.tn" required>
                            </div>
                            <button type="submit" name="chercher_email" style="white-space: nowrap;">RECHERCHER ▶</button>
                        </div>
                    </form>
                </div>

                <!-- Message de retour -->
                <?php if ($message !== '') { ?>
                <div style="padding: 1rem; margin-bottom: 1.5rem; border-radius: 8px; 
                    <?php if ($messageType === 'succes') echo 'background: rgba(0,255,136,0.1); border: 1px solid var(--vert-neon); color: var(--vert-neon);'; ?>
                    <?php if ($messageType === 'erreur') echo 'background: rgba(255,51,102,0.1); border: 1px solid #ff3366; color: #ff3366;'; ?>
                    <?php if ($messageType === 'info') echo 'background: rgba(255,153,0,0.1); border: 1px solid var(--jaune); color: var(--jaune);'; ?>">
                    <?php echo $message; ?>
                </div>
                <?php } ?>

                <!-- Affichage des réservations -->
                <?php if (count($reservations) > 0) { ?>
                    
                    <?php
                    // Loop pour afficher chaque réservation
                    for ($i = 0; $i < count($reservations); $i++) {
                        $res = $reservations[$i];
                        
                        // Récupérer les services de cette réservation
                        $sqlServices = "SELECT s.nom_service, s.categorie, s.prix, rs.quantite
                                        FROM reservation_services rs
                                        JOIN services s ON rs.id_service = s.id
                                        WHERE rs.id_reservation = :id";
                        $stmtServices = $pdo->prepare($sqlServices);
                        $stmtServices->execute(array(':id' => $res['id']));
                        $services = $stmtServices->fetchAll();
                        
                        // Calculer le prix total pour cette réservation
                        $prixTotal = 0;
                        for ($j = 0; $j < count($services); $j++) {
                            $prixTotal = $prixTotal + ($services[$j]['prix'] * $services[$j]['quantite']);
                        }
                    ?>
                    <div class="formulaire-bloc" style="margin-bottom: 2rem;">
                        <span class="label-section">Réservation #<?php echo $res['id']; ?></span>
                        <h2 class="titre-section" style="margin-top: 1rem; margin-bottom: 2rem;">
                            <?php
                            // Trouver le nom de la session principale
                            $sessionPrincipale = 'Session';
                            for ($j = 0; $j < count($services); $j++) {
                                if ($services[$j]['categorie'] === 'session') {
                                    $sessionPrincipale = $services[$j]['nom_service'];
                                    break;
                                }
                            }
                            echo $sessionPrincipale;
                            ?>
                        </h2>

                        <!-- Détails de la réservation -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                            <div>
                                <strong style="color: var(--vert-neon);">Nom :</strong><br>
                                <?php echo htmlspecialchars($res['nom_complet']); ?>
                            </div>
                            <div>
                                <strong style="color: var(--vert-neon);">Email :</strong><br>
                                <?php echo htmlspecialchars($res['email']); ?>
                            </div>
                            <div>
                                <strong style="color: var(--vert-neon);">Téléphone :</strong><br>
                                <?php echo htmlspecialchars($res['telephone']); ?>
                            </div>
                            <div>
                                <strong style="color: var(--vert-neon);">Date :</strong><br>
                                <?php echo $res['date_session']; ?>
                            </div>
                            <div>
                                <strong style="color: var(--vert-neon);">Créneau :</strong><br>
                                <?php echo $res['heure_session']; ?>
                            </div>
                            <div>
                                <strong style="color: var(--vert-neon);">Prix total :</strong><br>
                                <span style="font-size: 1.2rem; font-weight: 700;"><?php echo $prixTotal; ?> DT</span>
                            </div>
                        </div>

                        <!-- Statut de paiement -->
                        <div style="background: rgba(0,0,0,0.3); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                            <strong style="color: var(--vert-neon);">Statut :</strong>
                            <span style="color: <?php echo $couleursStatut[$res['statut']]; ?>; font-weight: 700; font-size: 1.1rem;">
                                <?php echo $labelsStatut[$res['statut']]; ?>
                            </span>
                        </div>

                        <!-- Services inclus -->
                        <div style="margin-bottom: 1.5rem;">
                            <strong style="color: var(--vert-neon);">Services inclus :</strong><br>
                            <?php
                            for ($j = 0; $j < count($services); $j++) {
                                $catLabel = ($services[$j]['categorie'] === 'session') ? '🏁 ' : '🎯 ';
                                echo $catLabel . htmlspecialchars($services[$j]['nom_service']);
                                if ($services[$j]['quantite'] > 1) {
                                    echo ' × ' . $services[$j]['quantite'];
                                }
                                echo ' — <span style="color: var(--vert-neon);">' . ($services[$j]['prix'] * $services[$j]['quantite']) . ' DT</span><br>';
                            }
                            ?>
                        </div>

                        <!-- Modifier le téléphone -->
                        <div style="background: rgba(0,191,255,0.1); border: 1px solid var(--cyan); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                            <strong style="color: var(--cyan);">Modifier votre numéro de téléphone :</strong>
                            <form method="post" action="mes_reservations.php" style="margin-top: 1rem; display: flex; gap: 1rem; align-items: flex-end;">
                                <input type="hidden" name="pilote_id" value="<?php echo $res['id_pilote']; ?>">
                                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                                <div class="form-groupe" style="flex: 1;">
                                    <input type="tel" name="nouveau_telephone" value="<?php echo htmlspecialchars($res['telephone']); ?>" required>
                                </div>
                                <button type="submit" name="modifier_telephone" style="white-space: nowrap;">MODIFIER</button>
                            </form>
                        </div>

                        <!-- Boutons d'action (si pas annulée) -->
                        <?php if ($res['statut'] !== 'annule') { ?>
                        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                            
                            <!-- Confirmer la réservation (si en attente) -->
                            <?php if ($res['statut'] === 'en_attente') { ?>
                            <form method="post" action="mes_reservations.php" style="display: inline;">
                                <input type="hidden" name="reservation_id" value="<?php echo $res['id']; ?>">
                                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                                <button type="submit" name="confirmer">CONFIRMER ✅</button>
                            </form>
                            <?php } ?>

                            <!-- Annuler la réservation -->
                            <form method="post" action="mes_reservations.php" style="display: inline;">
                                <input type="hidden" name="reservation_id" value="<?php echo $res['id']; ?>">
                                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                                <button type="submit" name="annuler" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?');">
                                    ANNULER LA RÉSERVATION ❌
                                </button>
                            </form>
                        </div>
                        <?php } ?>

                    </div>
                    <?php } ?>

                <?php } ?>

                <!-- Lien vers nouvelle réservation -->
                <div style="text-align: center; margin-top: 2rem;">
                    <a href="reserver.php" class="btn">FAIRE UNE NOUVELLE RÉSERVATION ▶</a>
                </div>

            </div>
        </section>

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
