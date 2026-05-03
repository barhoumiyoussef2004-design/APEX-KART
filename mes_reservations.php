<?php

require_once 'config.php';

$message = '';
$messageType = '';
$reservations = array();
$email = '';

// Si l'email est passé dans l'URL (depuis confirmation.php)
if (isset($_GET['email'])) {
    $email = $_GET['email'];
}

// TRAITER LA RECHERCHE PAR EMAIL
if (isset($_POST['chercher_email'])) {//lorsqu'on appui sur le bouton "rechercher"
    $email = $_POST['email'];
    
    try {
        // trouver les réservations d'un pilote
        $sql = "SELECT r.id, r.id_pilote, r.date_session, r.heure_session, r.statut, r.prix_total,
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

// TRAITER LA MODIFICATION (TÉLÉPHONE, EMAIL, SERVICES)
if (isset($_POST['modifier'])) {//lorsqu'on appui sur le bouton "modifier"
    $idPilote = $_POST['pilote_id'];
    $idReservation = $_POST['reservation_id'];
    $nouveauTelephone = $_POST['nouveau_telephone'];
    $nouveauEmail = $_POST['nouveau_email'];
    $nouveauPrixTotal = $_POST['nouveau_prix_total'];
    
    try {
        // UPDATE sur la table pilotes (téléphone et email)
        $stmt = $pdo->prepare("UPDATE pilotes SET telephone = :telephone, email = :email WHERE id = :id");
        $stmt->execute(array(
            ':telephone' => $nouveauTelephone,
            ':email' => $nouveauEmail,
            ':id' => $idPilote
        ));
        
        // Mettre à jour les services si modifiés
        if (isset($_POST['services_modifies']) && is_array($_POST['services_modifies'])) {
            // Supprimer les anciens services de cette réservation
            $stmt = $pdo->prepare("DELETE FROM reservation_services WHERE id_reservation = :id");
            $stmt->execute(array(':id' => $idReservation));
            
            // Réinsérer les services avec les nouvelles quantités
            $quantites = isset($_POST['quantites_modifies']) ? $_POST['quantites_modifies'] : array();
            foreach ($_POST['services_modifies'] as $idService) {
                $quantite = isset($quantites[$idService]) ? intval($quantites[$idService]) : 1;
                $stmt = $pdo->prepare("INSERT INTO reservation_services (id_reservation, id_service, quantite) VALUES (:idRes, :idServ, :qty)");
                $stmt->execute(array(
                    ':idRes' => $idReservation,
                    ':idServ' => $idService,
                    ':qty' => $quantite
                ));
            }
        }
        
        // UPDATE prix_total dans reservations
        $stmt = $pdo->prepare("UPDATE reservations SET prix_total = :total WHERE id = :id");
        $stmt->execute(array(
            ':total' => $nouveauPrixTotal,
            ':id' => $idReservation
        ));
        
        // Mettre à jour l'email pour la session
        $email = $nouveauEmail;
        
        $message = 'Informations modifiées avec succès ! Le statut reste en attente.';
        $messageType = 'succes';
        
        // Recharger les réservations
        $sql = "SELECT r.id, r.id_pilote, r.date_session, r.heure_session, r.statut, r.prix_total,
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

// TRAITER L'ANNULATION DE RÉSERVATION
if (isset($_POST['annuler'])) {
    $idReservation = $_POST['reservation_id'];
    
    try {
        // UPDATE pour changer le statut à 'annule' (plutôt que DELETE)
        $stmt = $pdo->prepare("UPDATE reservations SET statut = 'annule' WHERE id = :id");
        $stmt->execute(array(':id' => $idReservation));
        
        $message = 'Réservation annulée avec succès.';
        $messageType = 'succes';
        
        // Recharger les réservations
        $sql = "SELECT r.id, r.id_pilote, r.date_session, r.heure_session, r.statut, r.prix_total,
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

                     <form method="post" action="mes_reservations.php" style="max-width: 600px; margin:0 auto;">
                         <div class="form-groupe" style="margin-bottom: 1rem;">
                             <label for="email">Adresse email utilisée lors de la réservation *</label>
                             <input type="email" id="email" name="email" value="<?php echo $email; ?>" placeholder="jean@exemple.tn" required style="width: 100%; padding:0.8rem; font-size: 1rem;">
                         </div>
                         <button type="submit" name="chercher_email" style="white-space: nowrap; padding:0.6rem 1rem; font-size: 0.85rem; display: block; margin:0 auto;">RECHERCHER ▶</button>
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
                                <?php echo $res['nom_complet']; ?>
                            </div>
                            <div>
                                <strong style="color: var(--vert-neon);">Email :</strong><br>
                                <?php echo $res['email']; ?>
                            </div>
                            <div>
                                <strong style="color: var(--vert-neon);">Téléphone :</strong><br>
                                <?php echo $res['telephone']; ?>
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
                                <span style="font-size: 1.2rem; font-weight: 700;"><?php echo $res['prix_total']; ?> DT</span>
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
                                echo $catLabel . $services[$j]['nom_service'];
                                if ($services[$j]['quantite'] > 1) {
                                    echo ' (' . $services[$i]['quantite'] . ' Personne(s))';
                                }
                                echo ' — <span style="color: var(--vert-neon);">' . $services[$j]['prix'] . ' DT</span><br>';
                            }
                            ?>
                        </div>

                        <!-- Modifier les informations (téléphone, email, services) -->
                        <div style="background: rgba(0,191,255,0.1); border: 1px solid var(--cyan); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                            <strong style="color: var(--cyan);">Modifier vos informations :</strong>
                            <form method="post" action="mes_reservations.php" style="margin-top: 1rem;" id="form-modif-<?php echo $res['id']; ?>">
                                <input type="hidden" name="pilote_id" value="<?php echo $res['id_pilote']; ?>">
                                <input type="hidden" name="reservation_id" value="<?php echo $res['id']; ?>">
                                <input type="hidden" name="nouveau_prix_total" id="nouveau_prix_<?php echo $res['id']; ?>" value="<?php echo $res['prix_total']; ?>">
                                
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                    <div class="form-groupe">
                                        <label>Email</label>
                                        <input type="email" name="nouveau_email" value="<?php echo $res['email']; ?>" required style="width: 100%;">
                                    </div>
                                    <div class="form-groupe">
                                        <label>Téléphone</label>
                                        <input type="tel" name="nouveau_telephone" value="<?php echo $res['telephone']; ?>" required style="width: 100%;">
                                    </div>
                                </div>
                                
                                <!-- Modifier les services -->
                                <div class="form-groupe" style="margin-bottom: 1rem;">
                                    <label>Services</label>
                                    <div style="margin-top: 0.5rem;">
                                        <?php
                                        $servicesActuels = array();
                                        for ($j = 0; $j < count($services); $j++) {
                                            $servicesActuels[$services[$j]['nom_service']] = $services[$j]['quantite'];
                                        }
                                        
                                        // Charger tous les services disponibles
                                        try {
                                            $stmtAllServ = $pdo->query("SELECT id, nom_service, prix, categorie FROM services ORDER BY categorie, nom_service");
                                            $tousServices = $stmtAllServ->fetchAll();
                                        } catch (PDOException $e) {
                                            $tousServices = array();
                                        }
                                        
                                        for ($j = 0; $j < count($tousServices); $j++) {
                                            $srv = $tousServices[$j];
                                            $checked = isset($servicesActuels[$srv['nom_service']]) ? 'checked' : '';
                                            $qty = isset($servicesActuels[$srv['nom_service']]) ? $servicesActuels[$srv['nom_service']] : 1;
                                            $isGroupe = stripos($srv['nom_service'], 'groupe') !== false; //verifie si le service est forfait groupe(permet quatité>1)
                                        ?>
                                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; padding: 0.5rem; background: rgba(255,255,255,0.03); border-radius: 4px;">
                                            <input type="checkbox" name="services_modifies[]" value="<?php echo $srv['id']; ?>" <?php echo $checked; ?> onchange="calculerPrixModif(<?php echo $res['id']; ?>)">
                                            <span style="flex: 1; color: var(--blanc);"><?php echo $srv['nom_service']; ?> — <span style="color: var(--vert-neon);"><?php echo $srv['prix']; ?> DT</span></span>

                                            <?php if ($isGroupe): ?>
                                                <!-- Forfait groupe: editable quantity -->
                                                <input type="number"
                                                    name="quantites_modifies[<?php echo $srv['id']; ?>]"
                                                    value="<?php echo $qty; ?>"
                                                    min="1" max="10"
                                                    style="width: 50px; text-align: center; padding: 0.3rem; background: var(--noir-3); border: 1px solid var(--bord); color: var(--blanc); border-radius: 4px;"
                                                    onchange="calculerPrixModif(<?php echo $res['id']; ?>)">
                                            <?php else: ?>
                                                <!-- Other services: fixed quantity = 1, hidden -->
                                                <input type="hidden" name="quantites_modifies[<?php echo $srv['id']; ?>]" value="1">
                                            <?php endif; ?>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                
                                <!-- Affichage du prix en temps réel -->
                                <div id="affichage-prix-<?php echo $res['id']; ?>" style="background: rgba(0,255,136,0.1); border: 1px solid var(--vert-neon); padding: 1rem; border-radius: 8px; text-align: center; margin-bottom: 1rem;">
                                    <div style="color: var(--vert-neon); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">💰 Nouveau Prix Total</div>
                                    <div style="color: #fff; font-size: 2rem; font-weight: 700; font-family: Impact, sans-serif;" id="prix-modif-<?php echo $res['id']; ?>"><?php echo $res['prix_total']; ?> DT</div>
                                </div>
                                
                                <button type="submit" name="modifier" style="white-space: nowrap;">MODIFIER ✓</button>
                            </form>
                        </div>

                <!-- Boutons d'action (si pas annulée) -->
                        <?php if ($res['statut'] !== 'annule') { ?>
                        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                            
                            <!-- Annuler la réservation -->
                            <form method="post" action="mes_reservations.php" style="display: inline;">
                                <input type="hidden" name="reservation_id" value="<?php echo $res['id']; ?>">
                                <input type="hidden" name="email" value="<?php echo $email; ?>">
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
    <script>
    // Prix des services pour calcul en temps réel
    var prixServices = {
        <?php
        try {
            $stmtPrix = $pdo->query("SELECT id, prix FROM services");
            $tousServices = $stmtPrix->fetchAll();
            $parts = array();
            for ($i = 0; $i < count($tousServices); $i++) {
                $parts[] = $tousServices[$i]['id'] . ': ' . $tousServices[$i]['prix'];
            }//exple: $parts = ["3: 50", "5: 120", "7: 30"];
            echo implode(",\n        ", $parts);//exple: var prixServices = {3: 50, 5: 120, 7: 30};
        } catch (PDOException $e) {
            echo "";
        }
        ?>
    };
    
    function calculerPrixModif(reservationId) {
        var checkboxes = document.querySelectorAll('#form-modif-' + reservationId + ' input[name="services_modifies[]"]');
        //Elle cible uniquement les checkboxes du formulaire de cette réservation (grâce à l'ID unique form-modif-5 par exemple), 
        //pour ne pas mélanger avec d'autres formulaires sur la même page.
        var total = 0;
        
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                var serviceId = checkboxes[i].value;
                //Pour chaque checkbox cochée, elle récupère l'id du service
                if (prixServices[serviceId]) {
                    total = total + prixServices[serviceId];
                }
            }
        }
        
        var prixElement = document.getElementById('prix-modif-' + reservationId);
        if (prixElement) {
            prixElement.textContent = total + ' DT';// affichage visible
        }
        
        var prixInput = document.getElementById('nouveau_prix_' + reservationId);
        if (prixInput) {
            prixInput.value = total;
        }
        //ligne 338:<input type="hidden" id="nouveau_prix_5" name="nouveau_prix" value="0">
//Quand l'utilisateur clique Enregistrer, le formulaire soumet ce champ au serveur, et le PHP peut faire :
//php$nouveauPrix = $_POST['nouveau_prix']; // récupère le total calculé par JS
//Sans ça, le PHP ne saurait pas quel prix enregistrer dans la base de données.
    }
    
    // Initialiser les prix au chargement
    window.addEventListener('DOMContentLoaded', function() {
        //DOMContentLoaded veut dire : "exécute ce code une fois que tout le HTML est chargé".
        <?php
        if (count($reservations) > 0) {
            for ($i = 0; $i < count($reservations); $i++) {//pour chaque reservation, affiche le prix
                echo 'calculerPrixModif(' . $reservations[$i]['id'] . ');' . "\n        ";
            }
        }
        ?>
    });
    </script>
</body>
</html>
