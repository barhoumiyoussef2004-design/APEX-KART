//  VARIABLES GLOBALES 
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

//  FONCTIONS UTILITAIRES 

// Fonction pour valider un email
function validerEmail(email) {
  var mail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  //^: Start of the string.
 //[^\s@]+: One or more characters that are neither a whitespace (\s) nor the @ symbol.
  return mail.test(email);
  //.test() method executes a search for a match between a regular expression and a specified string
}

// Fonction pour valider un numéro de téléphone
function validerTelephone(tel) {// accepte les formats internationaux avec +, espaces, tirets
  var phone = /^[\+]?[0-9\s\-]{8,}$/;
  return phone.test(tel);
}

// Fonction pour afficher un message d'erreur
//utilisé dans le js
function afficherErreur(element, message) {
  if (!element) return;
  
  element.style.border = '2px solid #ff3366';
  element.style.backgroundColor = 'rgba(255, 51, 102, 0.05)';

  var erreurExistante = element.parentNode.querySelector('.message-erreur');
  if (erreurExistante) {
    erreurExistante.remove();
  }//s'il y a déja un msg d'err on le remplace:

  var messageErreur = document.createElement('div');
  messageErreur.className = 'message-erreur';
  messageErreur.style.color = '#ff3366';
  messageErreur.style.fontSize = '0.85rem';
  messageErreur.style.marginTop = '5px';
  messageErreur.style.fontWeight = '600';
  messageErreur.textContent = '⚠ ' + message;

  element.parentNode.appendChild(messageErreur);
  // =>  colorie le champ en rouge et insère un <div> d'erreur en dessous
}

// Fonction pour effacer les erreurs
function effacerErreur(element) {
  if (!element) return;
  
  element.style.border = '';
  element.style.backgroundColor = '';
  var erreur = element.parentNode.querySelector('.message-erreur');
  //element.parentNode : Accède à l'élément parent direct du nœud element
  // .querySelector('.message-erreur') : Recherche, dans le contenu de cet élément parent, le premier élément qui correspond au sélecteur CSS .message-erreur (c'est-à-dire un élément ayant la classe message-erreur)
  if (erreur) {
    erreur.remove();
  }
  //=> supprime la couleur et le message d'erreur
}

// Fonction pour créer un popup personnalisé
function creerPopup(titre, contenu, boutons) {
//construit dynamiquement une fenêtre modale (overlay sombre + boîte verte) avec des boutons configurables. 
//Utilisé pour la popup de vérification avant soumission du formulaire de réservation.

  // Créer l'overlay (fond sombre)
  var overlay = document.createElement('div');
  overlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); z-index: 9999; display: flex; align-items: center; justify-content: center; animation: fadeIn 0.3s ease;';
  
  // Créer le popup
  var popup = document.createElement('div');
  popup.style.cssText = 'background: #1a1a1a; border: 2px solid #00ff88; border-radius: 12px; padding: 2rem; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0, 255, 136, 0.3); animation: slideIn 0.3s ease;';
  
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

//CHARACTER COUNTER 

function ajouterCompteurCaracteres(textarea) {
//Attaché au <textarea> des formulaires contact et réservation. Il :
// Impose un maxlength="500" au niveau HTML
// Affiche un compteur dynamique en bas à droite ("487 / 500 caractères restants")
// Change de couleur : vert → orange (< 50 restants) → rouge (0 restant)

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
  textarea.onkeyup = mettreAJourCompteur;//se déclenche lorsque l'utilisateur relâche une touche du clavier après l'avoir enfoncée

  // Initialisation
  mettreAJourCompteur();

  console.log('Compteur de caractères ajouté');
}

//AUTO-SAVE 

// Fonction pour sauvegarder un formulaire
function sauvegarderFormulaire(formulaire, nomFormulaire) {
//Sauvegarde automatiquement le formulaire dans le localStorage toutes les 3 secondes, 
//pour ne pas perdre les données en cas de fermeture accidentelle de l'onglet.

  if (!formulaire || !window.localStorage) return;
  //Si le formulaire n’existe pas → on arrête
  // Si le navigateur ne supporte pas localStorage → on arrête
  // => évite les erreurs
  
  var elements = formulaire.elements;
  //elements contient tous les inputs, textarea, select…
  for (var i = 0; i < elements.length; i++) {//On traite chaque élément un par un
    var element = elements[i];
    var cle = nomFormulaire + '_' + element.name;
    
    if (element.type === 'checkbox' || element.type === 'radio') {
      if (element.checked) {
        localStorage.setItem(cle, 'checked');
      } else {
        localStorage.removeItem(cle);
      }//On stocke seulement si c’est coché
    } else if (element.type !== 'submit' && element.name) {//Ignore le bouton submit & Vérifie que le champ a un name
      localStorage.setItem(cle, element.value);//On sauvegarde la valeur (texte, email, etc.)
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

// Fonction pour effacer la sauvegarde dans le localstorage
function effacerSauvegarde(nomFormulaire) {
  if (!window.localStorage) return;
  
  var cles = [];
  for (var i = 0; i < localStorage.length; i++) {
    var cle = localStorage.key(i);//récupère toutes les clés stockées dans le navigateur
    if (cle.indexOf(nomFormulaire + '_') === 0) {
      cles.push(cle);//recupere les cles lies à ce form
    }
  }
  
  for (var j = 0; j < cles.length; j++) {
    localStorage.removeItem(cles[j]);
  }
  
  console.log('Sauvegarde effacée:', nomFormulaire);
}

//VALIDATION DATE 

function configurerValidationDate() {
  var champDate = document.getElementById('date');//On cible l’élément avec id="date"
  if (!champDate) return;//S’il n’existe pas => on arrête (évite une erreur)
  
  var aujourdhui = new Date();//Crée un objet contenant la date actuelle
  var annee = aujourdhui.getFullYear();
  var mois = aujourdhui.getMonth() + 1;//getMonth() commence à 0 (janvier = 0)
  var jour = aujourdhui.getDate();
  
  // Formater avec des zéros
  if (mois < 10) mois = '0' + mois;
  if (jour < 10) jour = '0' + jour;//YYYY-MM-DD
  
  var dateMin = annee + '-' + mois + '-' + jour;
  
  // Définir la date minimale
  champDate.setAttribute('min', dateMin);
  
  console.log('Validation de date configurée. Date min:', dateMin);
}

// ANIMATED COUNTERS

function animerCompteur(element) { //anime UN compteur
  var cible = parseInt(element.getAttribute('data-count'));
  //element.getAttribute('data-count') récupère la valeur de l'attribut HTML, qui est toujours une chaîne de caractères (ex: "42")
  //parseInt(...) convertit cette chaîne en un entier (ex: 42)
  var suffixe = element.getAttribute('data-suffix');
  //exple: cible = 1580    suffixe = "+" (ou vide)
  if (!suffixe) suffixe = '';
  var duree = 800; // animation dure 0.8 seconde
  var increment = cible / (duree / 16); // 60 FPS, on calcule combien ajouter à chaque frame
  var valeurActuelle = 0;

  element.textContent = '0' + suffixe; //Le compteur commence à 0

  var intervalle = setInterval(function() {
    valeurActuelle = valeurActuelle + increment;

    if (valeurActuelle >= cible) {
      valeurActuelle = cible;
      clearInterval(intervalle);//empêche de dépasser & arrête l’animation
    }

    element.textContent = Math.floor(valeurActuelle) + suffixe;//l'elt prend cette nouvelle valeur(l'affiche)
  }, 16); //Toutes les 16 ms, on augmente la valeur
}

function initialiserCompteursAnimes() {   //gère tous les compteurs de la page
  var compteurs = document.querySelectorAll('.stat-number[data-count]');
  // Récupère tous les <span> avec : classe stat-number  & attribut data-count

  var compteursAnimes = [];

  // Fonction pour vérifier si un élément est visible dans l'ecran
  //Permet de déclencher l’animation au scroll
  function estVisible(element) {
    var rect = element.getBoundingClientRect();
    //method on a DOM element to retrieve its size and position relative to the viewport
    var hauteurFenetre = window.innerHeight;
    return rect.top < hauteurFenetre - 100;
    //top, right, bottom, left: The distances from the viewport edges to the respective sides of the element
    //Ce code vérifie si le haut d'un élément est dans ou près de la zone visible du navigateur (viewport)
  }

  // Fonction pour vérifier et animer les compteurs
  function verifierEtAnimer() {
    if (compteurs.length === 0) return;
    //=> précaution: only run counter logic when there are counters on this page

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
  
  // au scroll : vérifie visibilité & lance animation si nécessaire
  window.onscroll = function() {
    verifierEtAnimer();
    
    //À chaque scroll : on appelle animerAuScroll(), ça déclenche les animations des éléments visibles
    animerAuScroll();
    
    // Bouton scroll-to-top
    var boutonScroll = document.querySelector('.scroll-top');
    //On récupère le bouton qui permet de remonter en haut
    if (boutonScroll) {
      if (window.pageYOffset > 300) { //Si tu descends de plus de 300px, le bouton apparaît :
        boutonScroll.style.display = 'flex';
        boutonScroll.style.opacity = '1';
      } else {
        boutonScroll.style.opacity = '0';//sinon Il disparaît doucement (fade out)
        setTimeout(function() {
          if (window.pageYOffset <= 300) {
            boutonScroll.style.display = 'none';
          }
        }, 300);//Après 300ms :il devient invisible (display none)
      }
    }
  };
  
  // Vérifier au chargement
  verifierEtAnimer();
  
  console.log('Compteurs animés initialisés:', compteurs.length);
}

//  FORMULAIRE DE CONTACT 

function initialiserFormulaireContact() {
  console.log('Initialisation formulaire contact...');
  //Juste pour vérifier dans la console que la fonction s’exécute
  
  var champPrenom = document.getElementById('prenom');
  if (!champPrenom) {
    console.log('Pas de champ prénom - pas sur la page contact');
    return;
    //Si #prenom n’existe pas => ce n’est pas la page contact
    //Donc on arrête la fonction
  }

  var formulaire = document.querySelector('form.formulaire-bloc');
  //On cherche un <form> avec la classe formulaire-bloc
  if (!formulaire) {
    console.log('Formulaire non trouvé');
    return;
  }

  console.log('Formulaire contact détecté!');
  
  // Restaurer la sauvegarde s'il y a
  restaurerFormulaire(formulaire, 'contact');
  
  // Ajouter compteur de caractères au message
  var champMessage = document.getElementById('message');
  ajouterCompteurCaracteres(champMessage);
  
  // Auto-save toutes les 2 secondes
  setInterval(function() {
    sauvegarderFormulaire(formulaire, 'contact');
  }, 2000);

  // Clear localStorage when exiting the page
  window.addEventListener('beforeunload', function() {
    effacerSauvegarde('contact');
  });

  formulaire.onsubmit = function(event) {
    effacerSauvegarde('contact'); // Clear form data before submitting
    return true;
  };

  console.log('Formulaire contact initialisé avec succès');
}

// FORMULAIRE DE RÉSERVATION 

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
  
  // Auto-save toutes les 2 secondes
  setInterval(function() {
    sauvegarderFormulaire(formulaire, 'reservation');
  }, 2000);

  // Clear localStorage when exiting the page
  window.addEventListener('beforeunload', function() {
    effacerSauvegarde('reservation');
  });

  // Show/hide instructor based on coaching checkbox
  var coachingCheckbox = document.querySelector('input[name="coaching"]');//la checkbox "Coaching de course" (par son attribut name)
  var instructeurGroup = document.getElementById('instructeur-group');
  //le bloc <div id="instructeur-group"> qui contient la liste des instructeurs (caché par défaut en PHP avec style="display:none")
  if (coachingCheckbox && instructeurGroup) {
    //Double vérification de sécurité : si l'un des deux éléments n'existe pas dans la page (ex: on n'est pas sur reserver.php), le code ne s'exécute pas et évite une erreur.
    coachingCheckbox.addEventListener('change', function() {
      if (this.checked) {
        instructeurGroup.style.display = 'block';// coaching coché => on montre
      } else {
        instructeurGroup.style.display = 'none'; // décoché => on cache
        var instrSelect = document.getElementById('instructeur');
        if (instrSelect) instrSelect.value = '0';// reset du select à "pas de préférence"
      }
      //À chaque fois que la checkbox change d'état, la fonction se déclenche. 
      // this désigne la checkbox elle-même. Le reset à '0' est important : 
      // si l'utilisateur avait choisi un instructeur puis décoche coaching, 
      // le choix est annulé proprement avant envoi du formulaire.
    });
  }

  function calculerPrixTotal() {
    var prixTotal = 0;
    var sessionSelectionnee = document.querySelector('input[name="session"]:checked');
    // on cherche quel radio "session" est coché (enfants, adultes ou groupe). Le sélecteur CSS :checked filtre directement le radio actif. 
    // On ajoute son prix depuis le dictionnaire global prixSessions défini en haut du fichier
    if (sessionSelectionnee) {
      prixTotal = prixTotal + prixSessions[sessionSelectionnee.value];
    }

    var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    for (var i = 0; i < checkboxes.length; i++) {
      var checkbox = checkboxes[i];
      if (checkbox.checked && checkbox.name !== 'conditions') {
        //checkbox.checked => elle doit être cochée
        // checkbox.name !== 'conditions' => on exclut la checkbox "J'accepte les conditions générales" qui n'a pas de prix associé
        var nomService = checkbox.name;
        if (prixServices[nomService]) {
          prixTotal = prixTotal + prixServices[nomService];
        }
      }
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
        //insertBefore(A, B) place A juste avant B
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
    //verifier les champs

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
      //date.value retourne une chaîne de caractères au format "2026-11-15". 
      // new Date(...) la convertit en objet Date comparable mathématiquement.
      var dateAujourdhui = new Date();//new Date() sans argument crée un objet représentant l'instant exact maintenant, par exemple 2026-05-03 14:37:22
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
    } else if (participantsInput.value > 10) {
      afficherErreur(participantsInput, 'Le nombre maximum de participants est 10');
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
               effacerSauvegarde('reservation');
               formulaire.submit();
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

  //la boucle de validation plus haut dans initialiserFormulaireReservation parcourt tous les éléments du formulaire
  // et leur assigne .onchange / .oninput pour effacer les erreurs. 
  // Ce faisant, elle écrase les handlers définis dans les <script> inline de reserver.php. 
  // Ce bloc les remet en place en dernier, garantissant qu'ils ne seront plus jamais écrasés.

  // getElementsByName('session') retourne tous les radios du groupe session (enfants, adultes, groupe). 
  // On boucle dessus et on assigne à chacun le même handler majParticipants (défini dans reserver.php). 
  // Quand l'utilisateur change de session, majParticipants ajuste automatiquement le champ participants :
//adultes/enfants sélectionné => participants bloqué à 1
//groupe sélectionné => participants peut aller jusqu'à 10
  var sessRadios = document.getElementsByName('session');
  for (var sr = 0; sr < sessRadios.length; sr++) {
    sessRadios[sr].onchange = majParticipants;
  }

  //Quand le nombre de participants change, deux choses doivent se passer en même temps, donc on crée une fonction anonyme qui appelle les deux. 
  // On attache la même logique sur deux événements :
  // onchange => se déclenche quand l'utilisateur quitte le champ (perd le focus)
  // oninput => se déclenche à chaque frappe, pour une réactivité immédiate
  var partField = document.getElementById('participants');
  if (partField) {
    partField.onchange = function() {
      afficherPrixTotal();
      checkKartLimits();
    };
    partField.oninput = function() {
      afficherPrixTotal();
      checkKartLimits();
    };
  }

  // .kart-qty-input cible tous les champs de quantité de karts (un par modèle de kart). 
  // À chaque modification d'un champ, checkKartLimits (défini dans reserver.php) vérifie que 
  // la somme des karts sélectionnés ne dépasse pas le nombre de participants, 
  // et grise les autres si la limite est atteinte.
  var kartFields = document.querySelectorAll('.kart-qty-input');
  for (var kf = 0; kf < kartFields.length; kf++) {
    kartFields[kf].oninput = checkKartLimits;
  }

  // Dès que l'utilisateur choisit une date, filtrerCreneaux (défini en bas de reserver.php) consulte creneauxOccupesData (objet PHP→JS contenant les créneaux déjà réservés) 
  // et désactive dans le <select> les heures qui ne sont plus disponibles ce jour-là.
  var dateField = document.getElementById('date');
  if (dateField) {
    dateField.onchange = filtrerCreneaux;
  }

  // Appliquer l'état initial correct dès le chargement
  majParticipants();
  checkKartLimits();

  console.log('Formulaire réservation initialisé avec succès');
}

//  ANIMATIONS 

function animerAuScroll() {//Anime les éléments quand ils deviennent visibles sur l'ecran
  var elements = document.querySelectorAll('.carte, .galerie-item, .media-bloc');
  //Tous les éléments à animer :cartes, galerie, médias
  for (var i = 0; i < elements.length; i++) {
    var element = elements[i];
    var position = element.getBoundingClientRect();
    var hauteurFenetre = window.innerHeight;

    if (position.top < hauteurFenetre - 100) {//Si l’élément entre dans l’écran :
      element.style.opacity = '1';
      element.style.transform = 'translateY(0)';//repositions it to its original place
      //fade-in + slide vers le haut
    }
  }
}

function animerLogo() {
  //Quand tu passes la souris :
  // zoom léger + rotation du point
  var logo = document.querySelector('.logo');
  if (!logo) return;

  var logoPoint = logo.querySelector('.logo-point');

  logo.onmouseover = function() {
    if (logoPoint) {
      logoPoint.style.transform = 'scale(1.5) rotate(360deg)';
      //Le point du logo est agrandi à 1.5 fois et fait une rotation complète de 360 degrés.
      logoPoint.style.transition = 'transform 0.6s ease';
    }
    this.style.transform = 'scale(1.05)';
    //Le logo entier est légèrement agrandi à 1.05 fois
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
    //monte légèrement + zoom + ombre verte

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
    e.preventDefault();//Empêche le comportement par défaut du bouton
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  };//remonte en haut doucement
}

function animerCartes() {
  var cartes = document.querySelectorAll('.carte');

  for (var i = 0; i < cartes.length; i++) {
    cartes[i].style.transition = 'all 0.4s ease';

    cartes[i].onmouseover = function() {
      this.style.transform = 'translateY(-10px) scale(1.03)';
      this.style.boxShadow = '0 15px 40px rgba(0, 255, 136, 0.3)';
    };
    //monte + zoom + ombre

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
    //zoom image + passe devant (z-index)

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
    //couleur verte + petit déplacement vers le haut
    
    liens[i].onmouseout = function() {
      if (!this.classList.contains('actif')) {
        this.style.color = '';
      }
      this.style.transform = 'translateY(0)';
    };
  }
}

function initialiserStylesAnimation() {//initialiser les elts
  var elements = document.querySelectorAll('.carte, .galerie-item, .media-bloc');

  for (var i = 0; i < elements.length; i++) {
    elements[i].style.opacity = '0';
    elements[i].style.transform = 'translateY(30px)';
    elements[i].style.transition = 'opacity 0.8s ease, transform 0.8s ease';
  }
  //éléments invisibles + légèrement en bas
  //puis animés au scroll
}

//  INITIALISATION 

function initialiser() { //Elle lance tout
  console.log('=============');
  console.log('APEX KART - Initialisation...');
  console.log('=============');
  
  // Formulaires
  initialiserFormulaireContact();
  initialiserFormulaireReservation();
  
  // Compteurs animés 
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
  //Lance les animations après 200ms (laisse le temps au navigateur de
  //appliquer les styles & calculer les positions)

  console.log('Page actuelle:', window.location.pathname);
  console.log('Date:', new Date().toLocaleDateString('fr-FR'));
  console.log('✅ Script initialisé avec succès!');
}

window.onload = function() {
  initialiser();
};//Quand la page est chargée => tout démarre