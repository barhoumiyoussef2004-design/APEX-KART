<?php

require_once 'config.php';

// Vérifier si l'ID est passé dans l'URL
if (!isset($_GET['id'])) {
    header('Location: reserver.php');
    exit();
}//=> C'est une protection contre l'accès direct à confirmation.php sans ID

/* $_GET['id'] est un tableau superglobal PHP qui récupère la valeur du paramètre `id` de la chaîne de requête de l'URL.
Lorsqu'un navigateur envoie une requête GET à un script PHP (par exemple, `http://example.com/page.php?id=123`),
//PHP analyse l'URL et remplit le tableau `$_GET` avec les paires clé-valeur trouvées dans la chaîne de requête. 
//Accéder à `$_GET['id']` permet d'extraire la valeur associée à la clé `id`, qui est `123` dans cet exemple. */

$id = $_GET['id'];

// Récupérer la réservation avec JOIN sur pilotes et reservation_services
try {
    $sql = "SELECT r.id, r.date_session, r.heure_session, r.statut, r.prix_total,
                   p.nom_complet, p.email, p.telephone
            FROM reservations r
            JOIN pilotes p ON r.id_pilote = p.id
            WHERE r.id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':id' => $id));
    $reservation = $stmt->fetch();
    
    // Si la réservation n'existe pas
    if (!$reservation) {
        header('Location: reserver.php');
        exit();
    }
    
    // Récupérer les services de cette réservation
    $sqlServices = "SELECT s.nom_service, s.categorie, s.prix, rs.quantite
                    FROM reservation_services rs
                    JOIN services s ON rs.id_service = s.id
                    WHERE rs.id_reservation = :id";
    
    $stmtServices = $pdo->prepare($sqlServices);
    $stmtServices->execute(array(':id' => $id));
    $services = $stmtServices->fetchAll();
    
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit();
}

// Labels pour le statut
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
    <title>Confirmation — Apex Kart</title>
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
                <span class="label-section" style="margin-bottom: 14px;">Confirmation</span>
                <h1>RÉSERVATION<br><span>CONFIRMÉE ✅</span></h1>
                <p>Merci <?php echo $reservation['nom_complet']; ?> ! Votre réservation a été enregistrée avec succès.</p>
            </div>
        </div>

        <section class="section-fond-2">
            <div style="max-width: 700px; margin: 0 auto;">

                <!-- Détails de la réservation -->
                <div class="formulaire-bloc">
                    <span class="label-section">Détails de votre réservation</span>
                    <h2 class="titre-section" style="margin-top: 1rem; margin-bottom: 2rem;">RÉCAPITULATIF</h2>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                        <div>
                            <strong style="color: var(--vert-neon);">Nom :</strong><br>
                            <?php echo $reservation['nom_complet']; ?>
                        </div>
                        <div>
                            <strong style="color: var(--vert-neon);">Email :</strong><br>
                            <?php echo $reservation['email']; ?>
                        </div>
                        <div>
                            <strong style="color: var(--vert-neon);">Téléphone :</strong><br>
                            <?php echo $reservation['telephone']; ?>
                        </div>
                        <div>
                            <strong style="color: var(--vert-neon);">Date :</strong><br>
                            <?php echo $reservation['date_session']; ?>
                        </div>
                        <div>
                            <strong style="color: var(--vert-neon);">Créneau :</strong><br>
                            <?php echo $reservation['heure_session']; ?>
                        </div>
                        <div>
                            <strong style="color: var(--vert-neon);">Statut :</strong><br>
                            <span id="statut-label" style="color: <?php echo $couleursStatut[$reservation['statut']]; ?>">
                                <?php echo $labelsStatut[$reservation['statut']]; ?>
                            </span>
                        </div>
                    </div>

                    <!-- Services de la réservation -->
                    <div style="margin-bottom: 2rem;">
                        <strong style="color: var(--vert-neon);">Services inclus :</strong><br><br>
                        <?php
                        // Parcourir les services avec fetch() dans un loop
                        for ($i = 0; $i < count($services); $i++) {
                            $catLabel = '';
                            if ($services[$i]['categorie'] === 'session') {
                                $catLabel = '🏁 ';
                            } else {
                                $catLabel = '🎯 ';
                            }
                            echo $catLabel . $services[$i]['nom_service'];
                            if ($services[$i]['quantite'] > 1) {
                                echo ' (' . $services[$i]['quantite'] . 'Personne(s))';
                            }
                            echo ' — <span style="color: var(--vert-neon);">' . $services[$i]['prix'] . ' DT</span><br>';
                        }
                        ?>
                    </div>

                    <!-- Prix total -->
                    <div style="background: rgba(0,255,136,0.1); border: 2px solid var(--vert-neon); padding: 1.5rem; border-radius: 8px; text-align: center; margin-bottom: 2rem;">
                        <div style="color: var(--vert-neon); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">PRIX TOTAL</div>
                        <div style="color: var(--blanc); font-size: 2.5rem; font-family: Impact, sans-serif;"><?php echo $reservation['prix_total']; ?> DT</div>
                    </div>

                    <!-- Numéro de réservation -->
                    <div style="text-align: center; margin-bottom: 2rem;">
                        <strong style="color: var(--gris);">Numéro de réservation :</strong>
                        <span style="color: var(--vert-neon); font-size: 1.2rem; font-weight: 700;"> #<?php echo $reservation['id']; ?></span>
                    </div>

                </div>

                <!-- Boutons d'action -->
                <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 2rem; flex-wrap: wrap;">
                    <a href="mes_reservations.php?email=<?php echo urlencode($reservation['email']); ?>" class="btn">
                                                               <!--encode l'adresse e-mail stockée dans le tableau $reservation-->
                        GÉRER MA RÉSERVATION ▶
                    </a>
                    <a href="index.html" class="btn btn-outline">
                        RETOUR À L'ACCUEIL
                    </a>
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
    // Auto-confirmer la réservation après 2 secondes
    setTimeout(function() {
        fetch('auto_confirmer.php?id=<?php echo $reservation['id']; ?>')//appelle auto_confirmer.php
            .then(function(response) { return response.text(); })
            .then(function(text) {
                if (text.trim() === 'OK') { //text.trim() is a built-in JavaScript string method that removes whitespace from both ends of a string
                    var statutElement = document.getElementById('statut-label');
                    if (statutElement) {
                        statutElement.style.color = 'var(--vert-neon)';
                        statutElement.textContent = 'Confirmée ✅';
                    }
                }
            });
    }, 2000);
    </script>
</body>
</html>
