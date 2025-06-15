<?php
require_once '../../config/database.php';

// Ders ekleme işlemi
if (isset($_POST['add_course'])) {
    $name = $_POST['name'];
    $language = $_POST['language'];
    $level = $_POST['level'];
    $teacher = $_POST['teacher'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $duration = $_POST['duration'];
    $price = $_POST['price'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO courses (name, language_id, level_id, teacher_id, description, start_date, end_date, duration, price) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $language, $level, $teacher, $description, $start_date, $end_date, $duration, $price]);
        $success = "Ders başarıyla eklendi!";
    } catch (PDOException $e) {
        $error = "Ders eklenirken bir hata oluştu: " . $e->getMessage();
    }
}

// Ders güncelleme işlemi
if (isset($_POST['update_course'])) {
    $id = $_POST['course_id'];
    $name = $_POST['name'];
    $language = $_POST['language'];
    $level = $_POST['level'];
    $teacher = $_POST['teacher'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $duration = $_POST['duration'];
    $price = $_POST['price'];
    
    try {
        $stmt = $pdo->prepare("UPDATE courses SET name = ?, language_id = ?, level_id = ?, teacher_id = ?, 
                             description = ?, start_date = ?, end_date = ?, duration = ?, price = ? WHERE id = ?");
        $stmt->execute([$name, $language, $level, $teacher, $description, $start_date, $end_date, $duration, $price, $id]);
        $success = "Ders başarıyla güncellendi!";
    } catch (PDOException $e) {
        $error = "Ders güncellenirken bir hata oluştu: " . $e->getMessage();
    }
}

// Ders silme işlemi
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Ders başarıyla silindi!";
    } catch (PDOException $e) {
        $error = "Ders silinirken bir hata oluştu: " . $e->getMessage();
    }
}

// Dersleri listele
$stmt = $pdo->query("SELECT c.*, l.name as language_name, lv.name as level_name, u.name as teacher_name 
                    FROM courses c 
                    JOIN languages l ON c.language_id = l.id 
                    JOIN levels lv ON c.level_id = lv.id 
                    JOIN users u ON c.teacher_id = u.id");
$courses = $stmt->fetchAll();

// Dil ve seviye seçeneklerini hazırla
$stmt = $pdo->query("SELECT * FROM languages");
$languages = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM levels");
$levels = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM users WHERE user_type_id = 2");
$teachers = $stmt->fetchAll();
?>

<!-- Ders Yönetimi Sayfası -->
<div class="row mb-4">
    <div class="col-12">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
            <i class="fas fa-plus me-2"></i> Yeni Ders Ekle
        </button>
    </div>
</div>

<!-- Ders Listesi -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title mb-4">Ders Listesi</h5>
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
                        <th>Ders Adı</th>
                        <th>Dil</th>
                        <th>Seviye</th>
                        <th>Eğitmen</th>
                        <th>Başlangıç Tarihi</th>
                        <th>Süre</th>
                        <th>Fiyat</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course): ?>
                        <tr>
                            <td><?php echo $course['name']; ?></td>
                            <td><?php echo $course['language_name']; ?></td>
                            <td><?php echo $course['level_name']; ?></td>
                            <td><?php echo $course['teacher_name']; ?></td>
                            <td><?php echo date('d.m.Y', strtotime($course['start_date'])); ?></td>
                            <td><?php echo $course['duration']; ?> hafta</td>
                            <td><?php echo number_format($course['price'], 2); ?> TL</td>
                            <td>
                                <button class="btn btn-sm btn-warning" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editCourseModal" 
                                        onclick="editCourse(<?php echo json_encode($course); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?page=courses&delete=<?php echo $course['id']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Bu dersi silmek istediğinize emin misiniz?')">
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

<!-- Yeni Ders Ekle Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Ders Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Ders Adı</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dil</label>
                        <select class="form-select" name="language" required>
                            <?php foreach ($languages as $lang): ?>
                                <option value="<?php echo $lang['id']; ?>"><?php echo $lang['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Seviye</label>
                        <select class="form-select" name="level" required>
                            <?php foreach ($levels as $level): ?>
                                <option value="<?php echo $level['id']; ?>"><?php echo $level['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Eğitmen</label>
                        <select class="form-select" name="teacher" required>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?php echo $teacher['id']; ?>"><?php echo $teacher['name'] . ' ' . $teacher['surname']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Başlangıç Tarihi</label>
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bitiş Tarihi</label>
                            <input type="date" class="form-control" name="end_date" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Süre (hafta)</label>
                            <input type="number" class="form-control" name="duration" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fiyat (TL)</label>
                            <input type="number" class="form-control" name="price" step="0.01" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        <button type="submit" name="add_course" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Ders Düzenle Modal -->
<div class="modal fade" id="editCourseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dersi Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <input type="hidden" name="course_id" id="course_id">
                    <!-- Form içeriği editCourse fonksiyonu tarafından doldurulacak -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        <button type="submit" name="update_course" class="btn btn-primary">Güncelle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editCourse(course) {
    document.getElementById('course_id').value = course.id;
    document.querySelector('input[name="name"]').value = course.name;
    document.querySelector('select[name="language"]').value = course.language_id;
    document.querySelector('select[name="level"]').value = course.level_id;
    document.querySelector('select[name="teacher"]').value = course.teacher_id;
    document.querySelector('textarea[name="description"]').value = course.description;
    document.querySelector('input[name="start_date"]').value = course.start_date;
    document.querySelector('input[name="end_date"]').value = course.end_date;
    document.querySelector('input[name="duration"]').value = course.duration;
    document.querySelector('input[name="price"]').value = course.price;
}
</script>
