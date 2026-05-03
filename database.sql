SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


CREATE TABLE `disponibilites_instructeurs` (
  `id` int NOT NULL,
  `id_instructeur` int NOT NULL,
  `jour_semaine` enum('Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche') COLLATE utf8mb4_unicode_ci NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `est_actif` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `disponibilites_instructeurs` (`id`, `id_instructeur`, `jour_semaine`, `heure_debut`, `heure_fin`, `est_actif`) VALUES
(1, 1, 'Lundi', '10:00:00', '14:00:00', 1),
(2, 1, 'Mardi', '10:00:00', '14:00:00', 1),
(3, 1, 'Mercredi', '10:00:00', '14:00:00', 1),
(4, 1, 'Jeudi', '10:00:00', '14:00:00', 1),
(5, 1, 'Vendredi', '10:00:00', '14:00:00', 1),
(6, 1, 'Samedi', '09:00:00', '18:00:00', 1),
(7, 1, 'Dimanche', '09:00:00', '18:00:00', 1),
(8, 2, 'Lundi', '14:00:00', '20:00:00', 1),
(9, 2, 'Mardi', '14:00:00', '20:00:00', 1),
(10, 2, 'Mercredi', '14:00:00', '20:00:00', 1),
(11, 2, 'Jeudi', '14:00:00', '20:00:00', 1),
(12, 2, 'Vendredi', '14:00:00', '20:00:00', 1),
(13, 2, 'Samedi', '09:00:00', '18:00:00', 1),
(14, 2, 'Dimanche', '09:00:00', '18:00:00', 1),
(15, 3, 'Lundi', '10:00:00', '18:00:00', 1),
(16, 3, 'Mardi', '10:00:00', '18:00:00', 1),
(17, 3, 'Mercredi', '10:00:00', '18:00:00', 1),
(18, 3, 'Jeudi', '10:00:00', '18:00:00', 1),
(19, 3, 'Vendredi', '10:00:00', '18:00:00', 1),
(20, 3, 'Samedi', '09:00:00', '22:00:00', 1),
(21, 3, 'Dimanche', '09:00:00', '22:00:00', 1);






CREATE TABLE `instructeurs` (
  `id` int NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `specialite` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `photo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_embauche` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `instructeurs` (`id`, `nom`, `specialite`, `bio`, `photo_url`, `date_embauche`) VALUES
(1, 'Karim Ben Ali', 'Pilotage de précision', 'Ancien pilote professionnel avec 10 ans d\'expérience en compétition.', NULL, '2022-03-15'),
(2, 'Sofia Mansouri', 'Endurance', 'Championne nationale de karting 2023. Spécialiste des courses longues distances.', NULL, '2023-01-10'),
(3, 'Yassine Trabelsi', 'Débutants', 'Moniteur certifié, spécialisé dans l\'accompagnement des nouveaux pilotes.', NULL, '2021-09-01'),
(4, 'Ahmed Gharbi', 'Drift & Contrôle', 'Spécialiste des techniques de drift et maîtrise du kart en glissade.', NULL, '2023-06-15'),
(5, 'Leila Bouaziz', 'Vitesse Pure', 'Ex-pilote championne de Tunisie 2022, elle vous apprendra à optimiser vos trajectoires.', NULL, '2024-02-01'),
(6, 'Sami Khelifi', 'Initiation Enfants', 'Pédagogue patient et certifié pour la sécurité des plus jeunes (8-12 ans).', NULL, '2021-11-20');





CREATE TABLE `karts` (
  `id` int NOT NULL,
  `numero_flotte` int NOT NULL,
  `modele` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('Adulte','Enfant','Biplace') COLLATE utf8mb4_unicode_ci NOT NULL,
  `puissance` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '270cc',
  `statut` enum('Disponible','En piste','Maintenance','Hors service') COLLATE utf8mb4_unicode_ci DEFAULT 'Disponible',
  `derniere_revision` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `karts` (`id`, `numero_flotte`, `modele`, `type`, `puissance`, `statut`, `derniere_revision`) VALUES
(1, 1, 'Sodi Kart SR5', 'Adulte', '270cc', 'Disponible', NULL),
(2, 2, 'Sodi Kart SR5', 'Adulte', '270cc', 'Disponible', NULL),
(3, 3, 'Sodi Kart SR5', 'Adulte', '270cc', 'Disponible', NULL),
(4, 4, 'Sodi Kart SR5', 'Adulte', '270cc', 'Disponible', NULL),
(5, 5, 'Sodi Kart SR5', 'Adulte', '270cc', 'Disponible', NULL),
(6, 6, 'Sodi Kart Junior', 'Enfant', '120cc', 'Disponible', NULL),
(7, 7, 'Sodi Kart Junior', 'Enfant', '120cc', 'Disponible', NULL),
(8, 8, 'Sodi Kart Junior', 'Enfant', '120cc', 'Disponible', NULL),
(9, 9, 'Sodi RSX', 'Adulte', '390cc', 'Disponible', NULL),
(10, 10, 'Sodi RSX', 'Adulte', '390cc', 'Disponible', NULL),
(11, 11, 'Sodi RSX', 'Adulte', '390cc', 'Disponible', NULL),
(12, 12, 'Sodi RSX', 'Adulte', '390cc', 'Disponible', NULL),
(13, 13, 'Sodi RSX', 'Adulte', '390cc', 'Disponible', NULL),
(14, 14, 'Tony Kart Racer', 'Biplace', '200cc', 'Disponible', NULL),
(15, 15, 'Tony Kart Racer', 'Biplace', '200cc', 'Disponible', NULL),
(16, 16, 'Tony Kart Racer', 'Biplace', '200cc', 'Disponible', NULL),
(17, 17, 'Tony Kart Racer', 'Biplace', '200cc', 'Disponible', NULL),
(18, 18, 'Tony Kart Racer', 'Biplace', '200cc', 'Disponible', NULL),
(19, 19, 'Praga R20', 'Enfant', '160cc', 'Disponible', NULL),
(20, 20, 'Praga R20', 'Enfant', '160cc', 'Disponible', NULL),
(21, 21, 'Praga R20', 'Enfant', '160cc', 'Disponible', NULL),
(22, 22, 'Praga R20', 'Enfant', '160cc', 'Disponible', NULL);






CREATE TABLE `messages` (
  `id` int NOT NULL,
  `nom_complet` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sujet` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_envoi` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `messages` (`id`, `nom_complet`, `email`, `telephone`, `sujet`, `message`, `date_envoi`) VALUES
(1, 'youssef barhoumi', 'barhoumiyoussef2004@gmail.com', '+216 71111111', 'groupe', 'ijuhygfdtrsdujedtyuykyrzsjzsrj', '2026-04-30 15:24:18');






CREATE TABLE `performances` (
  `id` int NOT NULL,
  `id_pilote` int NOT NULL,
  `temps_tour` time(3) NOT NULL,
  `date_performance` date NOT NULL,
  `kart_utilise` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `performances` (`id`, `id_pilote`, `temps_tour`, `date_performance`, `kart_utilise`) VALUES
(1, 1, '00:01:14.443', '2025-03-14', 'K7'),
(2, 1, '00:01:14.620', '2025-02-20', 'K3'),
(3, 2, '00:01:14.493', '2025-03-10', 'K12'),
(4, 2, '00:01:15.100', '2025-02-15', 'K5'),
(5, 3, '00:01:14.570', '2025-03-01', 'K3'),
(6, 3, '00:01:15.200', '2025-01-20', 'K7'),
(7, 4, '00:01:15.179', '2025-03-08', 'K15'),
(8, 4, '00:01:16.000', '2025-02-28', 'K1');





CREATE TABLE `pilotes` (
  `id` int NOT NULL,
  `nom_complet` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_inscription` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pilotes` (`id`, `nom_complet`, `email`, `telephone`, `date_inscription`) VALUES
(1, 'MARTIN Lucas', 'lucas@apexkart.tn', '+216 55 111 222', '2026-04-30 14:38:24'),
(2, 'DUBOIS Sara', 'sara@apexkart.tn', '+216 55 333 444', '2026-04-30 14:38:24'),
(3, 'ROSSI Marco', 'marco@apexkart.tn', '+216 55 555 666', '2026-04-30 14:38:24'),
(4, 'BENALI Karim', 'karim@apexkart.tn', '+216 55 777 888', '2026-04-30 14:38:24'),
(5, 'Youssef Barhoumi', 'barhoumiyoussef2004@gmail.com', '+216 71111111', '2026-04-30 15:01:56');






CREATE TABLE `reservations` (
  `id` int NOT NULL,
  `id_pilote` int NOT NULL,
  `date_session` date NOT NULL,
  `heure_session` time NOT NULL,
  `statut` enum('confirme','en_attente','annule') COLLATE utf8mb4_unicode_ci DEFAULT 'en_attente',
  `id_kart` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_instructeur` int DEFAULT NULL,
  `prix_total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `reservations` (`id`, `id_pilote`, `date_session`, `heure_session`, `statut`, `id_kart`, `id_instructeur`, `prix_total`) VALUES
(1, 5, '2026-11-11', '15:00:00', 'en_attente', NULL, NULL, 355.00),
(2, 5, '2026-11-11', '15:00:00', 'en_attente', 'Sodi Kart SR5 (x2), Sodi RSX (x2)', 1, 395.00);






CREATE TABLE `services` (
  `id` int NOT NULL,
  `nom_service` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categorie` enum('session','option') COLLATE utf8mb4_unicode_ci NOT NULL,
  `prix` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `services` (`id`, `nom_service`, `categorie`, `prix`) VALUES
(1, 'Session Enfants', 'session', 25.00),
(2, 'Session Adultes', 'session', 40.00),
(3, 'Forfait Groupe', 'session', 350.00),
(4, 'Enregistrement vidéo', 'option', 15.00),
(5, 'Forfait Multi-Courses', 'option', 170.00),
(6, 'Coaching de course', 'option', 50.00),
(7, 'Anniversaire', 'option', 350.00),
(8, "Événement d\'Entreprise", 'option', 800.00),
(9, 'Location Piste Privée', 'option', 500.00);





CREATE TABLE `reservation_services` (
  `id_reservation` int NOT NULL,
  `id_service` int NOT NULL,
  `quantite` int DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `reservation_services` (`id_reservation`, `id_service`, `quantite`) VALUES
(1, 2, 3),
(1, 4, 1),
(1, 5, 1),
(1, 6, 1),
(2, 2, 4),
(2, 4, 1),
(2, 5, 1),
(2, 6, 1);

-----------------------------------------------------------

ALTER TABLE `disponibilites_instructeurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_instructeur` (`id_instructeur`);


ALTER TABLE `instructeurs`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `karts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_flotte` (`numero_flotte`);


ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `performances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pilote` (`id_pilote`);


ALTER TABLE `pilotes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);


ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pilote` (`id_pilote`);


ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `reservation_services`
  ADD PRIMARY KEY (`id_reservation`,`id_service`),
  ADD KEY `id_service` (`id_service`);



-- AUTO_INCREMENT 

ALTER TABLE `disponibilites_instructeurs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;


ALTER TABLE `instructeurs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;


ALTER TABLE `karts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;


ALTER TABLE `messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


ALTER TABLE `performances`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;


ALTER TABLE `pilotes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;


ALTER TABLE `reservations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;


ALTER TABLE `services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;


-- Contraintes 

ALTER TABLE `disponibilites_instructeurs`
  ADD CONSTRAINT `disponibilites_instructeurs_ibfk_1` FOREIGN KEY (`id_instructeur`) REFERENCES `instructeurs` (`id`) ON DELETE CASCADE;


ALTER TABLE `performances`
  ADD CONSTRAINT `performances_ibfk_1` FOREIGN KEY (`id_pilote`) REFERENCES `pilotes` (`id`);


ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`id_pilote`) REFERENCES `pilotes` (`id`);


ALTER TABLE `reservation_services`
  ADD CONSTRAINT `reservation_services_ibfk_1` FOREIGN KEY (`id_reservation`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_services_ibfk_2` FOREIGN KEY (`id_service`) REFERENCES `services` (`id`);
COMMIT;