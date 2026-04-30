// ==================== SCRIPT.JS - APEX KART ====================
// JavaScript vanilla pour la validation des formulaires et les animations
// Sans framework - Concepts de base uniquement

// ==================== VARIABLES GLOBALES ====================
var prixSessions = {
  enfants: 25,
  adultes: 40,
  groupe: 350
};

var prixServices = {
  video: 15,
  forfait: 170,
  coaching: 50,
  catering: 0,
  anniv: 350,
  event_entreprise: 800,
  location: 500
};

var labelsServices = {
  video: 'Enregistrement vidéo',
  forfait: 'Forfait Multi-Courses',
  coaching: 'Coaching de course',
  catering: 'Service de restauration',
  anniv: 'Anniversaire',
  event_entreprise: 'Événement d\'Entreprise',
  location: 'Location Piste Privée'
};

// ==================== FONCTIONS UTILITAIRES ====================

// Fonction pour valider un email
function validerEmail(email) {
  var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
}

// Fonction pour valider un numéro de téléphone
function validerTelephone(tel) {
  var regex = /^[\+]?[0-9\s\-]{8,}$/;
  return regex.test(tel);
}

// Fonction pour afficher un message d'erreur
function afficherErreur(element, message) {
  if (!element) return;
  
  element.style.border = '2px solid #ff3366';
  element.style.backgroundColor = 'rgba(255, 51, 102, 0.05)';

  var erreurExistante = element.parentNode.querySelector('.message-erreur');
  if (erreurExistante) {
    erreurExistante.remove();
  }

  var messageErreur = document.createElement('div');
  messageErreur.className = 'message-erreur';
  messageErreur.style.color = '#ff3366';
  messageErreur.style.fontSize = '0.85rem';
  messageErreur.style.marginTop = '5px';
  messageErreur.style.fontWeight = '600';
  messageErreur.textContent = '⚠ ' + message;

  element.parentNode.appendChild(messageErreur);
}

// Fonction pour effacer les erreurs
function effacerErreur(element) {
  if (!element) return;
  
  element.style.border = '';
  element.style.backgroundColor = '';
  var erreur = element.parentNode.querySelector('.message-erreur');
  if (erreur) {
    erreur.remove();
  }
}

// Fonction pour créer un popup personnalisé
function creerPopup(titre, contenu, boutons) {
  // Créer l'overlay (fond sombre)
  var overlay = document.createElement('div');
  overlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); z-index: 9999; display: flex; align-items: center; justify-content: center; animation: fadeIn 0.3s ease;';
  
  // Créer le popup
  var popup = document.createElement('div');
  popup.style.cssText = 'background: #1a1a1a; border: 2px solid #00ff88; border-radius: 12px; padding: 2rem; max-width: 500px; width: 90%; box-shadow: 0 20px 60px rgba(0, 255, 136, 0.3); animation: slideIn 0.3s ease;';
  
  // Titre
  var titreElement = document.createElement('h3');
  titreElement.style.cssText = 'color: #00ff88; font-size: 1.5rem; margin-bottom: 1.5rem; text-align: center; font-family: Impact, sans-serif; text-transform: uppercase; letter-spacing: 1px;';
  titreElement.textContent = titre;
  popup.appendChild(titreElement);
  
  // Contenu
  var contenuElement = document.createElement('div');
  contenuElement.style.cssText = 'color: #fff; margin-bottom: 2rem; line-height: 1.8;';
  contenuElement.innerHTML = contenu;
  popup.appendChild(contenuElement);
  
  // Boutons
  var boutonsContainer = document.createElement('div');
  boutonsContainer.style.cssText = 'display: flex; gap: 1rem; justify-content: center;';
  
  for (var i = 0; i < boutons.length; i++) {
    var btn = document.createElement('button');
    btn.textContent = boutons[i].texte;
    btn.style.cssText = 'padding: 0.8rem 2rem; font-size: 1rem; font-weight: 700; border: none; border-radius: 6px; cursor: pointer; transition: all 0.3s ease; text-transform: uppercase; letter-spacing: 1px;';
    
    if (boutons[i].principal) {
      btn.style.background = '#00ff88';
      btn.style.color = '#000';
      btn.onmouseover = function() {
        this.style.background = '#00dd77';
        this.style.transform = 'translateY(-2px)';
      };
      btn.onmouseout = function() {
        this.style.background = '#00ff88';
        this.style.transform = 'translateY(0)';
      };
    } else {
      btn.style.background = 'transparent';
      btn.style.color = '#fff';
      btn.style.border = '2px solid #555';
      btn.onmouseover = function() {
        this.style.borderColor = '#00ff88';
        this.style.color = '#00ff88';
      };
      btn.onmouseout = function() {
        this.style.borderColor = '#555';
        this.style.color = '#fff';
      };
    }
    
    btn.onclick = boutons[i].action;
    boutonsContainer.appendChild(btn);
  }
  
  popup.appendChild(boutonsContainer);
  overlay.appendChild(popup);
  
  // Ajouter les animations CSS
  var style = document.createElement('style');
  style.textContent = '@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } } @keyframes slideIn { from { transform: translateY(-50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }';
  document.head.appendChild(style);
  
  document.body.appendChild(overlay);
  
  return overlay;
}

// Fonction pour fermer un popup
function fermerPopup(popup) {
  popup.style.animation = 'fadeOut 0.3s ease';
  setTimeout(function() {
    if (popup.parentNode) {
      popup.parentNode.removeChild(popup);
    }
  }, 300);
}

// ==================== NOUVEAUTÉ 1: CHARACTER COUNTER ====================

function ajouterCompteurCaracteres(textarea) {
  if (!textarea) return;

  // Guard: remove any counter already inserted (prevents duplicates on double init)
  var existant = textarea.parentNode.querySelector('.compteur-caracteres');
  if (existant) {
    existant.parentNode.removeChild(existant);
  }

  // Enforce the hard limit at the HTML level
  textarea.setAttribute('maxlength', '500');

  // Créer le compteur
  var compteur = document.createElement('div');
  compteur.className = 'compteur-caracteres';
  compteur.style.cssText = 'text-align: right; margin-top: 5px; font-size: 0.85rem; color: #888;';

  // Fonction pour mettre à jour le compteur
  function mettreAJourCompteur() {
    // Truncate strictly — never allow more than 500 characters
    if (textarea.value.length > 500) {
      textarea.value = textarea.value.substring(0, 500);
    }

    var restants = 500 - textarea.value.length;
    var couleur;

    if (restants === 0) {
      couleur = '#ff3366';
    } else if (restants < 50) {
      couleur = '#ff9900';
    } else {
      couleur = '#00ff88';
    }

    compteur.style.color = couleur;
    compteur.textContent = restants + ' / 500 caractères restants';
  }

  // Insérer après le textarea
  textarea.parentNode.insertBefore(compteur, textarea.nextSibling);

  // Événements
  textarea.oninput = mettreAJourCompteur;
  textarea.onkeyup = mettreAJourCompteur;

  // Initialisation
  mettreAJourCompteur();

  console.log('Compteur de caractères ajouté');
}

// ==================== NOUVEAUTÉ 2: AUTO-SAVE ====================

// Fonction pour sauvegarder un formulaire
function sauvegarderFormulaire(formulaire, nomFormulaire) {
  if (!formulaire || !window.localStorage) return;
  
  var elements = formulaire.elements;
  for (var i = 0; i < elements.length; i++) {
    var element = elements[i];
    var cle = nomFormulaire + '_' + element.name;
    
    if (element.type === 'checkbox' || element.type === 'radio') {
      if (element.checked) {
        localStorage.setItem(cle, 'checked');
      } else {
        localStorage.removeItem(cle);
      }
    } else if (element.type !== 'submit' && element.name) {
      localStorage.setItem(cle, element.value);
    }
  }
  
  console.log('Formulaire sauvegardé:', nomFormulaire);
}

// Fonction pour restaurer un formulaire
function restaurerFormulaire(formulaire, nomFormulaire) {
  if (!formulaire || !window.localStorage) return;
  
  var elements = formulaire.elements;
  var restaure = false;
  
  for (var i = 0; i < elements.length; i++) {
    var element = elements[i];
    var cle = nomFormulaire + '_' + element.name;
    var valeur = localStorage.getItem(cle);
    
    if (valeur) {
      if (element.type === 'checkbox' || element.type === 'radio') {
        if (valeur === 'checked') {
          element.checked = true;
          restaure = true;
        }
      } else if (element.type !== 'submit') {
        element.value = valeur;
        restaure = true;
      }
    }
  }
  
  if (restaure) {
    console.log('Formulaire restauré:', nomFormulaire);
  }
}

// Fonction pour effacer la sauvegarde
function effacerSauvegarde(nomFormulaire) {
  if (!window.localStorage) return;
  
  var cles = [];
  for (var i = 0; i < localStorage.length; i++) {
    var cle = localStorage.key(i);
    if (cle.indexOf(nomFormulaire + '_') === 0) {
      cles.push(cle);
    }
  }
  
  for (var j = 0; j < cles.length; j++) {
    localStorage.removeItem(cles[j]);
  }
  
  console.log('Sauvegarde effacée:', nomFormulaire);
}

// ==================== NOUVEAUTÉ 3: VALIDATION DATE ====================

function configurerValidationDate() {
  var champDate = document.getElementById('date');
  if (!champDate) return;
  
  // Obtenir la date d'aujourd'hui au format YYYY-MM-DD
  var aujourdhui = new Date();
  var annee = aujourdhui.getFullYear();
  var mois = aujourdhui.getMonth() + 1;
  var jour = aujourdhui.getDate();
  
  // Formater avec des zéros
  if (mois < 10) mois = '0' + mois;
  if (jour < 10) jour = '0' + jour;
  
  var dateMin = annee + '-' + mois + '-' + jour;
  
  // Définir la date minimale
  champDate.setAttribute('min', dateMin);
  
  console.log('Validation de date configurée. Date min:', dateMin);
}

// ==================== NOUVEAUTÉ 4: ANIMATED COUNTERS ====================

function animerCompteur(element) {
  var cible = parseInt(element.getAttribute('data-count'));
  var suffixe = element.getAttribute('data-suffix');
  if (!suffixe) suffixe = '';
  var duree = 800; // 0.8 secondes — plus rapide
  var increment = cible / (duree / 16); // 60 FPS
  var valeurActuelle = 0;

  element.textContent = '0' + suffixe;

  var intervalle = setInterval(function() {
    valeurActuelle = valeurActuelle + increment;

    if (valeurActuelle >= cible) {
      valeurActuelle = cible;
      clearInterval(intervalle);
    }

    element.textContent = Math.floor(valeurActuelle) + suffixe;
  }, 16);
}

function initialiserCompteursAnimes() {
  var compteurs = document.querySelectorAll('.stat-number[data-count]');
  // No early return here — the scroll handler must be registered on every page
  // so that animerAuScroll() fires for .carte / .galerie-item / .media-bloc elements.

  var compteursAnimes = [];

  // Fonction pour vérifier si un élément est visible
  function estVisible(element) {
    var rect = element.getBoundingClientRect();
    var hauteurFenetre = window.innerHeight;
    return rect.top < hauteurFenetre - 100;
  }

  // Fonction pour vérifier et animer les compteurs
  function verifierEtAnimer() {
    // Guard: only run counter logic when there are counters on this page
    if (compteurs.length === 0) return;

    for (var i = 0; i < compteurs.length; i++) {
      var compteur = compteurs[i];
      var dejaAnime = false;
      
      // Vérifier si déjà animé
      for (var j = 0; j < compteursAnimes.length; j++) {
        if (compteursAnimes[j] === compteur) {
          dejaAnime = true;
          break;
        }
      }
      
      if (!dejaAnime && estVisible(compteur)) {
        animerCompteur(compteur);
        compteursAnimes.push(compteur);
        console.log('Compteur animé:', compteur.getAttribute('data-count'));
      }
    }
  }
  
  // Vérifier au scroll
  window.onscroll = function() {
    verifierEtAnimer();
    
    // Autres animations au scroll
    animerAuScroll();
    
    // Bouton scroll-to-top
    var boutonScroll = document.querySelector('.scroll-top');
    if (boutonScroll) {
      if (window.pageYOffset > 300) {
        boutonScroll.style.display = 'flex';
        boutonScroll.style.opacity = '1';
      } else {
        boutonScroll.style.opacity = '0';
        setTimeout(function() {
          if (window.pageYOffset <= 300) {
            boutonScroll.style.display = 'none';
          }
        }, 300);
      }
    }
  };
  
  // Vérifier au chargement
  verifierEtAnimer();
  
  console.log('Compteurs animés initialisés:', compteurs.length);
}

// ==================== FORMULAIRE DE CONTACT ====================

function initialiserFormulaireContact() {
  console.log('Initialisation formulaire contact...');
  
  var champPrenom = document.getElementById('prenom');
  if (!champPrenom) {
    console.log('Pas de champ prénom - pas sur la page contact');
    return;
  }

  var formulaire = document.querySelector('form.formulaire-bloc');
  if (!formulaire) {
    console.log('Formulaire non trouvé');
    return;
  }

  console.log('Formulaire contact détecté!');
  
  // Restaurer la sauvegarde
  restaurerFormulaire(formulaire, 'contact');
  
  // Ajouter compteur de caractères au message
  var champMessage = document.getElementById('message');
  ajouterCompteurCaracteres(champMessage);
  
  // Auto-save toutes les 3 secondes
  setInterval(function() {
    sauvegarderFormulaire(formulaire, 'contact');
  }, 3000);

  // Événement onSubmit
  formulaire.onsubmit = function(event) {
    event.preventDefault();
    console.log('Soumission du formulaire contact');

    var valide = true;
    var prenom = document.getElementById('prenom');
    var nom = document.getElementById('nom');
    var email = document.getElementById('email');
    var telephone = document.getElementById('telephone');
    var message = document.getElementById('message');
    var rgpd = document.querySelector('input[name="rgpd"]');

    console.log('Validation en cours...');

    if (!prenom || prenom.value.trim() === '') {
      afficherErreur(prenom, 'Le prénom est obligatoire');
      valide = false;
    } else {
      effacerErreur(prenom);
    }

    if (!nom || nom.value.trim() === '') {
      afficherErreur(nom, 'Le nom est obligatoire');
      valide = false;
    } else {
      effacerErreur(nom);
    }

    if (!email || email.value.trim() === '') {
      afficherErreur(email, 'L\'adresse email est obligatoire');
      valide = false;
    } else if (!validerEmail(email.value)) {
      afficherErreur(email, 'L\'adresse email n\'est pas valide');
      valide = false;
    } else {
      effacerErreur(email);
    }

    if (telephone && telephone.value.trim() !== '' && !validerTelephone(telephone.value)) {
      afficherErreur(telephone, 'Le numéro de téléphone n\'est pas valide');
      valide = false;
    } else if (telephone) {
      effacerErreur(telephone);
    }

    if (!message || message.value.trim() === '') {
      afficherErreur(message, 'Le message est obligatoire');
      valide = false;
    } else if (message.value.trim().length < 10) {
      afficherErreur(message, 'Le message doit contenir au moins 10 caractères');
      valide = false;
    } else {
      effacerErreur(message);
    }

    if (!rgpd || !rgpd.checked) {
      afficherErreur(rgpd, 'Vous devez accepter la politique de confidentialité');
      valide = false;
    } else {
      effacerErreur(rgpd);
    }

    console.log('Validation terminée. Valide:', valide);

    if (valide) {
      var sujet = document.getElementById('sujet');
      var confirmation = window.confirm(
        'Êtes-vous sûr de vouloir envoyer ce message ?\n\n' +
        'Nom : ' + prenom.value + ' ' + nom.value + '\n' +
        'Email : ' + email.value + '\n' +
        'Sujet : ' + (sujet && sujet.value ? sujet.value : 'Non spécifié')
      );

      if (confirmation) {
        alert('Merci ' + prenom.value + ' ! Votre message a été envoyé avec succès.\nNous vous répondrons dans les 24 heures.');
        effacerSauvegarde('contact');
        formulaire.reset();
      }
    } else {
      alert('⚠ Veuillez corriger les erreurs dans le formulaire avant de soumettre.');
    }

    return false;
  };

  var elements = formulaire.elements;
  for (var i = 0; i < elements.length; i++) {
    elements[i].onchange = function() {
      effacerErreur(this);
    };
    elements[i].oninput = function() {
      effacerErreur(this);
    };
  }

  console.log('Formulaire contact initialisé avec succès');
}

// ==================== FORMULAIRE DE RÉSERVATION ====================

function initialiserFormulaireReservation() {
  console.log('Initialisation formulaire réservation...');
  
  var champParticipants = document.getElementById('participants');
  if (!champParticipants) {
    console.log('Pas de champ participants - pas sur la page réservation');
    return;
  }

  var formulaire = document.querySelector('form.formulaire-bloc');
  if (!formulaire) {
    console.log('Formulaire non trouvé');
    return;
  }

  console.log('Formulaire réservation détecté!');
  
  // Configurer la validation de date
  configurerValidationDate();
  
  // Restaurer la sauvegarde
  restaurerFormulaire(formulaire, 'reservation');
  
  // Ajouter compteur de caractères au message
  var champMessage = document.getElementById('message');
  ajouterCompteurCaracteres(champMessage);
  
  // Auto-save toutes les 3 secondes
  setInterval(function() {
    sauvegarderFormulaire(formulaire, 'reservation');
  }, 3000);

  function calculerPrixTotal() {
    var prixTotal = 0;
    var sessionSelectionnee = document.querySelector('input[name="session"]:checked');
    if (sessionSelectionnee) {
      prixTotal = prixTotal + prixSessions[sessionSelectionnee.value];
    }

    var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    for (var i = 0; i < checkboxes.length; i++) {
      var checkbox = checkboxes[i];
      if (checkbox.checked && checkbox.name !== 'conditions') {
        var nomService = checkbox.name;
        if (prixServices[nomService]) {
          prixTotal = prixTotal + prixServices[nomService];
        }
      }
    }

    var participants = document.getElementById('participants');
    if (participants && participants.value > 1 && sessionSelectionnee && sessionSelectionnee.value !== 'groupe') {
      prixTotal = prixTotal * parseInt(participants.value);
    }

    return prixTotal;
  }

  function afficherPrixTotal() {
    var prixTotal = calculerPrixTotal();
    var affichagePrix = document.getElementById('affichage-prix-total');

    if (!affichagePrix) {
      affichagePrix = document.createElement('div');
      affichagePrix.id = 'affichage-prix-total';
      affichagePrix.style.cssText = 'background: rgba(0,255,136,0.1); border: 2px solid #00ff88; padding: 1.5rem; margin-top: 1.5rem; margin-bottom: 1.5rem; border-radius: 8px; text-align: center; animation: pulse 2s infinite;';

      var boutonSubmit = formulaire.querySelector('button[type="submit"]');
      if (boutonSubmit) {
        formulaire.insertBefore(affichagePrix, boutonSubmit);
      } else {
        formulaire.appendChild(affichagePrix);
      }
    }

    affichagePrix.innerHTML = 
      '<div style="color: #00ff88; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">💰 Prix Total Estimé</div>' +
      '<div style="color: #fff; font-size: 2.5rem; font-weight: 700; font-family: Impact, sans-serif;">' + prixTotal + ' DT</div>' +
      '<div style="color: #888; font-size: 0.8rem; margin-top: 0.5rem;">Prix mis à jour en temps réel</div>';

    console.log('Prix total calculé:', prixTotal, 'DT');
  }

  var radios = document.querySelectorAll('input[name="session"]');
  for (var i = 0; i < radios.length; i++) {
    radios[i].onclick = function() {
      console.log('Session changée');
      afficherPrixTotal();
    };
  }

  var checkboxes = document.querySelectorAll('input[type="checkbox"]');
  for (var j = 0; j < checkboxes.length; j++) {
    checkboxes[j].onclick = function() {
      console.log('Service changé');
      afficherPrixTotal();
    };
  }

  var participants = document.getElementById('participants');
  if (participants) {
    participants.onchange = function() {
      console.log('Nombre de participants changé');
      afficherPrixTotal();
    };
    participants.oninput = function() {
      afficherPrixTotal();
    };
  }

  afficherPrixTotal();

  formulaire.onsubmit = function(event) {
    event.preventDefault();
    console.log('Soumission du formulaire réservation');

    var valide = true;
    var nom = document.getElementById('nom');
    var email = document.getElementById('email');
    var telephone = document.getElementById('telephone');
    var date = document.getElementById('date');
    var creneau = document.getElementById('creneau');
    var participantsInput = document.getElementById('participants');
    var conditions = document.querySelector('input[name="conditions"]');

    if (!nom || nom.value.trim() === '') {
      afficherErreur(nom, 'Le nom complet est obligatoire');
      valide = false;
    } else {
      effacerErreur(nom);
    }

    if (!email || email.value.trim() === '') {
      afficherErreur(email, 'L\'adresse email est obligatoire');
      valide = false;
    } else if (!validerEmail(email.value)) {
      afficherErreur(email, 'L\'adresse email n\'est pas valide');
      valide = false;
    } else {
      effacerErreur(email);
    }

    if (!telephone || telephone.value.trim() === '') {
      afficherErreur(telephone, 'Le numéro de téléphone est obligatoire');
      valide = false;
    } else if (!validerTelephone(telephone.value)) {
      afficherErreur(telephone, 'Le numéro de téléphone n\'est pas valide');
      valide = false;
    } else {
      effacerErreur(telephone);
    }

    if (!date || date.value === '') {
      afficherErreur(date, 'La date est obligatoire');
      valide = false;
    } else {
      var dateSelectionnee = new Date(date.value);
      var dateAujourdhui = new Date();
      dateAujourdhui.setHours(0, 0, 0, 0);

      if (dateSelectionnee < dateAujourdhui) {
        afficherErreur(date, 'La date ne peut pas être dans le passé');
        valide = false;
      } else {
        effacerErreur(date);
      }
    }

    if (!creneau || creneau.value === '') {
      afficherErreur(creneau, 'Veuillez choisir un créneau horaire');
      valide = false;
    } else {
      effacerErreur(creneau);
    }

    if (!participantsInput || participantsInput.value === '' || participantsInput.value < 1) {
      afficherErreur(participantsInput, 'Le nombre de participants doit être au moins 1');
      valide = false;
    } else if (participantsInput.value > 20) {
      afficherErreur(participantsInput, 'Le nombre maximum de participants est 20');
      valide = false;
    } else {
      effacerErreur(participantsInput);
    }

    if (!conditions || !conditions.checked) {
      afficherErreur(conditions, 'Vous devez accepter les conditions générales');
      valide = false;
    } else {
      effacerErreur(conditions);
    }

    console.log('Validation terminée. Valide:', valide);

    if (valide) {
      var sessionChoisie = document.querySelector('input[name="session"]:checked');
      var nomSession = sessionChoisie ? sessionChoisie.value.toUpperCase() : 'NON SPÉCIFIÉE';
      var prixTotal = calculerPrixTotal();

      var servicesSelectionnes = [];
      var checkboxesServices = document.querySelectorAll('input[type="checkbox"]:checked');
      for (var i = 0; i < checkboxesServices.length; i++) {
        var cb = checkboxesServices[i];
        if (cb.name !== 'conditions' && labelsServices[cb.name]) {
          servicesSelectionnes.push({
            nom: labelsServices[cb.name],
            prix: prixServices[cb.name]
          });
        }
      }

      var contenuVerification = '<div style="background: rgba(0, 0, 0, 0.3); padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem;">';
      contenuVerification += '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">';
      contenuVerification += '<div><strong style="color: #00ff88;">👤 Nom:</strong><br><span style="color: #ddd;">' + nom.value + '</span></div>';
      contenuVerification += '<div><strong style="color: #00ff88;">📧 Email:</strong><br><span style="color: #ddd;">' + email.value + '</span></div>';
      contenuVerification += '<div><strong style="color: #00ff88;">📅 Date:</strong><br><span style="color: #ddd;">' + date.value + '</span></div>';
      contenuVerification += '<div><strong style="color: #00ff88;">⏰ Heure:</strong><br><span style="color: #ddd;">' + creneau.value + '</span></div>';
      contenuVerification += '<div><strong style="color: #00ff88;">🏁 Session:</strong><br><span style="color: #ddd;">' + nomSession + '</span></div>';
      contenuVerification += '<div><strong style="color: #00ff88;">👥 Participants:</strong><br><span style="color: #ddd;">' + participantsInput.value + ' personne(s)</span></div>';
      contenuVerification += '</div>';
      
      if (servicesSelectionnes.length > 0) {
        contenuVerification += '<div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #333;">';
        contenuVerification += '<strong style="color: #00ff88; display: block; margin-bottom: 0.5rem;">🎯 Services Supplémentaires:</strong>';
        contenuVerification += '<ul style="list-style: none; padding: 0; margin: 0;">';
        for (var k = 0; k < servicesSelectionnes.length; k++) {
          contenuVerification += '<li style="padding: 0.3rem 0; color: #ddd;">✓ ' + servicesSelectionnes[k].nom + ' <span style="color: #00ff88;">(+' + servicesSelectionnes[k].prix + ' DT)</span></li>';
        }
        contenuVerification += '</ul></div>';
      }
      
      contenuVerification += '</div>';
      contenuVerification += '<div style="background: rgba(0, 255, 136, 0.1); border: 2px solid #00ff88; padding: 1rem; border-radius: 8px; text-align: center;">';
      contenuVerification += '<div style="font-size: 0.9rem; color: #00ff88; margin-bottom: 0.3rem;">PRIX TOTAL</div>';
      contenuVerification += '<div style="font-size: 2.5rem; font-weight: 700; font-family: Impact, sans-serif; color: #fff;">' + prixTotal + ' DT</div>';
      contenuVerification += '</div>';
      contenuVerification += '<p style="text-align: center; color: #888; margin-top: 1rem; font-size: 0.9rem;">⚠ Veuillez vérifier attentivement vos informations avant de confirmer</p>';

      var popupVerification = creerPopup(
        '📋 Vérification de votre réservation',
        contenuVerification,
        [
          {
            texte: 'Annuler',
            principal: false,
            action: function() {
              fermerPopup(popupVerification);
            }
          },
          {
            texte: '✓ Vérifier et Confirmer',
            principal: true,
            action: function() {
              fermerPopup(popupVerification);
              
              setTimeout(function() {
                var contenuMerci = '<div style="text-align: center;">';
                contenuMerci += '<div style="font-size: 4rem; margin-bottom: 1rem;">🏁</div>';
                contenuMerci += '<p style="font-size: 1.2rem; color: #ddd; line-height: 1.8; margin-bottom: 1rem;">Merci <strong style="color: #00ff88;">' + nom.value + '</strong> pour votre réservation !</p>';
                contenuMerci += '<p style="color: #999; line-height: 1.8;">Nous vous contacterons dans les <strong style="color: #00ff88;">24 heures</strong> pour confirmer tous les détails de votre session.</p>';
                contenuMerci += '<div style="background: rgba(0, 255, 136, 0.1); padding: 1rem; border-radius: 8px; margin-top: 1.5rem; border-left: 4px solid #00ff88;">';
                contenuMerci += '<p style="color: #00ff88; font-weight: 700; margin: 0;">🏎️ Préparez-vous à vivre une expérience inoubliable sur notre piste !</p>';
                contenuMerci += '</div>';
                contenuMerci += '<p style="color: #666; font-size: 0.85rem; margin-top: 1.5rem;">Un email de confirmation vous a été envoyé à <strong style="color: #00ff88;">' + email.value + '</strong></p>';
                contenuMerci += '</div>';

                var popupMerci = creerPopup(
                  '✅ Réservation Confirmée',
                  contenuMerci,
                  [
                    {
                      texte: 'Fermer',
                      principal: true,
                      action: function() {
                        fermerPopup(popupMerci);
                        effacerSauvegarde('reservation');
                        formulaire.reset();
                        afficherPrixTotal();
                      }
                    }
                  ]
                );
              }, 400);
            }
          }
        ]
      );
    } else {
      alert('⚠ Veuillez corriger les erreurs dans le formulaire avant de soumettre.');
    }

    return false;
  };

  var elements = formulaire.elements;
  for (var i = 0; i < elements.length; i++) {
    elements[i].onchange = function() {
      effacerErreur(this);
    };
    elements[i].oninput = function() {
      effacerErreur(this);
    };
  }

  console.log('Formulaire réservation initialisé avec succès');
}

// ==================== ANIMATIONS ====================

function animerAuScroll() {
  var elements = document.querySelectorAll('.carte, .galerie-item, .media-bloc');

  for (var i = 0; i < elements.length; i++) {
    var element = elements[i];
    var position = element.getBoundingClientRect();
    var hauteurFenetre = window.innerHeight;

    if (position.top < hauteurFenetre - 100) {
      element.style.opacity = '1';
      element.style.transform = 'translateY(0)';
    }
  }
}

function animerLogo() {
  var logo = document.querySelector('.logo');
  if (!logo) return;

  var logoPoint = logo.querySelector('.logo-point');

  logo.onmouseover = function() {
    if (logoPoint) {
      logoPoint.style.transform = 'scale(1.5) rotate(360deg)';
      logoPoint.style.transition = 'transform 0.6s ease';
    }
    this.style.transform = 'scale(1.05)';
  };

  logo.onmouseout = function() {
    if (logoPoint) {
      logoPoint.style.transform = 'scale(1) rotate(0deg)';
    }
    this.style.transform = 'scale(1)';
  };
}

function animerBoutons() {
  var boutons = document.querySelectorAll('.btn, .btn-blanc, .btn-outline, .btn-rouge, .nav-cta');

  for (var i = 0; i < boutons.length; i++) {
    boutons[i].style.transition = 'all 0.3s ease';
    
    boutons[i].onmouseover = function() {
      this.style.transform = 'translateY(-3px) scale(1.02)';
      this.style.boxShadow = '0 5px 20px rgba(0, 255, 136, 0.3)';
    };

    boutons[i].onmouseout = function() {
      this.style.transform = 'translateY(0) scale(1)';
      this.style.boxShadow = 'none';
    };
  }
}

function animerScrollTop() {
  var boutonScroll = document.querySelector('.scroll-top');
  if (!boutonScroll) return;

  boutonScroll.style.transition = 'all 0.3s ease';

  boutonScroll.onclick = function(e) {
    e.preventDefault();
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  };
}

function animerCartes() {
  var cartes = document.querySelectorAll('.carte');

  for (var i = 0; i < cartes.length; i++) {
    cartes[i].style.transition = 'all 0.4s ease';

    cartes[i].onmouseover = function() {
      this.style.transform = 'translateY(-10px) scale(1.03)';
      this.style.boxShadow = '0 15px 40px rgba(0, 255, 136, 0.3)';
    };

    cartes[i].onmouseout = function() {
      this.style.transform = 'translateY(0) scale(1)';
      this.style.boxShadow = 'none';
    };
  }
}

function animerGalerie() {
  var items = document.querySelectorAll('.galerie-item');

  for (var i = 0; i < items.length; i++) {
    items[i].style.transition = 'all 0.3s ease';

    items[i].onmouseover = function() {
      this.style.transform = 'scale(1.05)';
      this.style.zIndex = '10';
    };

    items[i].onmouseout = function() {
      this.style.transform = 'scale(1)';
      this.style.zIndex = '1';
    };
  }
}

function animerNavigation() {
  var liens = document.querySelectorAll('nav a:not(.nav-cta)');
  
  for (var i = 0; i < liens.length; i++) {
    liens[i].style.transition = 'all 0.2s ease';
    
    liens[i].onmouseover = function() {
      this.style.color = '#00ff88';
      this.style.transform = 'translateY(-2px)';
    };
    
    liens[i].onmouseout = function() {
      if (!this.classList.contains('actif')) {
        this.style.color = '';
      }
      this.style.transform = 'translateY(0)';
    };
  }
}

function initialiserStylesAnimation() {
  var elements = document.querySelectorAll('.carte, .galerie-item, .media-bloc');

  for (var i = 0; i < elements.length; i++) {
    elements[i].style.opacity = '0';
    elements[i].style.transform = 'translateY(30px)';
    elements[i].style.transition = 'opacity 0.8s ease, transform 0.8s ease';
  }
}

// ==================== INITIALISATION ====================

function initialiser() {
  console.log('=================================');
  console.log('APEX KART - Initialisation...');
  console.log('=================================');
  
  // Formulaires
  initialiserFormulaireContact();
  initialiserFormulaireReservation();
  
  // Compteurs animés (NOUVEAUTÉ 4)
  initialiserCompteursAnimes();

  // Animations
  initialiserStylesAnimation();
  animerLogo();
  animerBoutons();
  animerScrollTop();
  animerCartes();
  animerGalerie();
  animerNavigation();

  setTimeout(function() {
    animerAuScroll();
  }, 200);

  console.log('Page actuelle:', window.location.pathname);
  console.log('Date:', new Date().toLocaleDateString('fr-FR'));
  console.log('✅ Script initialisé avec succès!');
}

window.onload = function() {
  initialiser();
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initialiser);
} else {
  initialiser();
}
