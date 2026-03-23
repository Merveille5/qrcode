<?php
session_start();
require_once __DIR__ . '/db.php';
require_once 'phpqrcode/phpqrcode/qrlib.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($conn) && isset($pdo)) $conn = $pdo;

/* ================= DOSSIERS ================= */
$UPLOAD_DIR = __DIR__ . '/uploads/photos/';
$QRCODE_DIR = __DIR__ . '/qrcodes/';
$CARDS_DIR  = __DIR__ . '/cards/';

foreach ([$UPLOAD_DIR, $QRCODE_DIR, $CARDS_DIR] as $d) {
    if (!is_dir($d)) mkdir($d, 0755, true);
}

function webPath($path) {
    return dirname($_SERVER['SCRIPT_NAME']) . '/' . ltrim($path, '/');
}

/* =========================================================
   ENREGISTREMENT AGENT (POST)
========================================================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agent_submit'])) {

    try {
        $matricule   = trim($_POST['matricule']);
        $nom         = trim($_POST['nom']);
        $post_nom    = trim($_POST['post_nom']);
        $prenom      = trim($_POST['prenom']);
        $id_fonction = $_POST['id_fonction'] ?: null;
        
    

        if ($matricule === '' || $nom === '' || $prenom === '') {
            throw new Exception("Champs obligatoires manquants");
        }

        /* PHOTO */
        $photoRel = null;
        if (!empty($_FILES['photo']['name'])) {
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $filename = time().'_'.$matricule.'.'.$ext;
            move_uploaded_file($_FILES['photo']['tmp_name'], $UPLOAD_DIR.$filename);
            $photoRel = 'uploads/photos/'.$filename;
        }

        /* INSERT */
        $stmt = $conn->prepare("INSERT INTO T_Agents
            (matricule, nom, post_nom, prenom, photo, id_fonction)
            VALUES (?,?,?,?,?,?)");
        $stmt->execute([
            $matricule, $nom, $post_nom, $prenom,
            $photoRel, $id_fonction
        ]);

        $matricule = $conn->lastInsertId();

        /* QR CODE */
        $qrFile = $QRCODE_DIR.$matricule.'.png';
        QRcode::png("AGENT_ID:$matricule", $qrFile, QR_ECLEVEL_L, 4);

        /* ================= CARTE IMAGE AMÉLIORÉE (DESIGN PREMIUM) ================= */
        $card = imagecreatetruecolor(850, 530);
        $white       = imagecolorallocate($card, 255, 255, 255);
        $blueMain    = imagecolorallocate($card, 15, 23, 42); 
        $accentBlue  = imagecolorallocate($card, 37, 99, 235);
        $bgLight     = imagecolorallocate($card, 241, 245, 249);
        $textBlack   = imagecolorallocate($card, 30, 41, 59);
        $textMuted   = imagecolorallocate($card, 100, 116, 139);

        imagefill($card, 0, 0, $white);
        imagefilledrectangle($card, 0, 0, 850, 110, $blueMain);
        imagefilledrectangle($card, 360, 110, 850, 530, $bgLight);
        imagefilledrectangle($card, 0, 515, 850, 530, $accentBlue);

        $font = __DIR__ . '/arial.ttf'; 
        $useTTF = file_exists($font);

        if ($photoRel && file_exists(__DIR__ . '/' . $photoRel)) {
            $src = imagecreatefromstring(file_get_contents(__DIR__ . '/' . $photoRel));
            if ($src) {
                imagefilledrectangle($card, 55, 145, 315, 405, $bgLight); 
                imagecopyresampled($card, $src, 60, 150, 0, 0, 250, 250, imagesx($src), imagesy($src));
                imagedestroy($src);
            }
        }

        if ($useTTF) {
            imagettftext($card, 20, 0, 40, 65, $white, $font, "CARTE D'IDENTITÉ PROFESSIONNELLE");
            imagettftext($card, 10, 0, 40, 90, $accentBlue, $font, "GESTION DES RESSOURCES HUMAINES - GESPERS");
            imagettftext($card, 9, 0, 390, 160, $textMuted, $font, "NOM ET POST-NOM");
            imagettftext($card, 22, 0, 390, 200, $textBlack, $font, strtoupper("$nom $post_nom"));
            imagettftext($card, 9, 0, 390, 250, $textMuted, $font, "PRÉNOM");
            imagettftext($card, 18, 0, 390, 285, $textBlack, $font, $prenom);
            imagefilledrectangle($card, 390, 340, 580, 385, $blueMain);
            imagettftext($card, 12, 0, 405, 372, $white, $font, "MAT: $matricule");
            imagettftext($card, 9, 0, 390, 490, $textMuted, $font, "Document officiel émis le " . date('d/m/Y'));
        }

        if (file_exists($qrFile)) {
            $qr = imagecreatefrompng($qrFile);
            if ($qr) {
                imagefilledrectangle($card, 695, 365, 825, 495, $white);
                imagecopyresampled($card, $qr, 700, 370, 0, 0, 120, 120, imagesx($qr), imagesy($qr));
                imagedestroy($qr);
            }
        }

        $cardFileName = "agent_$matricule.png";
        imagepng($card, $CARDS_DIR . $cardFileName, 9);
        imagedestroy($card);

        $cardRel = "cards/" . $cardFileName;
        $conn->prepare("UPDATE agent SET card_image=? WHERE matricule=?")->execute([$cardRel, $id_agent]);

        header('Location: agent.php?success=1');
        exit;

    } catch (Throwable $e) {
        header('Location: agent.php?error='.urlencode($e->getMessage()));
        exit;
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gespers - Agents</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        :root { --accent: #4f46e5; --accent-2: #818cf8; --bg: #f8fafc; --card-bg: #ffffff; }
        .dashboard-container { max-width: 1600px; margin: 0 auto; min-height: 100vh; }
        /* ENTETE AVEC COULEUR IDENTIQUE AU DASHBOARD */
        .dashboard-header { background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%); color: white; padding: 1.5rem 2rem; border-radius: 16px; box-shadow: 0 4px 20px rgba(79,70,229,0.15); margin-bottom: 2rem; }
        .agent-card { transition: all 0.3s ease; border: none; border-radius: 12px; }
        .agent-card:hover { transform: translateY(-5px); box-shadow: 0 12px 30px rgba(0,0,0,0.1); }
        #mobileSidebar { width: 280px; }
        .nav-link.active { background-color: var(--accent) !important; color: white !important; border-radius: 8px; }
        @media (max-width: 767.98px) { .dashboard-header { padding: 1rem; } }
    </style>
</head>
<body class="bg-light">

<div class="dashboard-container">
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar">
        <div class="offcanvas-header bg-dark text-white">
            <h5 class="offcanvas-title">Gestion RH</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            <ul class="nav flex-column p-3">
                <li class="nav-item mb-2"><a class="nav-link" href="../dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                <li class="nav-item mb-2"><a class="nav-link active" href="agent.php"><i class="bi bi-people-fill me-2"></i>Agents</a></li>
                
            </ul>
        </div>
    </div>

    <div class="container-fluid px-3 px-md-4 py-4">
        <div class="dashboard-header rounded-4 mb-4">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-light btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <div>
                        <h2 class="h4 mb-1 fw-bold text-white"> <i class="bi bi-people-fill me-2"></i>Gestion des Agents </h2>
                        <small class="opacity-75">Enregistrement et identification</small>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <div class="dropdown">
                        <a class="d-flex align-items-center text-white text-decoration-none" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="avatar bg-white rounded-circle" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-person-fill text-primary fs-5"></i>
                            </div>
                            <div class="d-none d-sm-block text-start ms-2">
                                <strong class="text-white">Mhn024</strong>
                            </div>
                            <i class="bi bi-chevron-down ms-2 d-none d-sm-block"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4"><i class="bi bi-person-plus me-2 text-primary"></i>Nouvel Agent</h5>
                        <form id="agentForm" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="agent_submit" value="1">
                            <div class="mb-3"><label class="form-label small fw-bold">Matricule</label><input name="matricule" class="form-control" required></div>
                            <div class="mb-3"><label class="form-label small fw-bold">Nom</label><input name="nom" class="form-control" required></div>
                            <div class="mb-3"><label class="form-label small fw-bold">Post-Nom</label><input name="post_nom" class="form-control"></div>
                            <div class="mb-3"><label class="form-label small fw-bold">Prénom</label><input name="prenom" class="form-control" required></div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Photo</label>
                                <input type="file" name="photo" accept="image/*" class="form-control">
                                <img id="previewPhoto" style="display:none;max-width:100%;margin-top:10px;border-radius:10px;">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Fonction</label>
                                <select name="id_fonction" class="form-select">
                                    <option value="">Choisir...</option>
                                    <?php
                                    $r = $conn->query("SELECT id_fonction, designation FROM fonction ORDER BY designation");
                                    foreach ($r->fetchAll(PDO::FETCH_ASSOC) as $row) {
                                        echo "<option value=\"".htmlspecialchars($row['id_fonction'])."\">".htmlspecialchars($row['designation'])."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="d-grid"><button class="btn btn-primary py-2 fw-bold" type="submit">Enregistrer l'agent</button></div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h5 class="card-title fw-bold mb-0"><i class="bi bi-people me-2 text-primary"></i>Liste des Agents</h5>
                            <span class="badge bg-primary rounded-pill px-3" id="agentCount">0</span>
                        </div>
                        <div class="row g-3">
                            <?php
                            try {
                                $sql = "SELECT a.*, f.designation AS fonction_label FROM T_Agents a 
                                        LEFT JOIN T_Fonction f ON a.id_fonction = f.id_fonction 
                                        ORDER BY a.id_agent DESC";
                                $agents = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                                echo "<script>document.getElementById('agentCount').innerText = ".count($agents).";</script>";

                                foreach ($agents as $a):
                                    $imgSrc = ($a['photo'] && file_exists(__DIR__ . '/' . $a['photo'])) ? htmlspecialchars($a['photo']) : 'https://via.placeholder.com/80';
                            ?>
                            <div class="col-md-6 col-xl-4">
                                <div class="card h-100 agent-card p-3 shadow-sm border-0">
                                    <div class="d-flex gap-3 align-items-center">
                                        <img src="<?= $imgSrc ?>" style="width:70px;height:70px;object-fit:cover;border-radius:10px;">
                                        <div class="overflow-hidden">
                                            <h6 class="mb-0 text-truncate fw-bold"><?= htmlspecialchars($a['nom'].' '.$a['post_nom']) ?></h6>
                                            <small class="text-primary"><?= htmlspecialchars($a['prenom']) ?></small><br>
                                            <small class="text-muted small">Mat: <?= htmlspecialchars($a['matricule']) ?></small>
                                        </div>
                                    </div>
                                    <div class="mt-3 d-flex justify-content-between align-items-end pt-2 border-top">
                                        <?php if (!empty($a['card_image'])): ?>
                                            <a href="<?= htmlspecialchars($a['card_image']) ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">Carte</a>
                                        <?php endif; ?>
                                        <img src="qrcodes/<?= htmlspecialchars($a['matricule']) ?>.png" style="width:45px;height:45px;">
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; } catch (Exception $e) { echo "Erreur"; } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center text-muted small py-4 border-top mt-4">
            <i class="bi bi-shield-check me-1"></i>© 2025 - Système de gestion du personnel
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const form = document.getElementById('agentForm');
    const preview = document.getElementById('previewPhoto');
    form.querySelector('input[name="photo"]')?.addEventListener('change', function () {
        const f = this.files[0];
        if (f) {
            preview.src = URL.createObjectURL(f);
            preview.style.display = 'block';
        }
    });
</script>
</body>
</html>
