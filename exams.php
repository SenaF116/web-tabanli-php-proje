<?php
require_once '../../config/database.php';

// Sınav ekleme işlemi
if (isset($_POST['add_exam'])) {
    $course_id = $_POST['course_id'];
    $exam_date = $_POST['exam_date'];
    $duration = $_POST['duration'];
    $total_points = $_POST['total_points'];
    $description = $_POST['description'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO exams (course_id, exam_date, duration, total_points, description) 
                             VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$course_id, $exam_date, $duration, $total_points, $description]);
        
        $success = "Sınav başarıyla eklendi!";
    } catch (PDOException $e) {
        $error = "Sınav eklenirken bir hata oluştu: " . $e->getMessage();
    }
}

// Sınav güncelleme işlemi
if (isset($_POST['update_exam'])) {
    $id = $_POST['exam_id'];
    $course_id = $_POST['course_id'];
    $exam_date = $_POST['exam_date'];
    $duration = $_POST['duration'];
    $total_points = $_POST['total_points'];
    $description = $_POST['description'];
    
    try {
        $stmt = $pdo->prepare("UPDATE exams SET course_id = ?, exam_date = ?, duration = ?, 
                             total_points = ?, description = ? WHERE id = ?");
        $stmt->execute([$course_id, $exam_date, $duration, $total_points, $description, $id]);
        
        $success = "Sınav başarıyla güncellendi!";
    } catch (PDOException $e) {
        $error = "Sınav güncellenirken bir hata oluştu: " . $e->getMessage();
    }
}

// Sınav silme işlemi
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM exams WHERE id = ?");
        $stmt->execute([$id]);
        
        $success = "Sınav başarıyla silindi!";
    } catch (PDOException $e) {
        $error = "Sınav silinirken bir hata oluştu: " . $e->getMessage();
    }
}

// Sınav sonuçları ekleme işlemi
if (isset($_POST['add_result'])) {
    $exam_id = $_POST['exam_id'];
    $student_id = $_POST['student_id'];
    $score = $_POST['score'];
    $notes = $_POST['notes'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO exam_results (exam_id, student_id, score, notes) 
                             VALUES (?, ?, ?, ?)");
        $stmt->execute([$exam_id, $student_id, $score, $notes]);
        
        $success = "Sınav sonucu başarıyla eklendi!";
    } catch (PDOException $e) {
        $error = "Sınav sonucu eklenirken bir hata oluştu: " . $e->getMessage();
    }
}

// Sınav sonuçları güncelleme işlemi
if (isset($_POST['update_result'])) {
    $id = $_POST['result_id'];
    $score = $_POST['score'];
    $notes = $_POST['notes'];
    
    try {
        $stmt = $pdo->prepare("UPDATE exam_results SET score = ?, notes = ? WHERE id = ?");
        $stmt->execute([$score, $notes, $id]);
        
        $success = "Sınav sonucu başarıyla güncellendi!";
    } catch (PDOException $e) {
        $error = "Sınav sonucu güncellenirken bir hata oluştu: " . $e->getMessage();
    }
}

// Sınav sonuçları silme işlemi
if (isset($_GET['delete_result'])) {
    $id = $_GET['delete_result'];
    try {
        $stmt = $pdo->prepare("DELETE FROM exam_results WHERE id = ?");
        $stmt->execute([$id]);
        
        $success = "Sınav sonucu başarıyla silindi!";
    } catch (PDOException $e) {
        $error = "Sınav sonucu silinirken bir hata oluştu: " . $e->getMessage();
    }
}

// Sınavlar ve sonuçları listele
$stmt = $pdo->query("SELECT e.*, c.name as course_name, c.language_id, l.name as language_name 
                    FROM exams e 
                    JOIN courses c ON e.course_id = c.id 
                    JOIN languages l ON c.language_id = l.id");
$exams = $stmt->fetchAll();

// Sınav sonuçlarını listele
$stmt = $pdo->query("SELECT er.*, e.exam_date, c.name as course_name, u.name, u.surname 
                    FROM exam_results er 
                    JOIN exams e ON er.exam_id = e.id 
                    JOIN courses c ON e.course_id = c.id 
                    JOIN users u ON er.student_id = u.id");
$results = $stmt->fetchAll();

// Dersleri listele
$stmt = $pdo->query("SELECT * FROM courses");
$courses = $stmt->fetchAll();

// Öğrencileri listele
$stmt = $pdo->query("SELECT * FROM users WHERE user_type_id = 3");
$students = $stmt->fetchAll();
?>

<!-- Sınav Yönetimi Sayfası -->
<div class="row mb-4">
    <div class="col-12">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExamModal">
            <i class="fas fa-plus me-2"></i> Yeni Sınav Ekle
        </button>
    </div>
</div>

<!-- Sınav Listesi -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title mb-4">Sınav Listesi</h5>
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
                        <th>Ders</th>
                        <th>Dil</th>
                        <th>Sınav Tarihi</th>
                        <th>Süre</th>
                        <th>Toplam Puan</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exams as $exam): ?>
                        <tr>
                            <td><?php echo $exam['course_name']; ?></td>
                            <td><?php echo $exam['language_name']; ?></td>
                            <td><?php echo date('d.m.Y', strtotime($exam['exam_date'])); ?></td>
                            <td><?php echo $exam['duration']; ?> dakika</td>
                            <td><?php echo $exam['total_points']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editExamModal" 
                                        onclick="editExam(<?php echo json_encode($exam); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?page=exams&delete=<?php echo $exam['id']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Bu sınavı silmek istediğinize emin misiniz?')">
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

<!-- Sınav Sonuçları -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title mb-4">Sınav Sonuçları</h5>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Öğrenci</th>
                        <th>Ders</th>
                        <th>Sınav Tarihi</th>
                        <th>Puan</th>
                        <th>Notlar</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $result): ?>
                        <tr>
                            <td><?php echo $result['name'] . ' ' . $result['surname']; ?></td>
                            <td><?php echo $result['course_name']; ?></td>
                            <td><?php echo date('d.m.Y', strtotime($result['exam_date'])); ?></td>
                            <td><?php echo $result['score']; ?></td>
                            <td><?php echo $result['notes']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editResultModal" 
                                        onclick="editResult(<?php echo json_encode($result); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?page=exams&delete_result=<?php echo $result['id']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Bu sınav sonucunu silmek istediğinize emin misiniz?')">
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

<!-- Yeni Sınav Ekle Modal -->
<div class="modal fade" id="addExamModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Sınav Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Ders</label>
                        <select class="form-select" name="course_id" required>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?php echo $course['id']; ?>"><?php echo $course['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sınav Tarihi</label>
                        <input type="date" class="form-control" name="exam_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sınav Süresi (dakika)</label>
                        <input type="number" class="form-control" name="duration" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Toplam Puan</label>
                        <input type="number" class="form-control" name="total_points" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        <button type="submit" name="add_exam" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Sınav Düzenle Modal -->
<div class="modal fade" id="editExamModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sınav Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <input type="hidden" name="exam_id" id="exam_id">
                    <!-- Form içeriği editExam fonksiyonu tarafından doldurulacak -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        <button type="submit" name="update_exam" class="btn btn-primary">Güncelle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Sınav Sonucu Ekle Modal -->
<div class="modal fade" id="addResultModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sınav Sonucu Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Sınav</label>
                        <select class="form-select" name="exam_id" required>
                            <?php foreach ($exams as $exam): ?>
                                <option value="<?php echo $exam['id']; ?>">
                                    <?php echo $exam['course_name'] . ' - ' . date('d.m.Y', strtotime($exam['exam_date'])); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Öğrenci</label>
                        <select class="form-select" name="student_id" required>
                            <?php foreach ($students as $student): ?>
                                <option value="<?php echo $student['id']; ?>">
                                    <?php echo $student['name'] . ' ' . $student['surname']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Puan</label>
                        <input type="number" class="form-control" name="score" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notlar</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        <button type="submit" name="add_result" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Sınav Sonucu Düzenle Modal -->
<div class="modal fade" id="editResultModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sınav Sonucu Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <input type="hidden" name="result_id" id="result_id">
                    <!-- Form içeriği editResult fonksiyonu tarafından doldurulacak -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        <button type="submit" name="update_result" class="btn btn-primary">Güncelle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editExam(exam) {
    document.getElementById('exam_id').value = exam.id;
    document.querySelector('select[name="course_id"]').value = exam.course_id;
    document.querySelector('input[name="exam_date"]').value = exam.exam_date;
    document.querySelector('input[name="duration"]').value = exam.duration;
    document.querySelector('input[name="total_points"]').value = exam.total_points;
    document.querySelector('textarea[name="description"]').value = exam.description;
}

function editResult(result) {
    document.getElementById('result_id').value = result.id;
    document.querySelector('input[name="score"]').value = result.score;
    document.querySelector('textarea[name="notes"]').value = result.notes;
}
</script>
