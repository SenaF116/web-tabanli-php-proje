<?php
session_start();
require_once 'config/database.php';

// Kullanıcı girişi kontrolü
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Kullanıcı tipi kontrolü
function getUserType() {
    if (isset($_SESSION['user_type'])) {
        return $_SESSION['user_type'];
    }
    return null;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Speak It Kurs - Dil Kurs Merkezi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigasyon Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Speak It Kurs</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="courses.php">Dersler</a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">Profil</a>
                        </li>
                        <?php if (getUserType() == 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="admin/dashboard.php">Yönetim Paneli</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (!isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Giriş</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Kayıt Ol</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Çıkış</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Ana İçerik -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center mb-4">Hoş Geldiniz</h1>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">İngilizce</h5>
                                <p class="card-text">Profesyonel İngilizce eğitimi</p>
                                <a href="courses.php?language=en" class="btn btn-primary">Dersleri Görüntüle</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Fransızca</h5>
                                <p class="card-text">Profesyonel Fransızca eğitimi</p>
                                <a href="courses.php?language=fr" class="btn btn-primary">Dersleri Görüntüle</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">İspanyolca</h5>
                                <p class="card-text">Profesyonel İspanyolca eğitimi</p>
                                <a href="courses.php?language=es" class="btn btn-primary">Dersleri Görüntüle</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light mt-5 py-3">
        <div class="container text-center">
            <p>&copy; 2025 Speak It Kurs. Tüm hakları saklıdır.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

