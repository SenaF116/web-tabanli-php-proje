<?php
session_start();
require_once 'config/database.php';

// CSRF koruması
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['register'])) {
    // Input doğrulama
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $surname = filter_input(INPUT_POST, 'surname', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $phone = preg_replace('/[^0-9]/', '', $_POST['phone']);
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Şifre doğrulama
    if (strlen($password) < 8) {
        $error = "Şifre en az 8 karakter olmalıdır!";
        exit;
    }
    
    // CSRF kontrolü
    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        die("Geçersiz istek!");
    }
    
    try {
        // Kullanıcı adı kontrolü
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Bu kullanıcı adı zaten kullanılıyor!";
        } else {
            // Şifre hashleme
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Kullanıcı tipi ID'sini al
            $stmt = $pdo->prepare("SELECT id FROM user_types WHERE name = ?");
            $stmt->execute(['student']);
            $user_type_id = $stmt->fetch()['id'];
            
            // Kullanıcı kaydetme
            $stmt = $pdo->prepare("INSERT INTO users (user_type_id, username, password, name, surname, email, phone) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_type_id, $username, $hashed_password, $name, $surname, $email, $phone]);
            
            // Öğrenci detayları kaydetme
            $stmt = $pdo->prepare("INSERT INTO student_details (student_id) VALUES (?)");
            $stmt->execute([$pdo->lastInsertId()]);
            
            header("Location: login.php?success=1");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Registration error: " . $e->getMessage());
        $error = "Kayıt sırasında bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt - Speak It Kurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">Yeni Kayıt</h2>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Kullanıcı Adı</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Şifre</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Ad</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="surname" class="form-label">Soyad</label>
                                <input type="text" class="form-control" id="surname" name="surname" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">E-posta</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Telefon</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" name="register" class="btn btn-primary">Kayıt Ol</button>
                            </div>
                            
                            <div class="text-center mt-3">
                                <a href="login.php">Zaten bir hesabınız var mı? Giriş yapın</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
