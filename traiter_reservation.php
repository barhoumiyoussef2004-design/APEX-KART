<?php
// =============================================
// TRAITER_RESERVATION.PHP - Traitement du formulaire
// =============================================

// Inclure la connexion à la base de données
require_once 'config.php';

// Vérifier si le formulaire a été soumis avec POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Récupérer les données du formulaire
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $session = $_POST['session'];          // ex: 'enfants', 'adultes', 'groupe'
    $date = $_POST['date'];
    $creneau = $_POST['creneau'];
    $participants = $_POST['participants'];
    $message = isset($_POST['message']) ? $_POST['message'] : '';
    
    // Récupérer les karts choisis par type (ex: 'Sodi RSX' => 2)
    $kartsChosen = array();
    if (isset($_POST['karts']) && is_array($_POST['karts'])) {
        foreach ($_POST['karts'] as $modele => $qty) {
            if ($qty > 0) {
                // On stocke le modèle et la quantité (ex: "Sodi RSX (x2)")
                $kartsChosen[] = htmlspecialchars($modele) . ' (x' . intval($qty) . ')';
            }
        }
    }
    // On sauvegarde le tout dans la colonne id_kart (format texte)
    $idKart = count($kartsChosen) > 0 ? implode(', ', $kartsChosen) : null;
    $idInstructeur = isset($_POST['instructeur']) && $_POST['instructeur'] !== '' && $_POST['instructeur'] !== '0' ? $_POST['instructeur'] : null;
    
    // Si pas de préférance (0 ou vide), choisir le premier instructeur disponible
    if ($idInstructeur === null) {
        try {
            $stmt = $pdo->query("SELECT id FROM instructeurs ORDER BY date_embauche ASC LIMIT 1");
            $premierInstructeur = $stmt->fetch();
            if ($premierInstructeur) {
                $idInstructeur = $premierInstructeur['id'];
            }
        } catch (PDOException $e) {
            $idInstructeur = null;
        }
    }
    
    // Récupérer les services supplémentaires cochés
    $servicesSelectionnes = array();
    
    if (isset($_POST['video'])) $servicesSelectionnes[] = 'video';
    if (isset($_POST['forfait'])) $servicesSelectionnes[] = 'forfait';
    if (isset($_POST['coaching'])) $servicesSelectionnes[] = 'coaching';
    if (isset($_POST['anniv'])) $servicesSelectionnes[] = 'anniv';
    if (isset($_POST['event_entreprise'])) $servicesSelectionnes[] = 'event_entreprise';
    if (isset($_POST['location'])) $servicesSelectionnes[] = 'location';
    
    // =============================================
    // ÉTAPE 1 : Insérer ou retrouver le pilote
    // =============================================
    try {
        // Chercher si le pilote existe déjà par email
        $stmt = $pdo->prepare("SELECT id FROM pilotes WHERE email = :email");
        $stmt->execute(array(':email' => $email));
        $pilote = $stmt->fetch();
        
        if ($pilote) {
            // Le pilote existe déjà, on récupère son id
            $idPilote = $pilote['id'];
            
            // Mettre à jour ses informations (nom, téléphone)
            $stmt = $pdo->prepare("UPDATE pilotes SET nom_complet = :nom, telephone = :telephone WHERE id = :id");
            $stmt->execute(array(
                ':nom' => $nom,
                ':telephone' => $telephone,
                ':id' => $idPilote
            ));
        } else {
            // Nouveau pilote, on l'insère
            $stmt = $pdo->prepare("INSERT INTO pilotes (nom_complet, email, telephone) VALUES (:nom, :email, :telephone)");
            $stmt->execute(array(
                ':nom' => $nom,
                ':email' => $email,
                ':telephone' => $telephone
            ));
            $idPilote = $pdo->lastInsertId();
        }
        
        // =============================================
        // ÉTAPE 2 : Insérer la réservation
        // =============================================
        $stmt = $pdo->prepare("INSERT INTO reservations (id_pilote, date_session, heure_session, statut, id_kart, id_instructeur, prix_total) VALUES (:idPilote, :date, :heure, 'en_attente', :kart, :instructeur, 0)");
        $stmt->execute(array(
            ':idPilote' => $idPilote,
            ':date' => $date,
            ':heure' => $creneau,
            ':kart' => $idKart,
            ':instructeur' => $idInstructeur
        ));
        $idReservation = $pdo->lastInsertId();
        
        // =============================================
        // ÉTAPE 3 : Insérer les services dans reservation_services
        // =============================================
        
        // Mapping entre les noms des checkboxes et les noms dans la table services
        $mapServices = array(
            'enfants' => 'Session Enfants',
            'adultes' => 'Session Adultes',
            'groupe' => 'Forfait Groupe',
            'video' => 'Enregistrement vidéo',
            'forfait' => 'Forfait Multi-Courses',
            'coaching' => 'Coaching de course',
            'anniv' => 'Anniversaire',
            'event_entreprise' => 'Événement d\'Entreprise',
            'location' => 'Location Piste Privée'
        );
        
        // Ajouter la session principale
        $nomServiceSession = $mapServices[$session];
        $stmt = $pdo->prepare("SELECT id FROM services WHERE nom_service = :nom");
        $stmt->execute(array(':nom' => $nomServiceSession));
        $serviceSession = $stmt->fetch();
        
        if ($serviceSession) {
            $stmt = $pdo->prepare("INSERT INTO reservation_services (id_reservation, id_service, quantite) VALUES (:idRes, :idServ, :quantite)");
            $stmt->execute(array(
                ':idRes' => $idReservation,
                ':idServ' => $serviceSession['id'],
                ':quantite' => $participants
            ));
        }
        
        // Ajouter les services supplémentaires
        for ($i = 0; $i < count($servicesSelectionnes); $i++) {
            $nomService = $mapServices[$servicesSelectionnes[$i]];
            
            $stmt = $pdo->prepare("SELECT id FROM services WHERE nom_service = :nom");
            $stmt->execute(array(':nom' => $nomService));
            $service = $stmt->fetch();
            
            if ($service) {
                $stmt = $pdo->prepare("INSERT INTO reservation_services (id_reservation, id_service, quantite) VALUES (:idRes, :idServ, 1)");
                $stmt->execute(array(
                    ':idRes' => $idReservation,
                    ':idServ' => $service['id']
                ));
            }
        }

        // =============================================
        // ÉTAPE 4 : Calculer et sauvegarder le prix total
        // =============================================
        $stmt = $pdo->prepare("SELECT SUM(s.prix * rs.quantite) as total FROM reservation_services rs JOIN services s ON rs.id_service = s.id WHERE rs.id_reservation = :idRes");
        $stmt->execute(array(':idRes' => $idReservation));
        $result = $stmt->fetch();
        $prixTotal = $result['total'] ? $result['total'] : 0;

        $stmt = $pdo->prepare("UPDATE reservations SET prix_total = :total WHERE id = :id");
        $stmt->execute(array(':total' => $prixTotal, ':id' => $idReservation));
        
        // Rediriger vers la page de confirmation
        header('Location: confirmation.php?id=' . $idReservation);
        exit();
        
    } catch (PDOException $e) {
        // En cas d'erreur, afficher un message
        echo "<h1>Erreur lors de la réservation</h1>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo '<a href="reserver.php">Retour au formulaire</a>';
    }
}
?>
