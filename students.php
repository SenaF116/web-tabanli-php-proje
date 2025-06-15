<?php
require_once '../../config/database.php';

// Öğrenci ekleme işlemi
if (isset($_POST['add_student'])) {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $birth_date = $_POST['birth_date'];
    
    try {
        // Öğrenci tipi ID'sini al
        $stmt = $pdo->prepare("SELECT id FROM user_types WHERE name = 'student'");
        $stmt->execute();
        $user_type_id = $stmt->fetch()['id'];
        
        // Şifre hashleme (varsayılan şifre: student123)
        $password = password_hash('student123', PASSWORD_DEFAULT);
        
        // Öğrenci kaydetme
        $stmt = $pdo->prepare("INSERT INTO users (user_type_id, username, password, name, surname, email, phone) 
                             VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_type_id, strtolower($name . '.' . $surname), $password, $name, $surname, $email, $phone]);
        
        // Öğrenci detayları kaydetme
        $student_id = $pdo->lastInsertId();
        $stmt = $pdo->prepare("INSERT INTO student_details (student_id, address, birth_date) VALUES (?, ?, ?)");
        $stmt->execute([$student_id, $address, $birth_date]);
        
        $success = "Öğrenci başarıyla eklendi!";
    } catch (PDOException $e) {
        $error = "Öğrenci eklenirken bir hata oluştu: " . $e->getMessage();
    }
}

// Öğrenci güncelleme işlemi
if (isset($_POST['update_student'])) {
    $id = $_POST['student_id'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $birth_date = $_POST['birth_date'];
    
    try {
        // Öğrenci bilgilerini güncelle
        $stmt = $pdo->prepare("UPDATE users SET name = ?, surname = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->execute([$name, $surname, $email, $phone, $id]);
        
        // Öğrenci detaylarını güncelle
        $stmt = $pdo->prepare("UPDATE student_details SET address = ?, birth_date = ? WHERE student_id = ?");
        $stmt->execute([$address, $birth_date, $id]);
        
        $success = "Öğrenci başarıyla güncellendi!";
    } catch (PDOException $e) {
        $error = "Öğrenci güncellenirken bir hata oluştu: " . $e->getMessage();
    }
}

// Öğrenci silme işlemi
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        // Önce student_details tablosundan sil
        $stmt = $pdo->prepare("DELETE FROM student_details WHERE student_id = ?");
        $stmt->execute([$id]);
        
        // Sonra users tablosundan sil
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        
        $success = "Öğrenci başarıyla silindi!";
    } catch (PDOException $e) {
        $error = "Öğrenci silinirken bir hata oluştu: " . $e->getMessage();
    }
}

// Öğrencileri listele
$stmt = $pdo->query("SELECT u.*, sd.address, sd.birth_date 
                    FROM users u 
                    LEFT JOIN student_details sd ON u.id = sd.student_id 
                    WHERE u.user_type_id = 3");
$students = $stmt->fetchAll();
?>

<!-- Öğrenci Yönetimi Sayfası -->
<div class="row mb-4">
    <div class="col-12">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
            <i class="fas fa-plus me-2"></i> Yeni Öğrenci Ekle
        </button>
    </div>
</div>

<!-- Öğrenci Listesi -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title mb-4">Öğrenci Listesi</h5>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Ad</th>
                        <th>Soyad</th>
                        <th>E-posta</th>
                        <th>Telefon</th>
                        <th>Doğum Tarihi</th>
                        <th>Adres</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo $student['name']; ?></td>
                            <td><?php echo $student['surname']; ?></td>
                            <td><?php echo $student['email']; ?></td>
                            <td><?php echo $student['phone']; ?></td>
                            <td><?php echo date('d.m.Y', strtotime($student['birth_date'])); ?></td>
                            <td><?php echo $student['address']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editStudentModal" 
                                        onclick="editStudent(<?php echo json_encode($student); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?page=students&delete=<?php echo $student['id']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Bu öğrenciyi silmek istediğinize emin misiniz?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Yeni Öğrenci Ekle Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Öğrenci Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Ad</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Soyad</label>
                        <input type="text" class="form-control" name="surname" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">E-posta</label>
                        <input type="email" class="form-control" name="email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefon</label>
                        <input type="tel" class="form-control" name="phone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Doğum Tarihi</label>
                        <input type="date" class="form-control" name="birth_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Adres</label>
                        <textarea class="form-control" name="address" rows="3"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        <button type="submit" name="add_student" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Öğrenci Düzenle Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Öğrenci Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <input type="hidden" name="student_id" id="student_id">
                    <!-- Form içeriği editStudent fonksiyonu tarafından doldurulacak -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        <button type="submit" name="update_student" class="btn btn-primary">Güncelle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editStudent(student) {
    document.getElementById('student_id').value = student.id;
    document.querySelector('input[name="name"]').value = student.name;
    document.querySelector('input[name="surname"]').value = student.surname;
    document.querySelector('input[name="email"]').value = student.email;
    document.querySelector('input[name="phone"]').value = student.phone;
    document.querySelector('input[name="birth_date"]').value = student.birth_date;
    document.querySelector('textarea[name="address"]').value = student.address;
}
</script>
