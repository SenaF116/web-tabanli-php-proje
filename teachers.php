<?php
require_once '../../config/database.php';

// Eğitmen ekleme işlemi
if (isset($_POST['add_teacher'])) {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $education = $_POST['education'];
    $experience = $_POST['experience'];
    $languages = $_POST['languages'];
    
    try {
        // Eğitmen tipi ID'sini al
        $stmt = $pdo->prepare("SELECT id FROM user_types WHERE name = 'teacher'");
        $stmt->execute();
        $user_type_id = $stmt->fetch()['id'];
        
        // Şifre hashleme (varsayılan şifre: teacher123)
        $password = password_hash('teacher123', PASSWORD_DEFAULT);
        
        // Eğitmen kaydetme
        $stmt = $pdo->prepare("INSERT INTO users (user_type_id, username, password, name, surname, email, phone) 
                             VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_type_id, strtolower($name . '.' . $surname), $password, $name, $surname, $email, $phone]);
        
        // Eğitmen detayları kaydetme
        $teacher_id = $pdo->lastInsertId();
        $stmt = $pdo->prepare("INSERT INTO teacher_details (teacher_id, education, experience, languages) 
                             VALUES (?, ?, ?, ?)");
        $stmt->execute([$teacher_id, $education, $experience, $languages]);
        
        $success = "Eğitmen başarıyla eklendi!";
    } catch (PDOException $e) {
        $error = "Eğitmen eklenirken bir hata oluştu: " . $e->getMessage();
    }
}

// Eğitmen güncelleme işlemi
if (isset($_POST['update_teacher'])) {
    $id = $_POST['teacher_id'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $education = $_POST['education'];
    $experience = $_POST['experience'];
    $languages = $_POST['languages'];
    
    try {
        // Eğitmen bilgilerini güncelle
        $stmt = $pdo->prepare("UPDATE users SET name = ?, surname = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->execute([$name, $surname, $email, $phone, $id]);
        
        // Eğitmen detaylarını güncelle
        $stmt = $pdo->prepare("UPDATE teacher_details SET education = ?, experience = ?, languages = ? WHERE teacher_id = ?");
        $stmt->execute([$education, $experience, $languages, $id]);
        
        $success = "Eğitmen başarıyla güncellendi!";
    } catch (PDOException $e) {
        $error = "Eğitmen güncellenirken bir hata oluştu: " . $e->getMessage();
    }
}

// Eğitmen silme işlemi
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        // Önce teacher_details tablosundan sil
        $stmt = $pdo->prepare("DELETE FROM teacher_details WHERE teacher_id = ?");
        $stmt->execute([$id]);
        
        // Sonra users tablosundan sil
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        
        $success = "Eğitmen başarıyla silindi!";
    } catch (PDOException $e) {
        $error = "Eğitmen silinirken bir hata oluştu: " . $e->getMessage();
    }
}

// Eğitmenleri listele
$stmt = $pdo->query("SELECT u.*, td.education, td.experience, td.languages 
                    FROM users u 
                    LEFT JOIN teacher_details td ON u.id = td.teacher_id 
                    WHERE u.user_type_id = 2");
$teachers = $stmt->fetchAll();
?>

<!-- Eğitmen Yönetimi Sayfası -->
<div class="row mb-4">
    <div class="col-12">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
            <i class="fas fa-plus me-2"></i> Yeni Eğitmen Ekle
        </button>
    </div>
</div>

<!-- Eğitmen Listesi -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title mb-4">Eğitmen Listesi</h5>
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
                        <th>Eğitim</th>
                        <th>Deneyim</th>
                        <th>Diller</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teachers as $teacher): ?>
                        <tr>
                            <td><?php echo $teacher['name']; ?></td>
                            <td><?php echo $teacher['surname']; ?></td>
                            <td><?php echo $teacher['email']; ?></td>
                            <td><?php echo $teacher['phone']; ?></td>
                            <td><?php echo $teacher['education']; ?></td>
                            <td><?php echo $teacher['experience']; ?> yıl</td>
                            <td><?php echo $teacher['languages']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editTeacherModal" 
                                        onclick="editTeacher(<?php echo json_encode($teacher); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?page=teachers&delete=<?php echo $teacher['id']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Bu eğitmeni silmek istediğinize emin misiniz?')">
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

<!-- Yeni Eğitmen Ekle Modal -->
<div class="modal fade" id="addTeacherModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Eğitmen Ekle</h5>
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
                        <label class="form-label">Eğitim Durumu</label>
                        <input type="text" class="form-control" name="education" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deneyim (yıl)</label>
                        <input type="number" class="form-control" name="experience" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Öğrettiği Diller</label>
                        <input type="text" class="form-control" name="languages" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        <button type="submit" name="add_teacher" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Eğitmen Düzenle Modal -->
<div class="modal fade" id="editTeacherModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eğitmen Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <input type="hidden" name="teacher_id" id="teacher_id">
                    <!-- Form içeriği editTeacher fonksiyonu tarafından doldurulacak -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        <button type="submit" name="update_teacher" class="btn btn-primary">Güncelle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editTeacher(teacher) {
    document.getElementById('teacher_id').value = teacher.id;
    document.querySelector('input[name="name"]').value = teacher.name;
    document.querySelector('input[name="surname"]').value = teacher.surname;
    document.querySelector('input[name="email"]').value = teacher.email;
    document.querySelector('input[name="phone"]').value = teacher.phone;
    document.querySelector('input[name="education"]').value = teacher.education;
    document.querySelector('input[name="experience"]').value = teacher.experience;
    document.querySelector('input[name="languages"]').value = teacher.languages;
}
</script>
