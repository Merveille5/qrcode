<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>QR Présence avec jsQR</title>
<script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
<style>
body {
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #0f172a, #1e293b);
    color: white;
    text-align: center;
    margin: 0;
    padding: 0;
}
.container {
    margin-top: 50px;
}
h1 {
    font-size: 28px;
    margin-bottom: 20px;
}
#video {
    width: 300px;
    border-radius: 15px;
    border: 4px solid #38bdf8;
    box-shadow: 0 0 20px rgba(56,189,248,0.5);
}
#result {
    margin-top: 20px;
    font-size: 18px;
}
.success {
    color: #22c55e;
}
.error {
    color: #ef4444;
}
.btn {
    margin-top: 20px;
    padding: 10px 20px;
    border: none;
    background: #38bdf8;
    color: black;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
}
canvas {
    display: none; /* on n'affiche pas le canvas, juste pour le scan */
}
</style>
</head>
<body>

<div class="container">
    <h1>Scanner de Présence</h1>
    <video id="video" autoplay playsinline></video>
    <canvas id="canvas"></canvas>
    <p id="result"></p>
    <button class="btn" onclick="startScanner()">Scanner à nouveau</button>
</div>

<script>
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const context = canvas.getContext('2d');
const result = document.getElementById('result');
let scanning = false;

function startScanner() {
    result.textContent = "En attente de scan...";
    result.className = "";
    scanning = true;

    navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
        .then(stream => {
            video.srcObject = stream;
            video.setAttribute("playsinline", true); // iOS compatibilité
            requestAnimationFrame(scanQR);
        })
        .catch(err => {
            console.error("Erreur caméra :", err);
            result.textContent = "Erreur caméra ❌";
            result.className = "error";
        });
}

function scanQR() {
    if (!scanning) return;

    if (video.readyState === video.HAVE_ENOUGH_DATA) {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height);

        if (code) {
            scanning = false; // stop le scan
            video.srcObject.getTracks().forEach(track => track.stop()); // stop la caméra

            const [id_user, token, timestamp] = code.data.split('|');
            result.textContent = "Présence enregistrée ✅"+code.data;
            result.className = "success";
            console.log("QR détecté :", code.data);
        }
    }
    if (scanning) {
            result.textContent = "Aucun QR code détecté...";
            result.className = "error";
            requestAnimationFrame(scanQR);
    }
}

// Démarrage automatique
startScanner();
</script>

</body>
</html>