<!doctype html> <!-- Déclaration du type de document HTML5 -->
<html lang="fr"> <!-- Balise racine HTML avec langue française -->
  <head> <!-- En-tête du document contenant les métadonnées -->
    <meta charset="utf-8" /> <!-- Encodage des caractères UTF-8 pour support des accents -->
    <meta name="viewport" content="width=device-width, initial-scale=1" /> <!-- Configuration responsive pour mobile -->
    <title>Gestion des Agents et Stagiaires</title> <!-- Titre affiché dans l'onglet du navigateur -->

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" /> <!-- Import Bootstrap 5 depuis CDN -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css"> <!-- Import Bootstrap local (fallback) -->

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" /> <!-- Import icônes Bootstrap depuis CDN -->
    <link rel="stylesheet" href="assets/css/bootstrap-icons.css"> <!-- Import icônes Bootstrap local (fallback) -->

    <!-- Consolidated styles -->
    <link href="/assets/css/styles.css" rel="stylesheet" /> <!-- Import feuille de style personnalisée -->
    
    <!-- QR Code Generator Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    
    <style>
      /* Styles CSS personnalisés pour la page agents */
      .agent-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease; /* Transition pour effet hover */
      }
      .agent-card:hover {
        transform: translateY(-5px); /* Légère élévation au survol */
        box-shadow: 0 8px 20px rgba(0,0,0,0.15); /* Ombre plus prononcée */
      }
      /* Zone QR code dans la carte */
      .qr-code-container {
        width: 150px; /* Largeur fixe pour le QR code */
        height: 150px; /* Hauteur fixe pour le QR code */
        margin: 0 auto; /* Centrage horizontal */
        background: white; /* Fond blanc */
        padding: 10px; /* Padding autour du QR code */
        border-radius: 8px; /* Coins arrondis */
        border: 2px solid #e5e7eb; /* Bordure grise légère */
      }
      /* Carte d'impression */
      .print-card {
        width: 100%; /* Largeur complète */
        max-width: 400px; /* Largeur maximale de 400px */
        margin: 0 auto; /* Centrage */
        padding: 20px; /* Padding interne */
        background: white; /* Fond blanc */
        border: 2px solid #000; /* Bordure noire */
        border-radius: 8px; /* Coins arrondis */
      }
      /* Styles pour l'impression */
      @media print {
        body * { visibility: hidden; } /* Masque tout le contenu par défaut */
        .print-card, .print-card * { visibility: visible; } /* Affiche uniquement la carte d'impression */
        .print-card {
          position: absolute; /* Position absolue pour l'impression */
          left: 0; /* Aligné à gauche */
          top: 0; /* Aligné en haut */
          width: 100%; /* Largeur complète */
          max-width: 100%; /* Largeur maximale sans restriction */
          border: none; /* Pas de bordure à l'impression */
          box-shadow: none; /* Pas d'ombre à l'impression */
        }
        /* Masquer les boutons et éléments non nécessaires à l'impression */
        .no-print { display: none !important; }
      }
      /* Réduction du menu overlay */
      #mobileSidebar {
        width: 280px; /* Largeur réduite de 280px */
        max-width: 85vw; /* Largeur maximale de 85% de la largeur de la fenêtre */
      }
      #mobileSidebar .offcanvas-header {
        padding: 1rem 1.25rem; /* Padding réduit pour l'en-tête */
      }
      #mobileSidebar .offcanvas-title {
        font-size: 1rem; /* Taille de police réduite */
      }
      #mobileSidebar .nav-link {
        padding: 0.75rem 1.25rem; /* Padding réduit pour les liens */
        font-size: 0.95rem; /* Taille de police légèrement réduite */
      }
      @media (max-width: 767.98px) {
        #mobileSidebar {
          width: 260px; /* Largeur encore plus réduite sur mobile */
          max-width: 80vw; /* Largeur maximale de 80% sur mobile */
        }
      }
    </style>
  </head> <!-- Fin de l'en-tête -->
  <body class="bg-light"> <!-- Corps du document avec classe Bootstrap pour fond clair -->
    
    <!-- Offcanvas sidebar (mobile et desktop) -->
    <!-- Menu latéral pour mobile et desktop qui s'ouvre en overlay -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel"> <!-- Offcanvas Bootstrap s'ouvrant depuis la gauche -->
      <div class="offcanvas-header bg-dark text-white"> <!-- En-tête de l'offcanvas avec fond sombre -->
        <h5 class="offcanvas-title" id="mobileSidebarLabel">Gestion RH</h5> <!-- Titre du menu latéral -->
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Fermer"></button> <!-- Bouton de fermeture blanc -->
      </div>
      <div class="offcanvas-body p-0"> <!-- Corps de l'offcanvas sans padding -->
        <ul class="nav flex-column"> <!-- Liste de navigation verticale -->
          <li class="nav-item"> <!-- Élément de navigation -->
            <a class="nav-link" href="dashboard.html"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a> <!-- Lien Dashboard avec icône -->
          </li>
          <li class="nav-item"> <!-- Élément de navigation -->
            <a class="nav-link active bg-primary text-white" href="agent.php"><i class="bi bi-people-fill me-2"></i>Agents</a> <!-- Lien Agents actif avec icône -->
          </li>
          <li class="nav-item"> <!-- Élément de navigation -->
            <a class="nav-link" href="pointage.html"><i class="bi bi-clock-fill me-2"></i>Pointage</a> <!-- Lien Pointage avec icône -->
          </li>
          
        </ul>
      </div>
    </div>

    <!-- Main Container -->
    <!-- Conteneur principal du contenu -->
    <div class="container-fluid px-3 px-md-4 py-4"> <!-- Container fluid Bootstrap avec padding responsive -->
      
      <!-- Header -->
      <!-- En-tête de la page avec titre et bouton menu -->
      <div class="d-flex align-items-center justify-content-between mb-4"> <!-- Flexbox pour aligner le titre et le bouton -->
        <div class="d-flex align-items-center gap-3"> <!-- Flexbox pour aligner bouton et titre -->
          <!-- Bouton menu hamburger pour mobile et desktop -->
          <button class="btn btn-light btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar"> <!-- Bouton pour ouvrir le menu -->
            <i class="bi bi-list"></i> <!-- Icône hamburger (trois lignes) -->
          </button>
          <div> <!-- Div contenant le titre -->
            <h1 class="h3 mb-0"><i class="bi bi-people-fill me-2 text-primary"></i>Gestion des Agents</h1> <!-- Titre principal avec icône -->
            <small class="text-muted">Enregistrement et génération de QR codes</small> <!-- Sous-titre descriptif -->
          </div>
        </div>
      </div>

      <!-- Tabs Navigation -->
      <!-- Navigation  agents  -->
      <ul class="nav nav-tabs mb-4" id="agentTabs" role="tablist"> <!-- Liste d'onglets Bootstrap -->
        <li class="nav-item" role="presentation"> <!-- Onglet Agents -->
          <button class="nav-link active" id="agents-tab" data-bs-toggle="tab" data-bs-target="#agents" type="button" role="tab"> <!-- Bouton onglet actif -->
            <i class="bi bi-person-badge me-2"></i>Agents <!-- Icône et texte Agents -->
          </button>
        </li>
        
      </ul>

      <!-- Tab Content -->
      <!-- Contenu des onglets -->
      <div class="tab-content" id="agentTabContent"> <!-- Conteneur du contenu des onglets -->
        
        
        <!-- Onglet des agents -->
        <div class="tab-pane fade show active" id="agents" role="tabpanel"> <!-- Panneau Agents actif par défaut -->
          
          <!-- Form Section -->
          <!-- Section formulaire pour ajouter un agent -->
          <div class="row mb-4"> <!-- Ligne Bootstrap -->
            <div class="col-12 col-lg-4"> <!-- Colonne formulaire (4/12 sur desktop) -->
              <div class="card shadow-sm"> <!-- Carte Bootstrap avec ombre légère -->
                <div class="card-body"> <!-- Corps de la carte -->
                  <h5 class="card-title mb-3"><i class="bi bi-person-plus me-2"></i>Nouvel Agent</h5> <!-- Titre de la section -->
                  
                  <!-- Formulaire d'ajout d'agent -->
                  <form id="agentForm" class="needs-validation" novalidate> <!-- Formulaire avec validation Bootstrap -->
                    <!-- Champ Nom -->
                    <div class="mb-3"> <!-- Marge en bas -->
                      <label for="agentNom" class="form-label">Matricule </label> <!-- Label du champ -->
                      <input type="text" class="form-control form-control-rounded" id="agentmatricule" required /> <!-- Input texte requis -->
                     
                    </div>
                    
                    <!-- Champ noms -->
                    <div class="mb-3"> <!-- Marge en bas -->
                      <label  class="form-label">Nom </label> <!-- Label du champ -->
                      <input type="text" class="form-control form-control-rounded" id="agentNom" required /> <!-- Input email requis -->
                      
                    </div>
                  <!-- Champ noms -->
                    <div class="mb-3"> <!-- Marge en bas -->
                      <labe class="form-label">Post-Nom</label> <!-- Label du champ -->
                      <input type="text" class="form-control form-control-rounded" id="agentPostNom" required /> <!-- Input email requis -->
                      
                    </div>
                    
                    <!-- Champ noms -->
                    <div class="mb-3"> <!-- Marge en bas -->
                      <label class="form-label">Prénom </label> <!-- Label du champ -->
                      <input type="text" class="form-control form-control-rounded" id="agentEmail" required /> <!-- Input email requis -->
                      
                    </div>
                    <div class="mb-3"> <!-- Marge en bas -->
                      <labe class="form-label">Sexe</label> <!-- Label du champ -->
                      <select class="form-select form-control-rounded" id="agentSexe" required> <!-- Select requis -->
                        <option value="" selected disabled>Choisir un sexe...</option> <!-- Option par défaut -->
                        <option>Masculin</option> <!-- Option sexe masculin -->
                        <option>Féminin</option> <!-- Option sexe féminin -->
                        <option>Autre</option> <!-- Option sexe autre -->
                      </select>
                      <div class="invalid-feedback">Veuillez sélectionner un sexe</div> <!-- Message d'erreur validation -->
                      
                    </div>
                    <!-- Champ Service -->
                    <div class="mb-3"> <!-- Marge en bas -->
                      <label for="agentService" class="form-label">Fonction</label> <!-- Label du champ -->
                      <select class="form-select form-control-rounded" id="agentService" required> <!-- Select requis -->
                        <option value="" selected disabled>Choisir une Fonction...</option> <!-- Option par défaut -->
                        <option>Production</option> <!-- Option service -->
                        <option>RH</option> <!-- Option service -->
                        <option>Logistique</option> <!-- Option service -->
                        <option>Compta</option> <!-- Option service -->
                        <option>IT</option> <!-- Option service -->
                      </select>
                      <div class="invalid-feedback">Veuillez sélectionner une Fonction</div> <!-- Message d'erreur validation -->
                    </div>
                     <!-- Champ Service -->
                   
                    
                    
                    
                    <!-- Bouton de soumission -->
                    <div class="d-grid"> <!-- Grille Bootstrap pour bouton pleine largeur -->
                      <button type="submit" class="btn btn-primary btn-rounded"> <!-- Bouton de soumission -->
                        <i class="bi bi-save2 me-2"></i>Enregistrer l'agent <!-- Icône sauvegarde et texte -->
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <!-- Agents List Section -->
            <!-- Section liste des agents avec QR codes -->
            <div class="col-12 col-lg-8"> <!-- Colonne liste (8/12 sur desktop) -->
              <div class="card shadow-sm"> <!-- Carte Bootstrap -->
                <div class="card-body"> <!-- Corps de la carte -->
                  <div class="d-flex align-items-center justify-content-between mb-3"> <!-- En-tête de la section -->
                    <h5 class="card-title mb-0"><i class="bi bi-people me-2"></i>Liste des Agents</h5> <!-- Titre de la section -->
                    <span class="badge bg-primary" id="agentCount">0</span> <!-- Badge compteur d'agents -->
                  </div>
                  
                  <!-- Container pour les cartes d'agents -->
                  <div class="row g-3" id="agentsList"> <!-- Ligne Bootstrap avec espacement -->
                    <!-- Les cartes d'agents seront injectées ici par JavaScript -->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

       
      </div>
    </div>

    <!-- Bootstrap JS (with Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> <!-- Script Bootstrap avec Popper inclus depuis CDN -->

    <!-- Shared theme script + page script -->
    <script src="/assets/js/theme.js"></script> <!-- Script pour gestion du thème (clair/sombre) -->
    <script src="/assets/js/agent.js"></script> <!-- Script spécifique à la gestion des agents -->
  </body> <!-- Fin du corps -->
</html> <!-- Fin du document HTML -->

