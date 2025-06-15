<?php
require_once '../../config/database.php';

// Ödeme ekleme işlemi
if (isset($_POST['add_payment'])) {
    $registration_id = $_POST['registration_id'];
    $amount = $_POST['amount'];
    $payment_date = $_POST['payment_date'];
    $payment_method = $_POST['payment_method'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO payments (registration_id, amount, payment_date, payment_method) 
                             VALUES (?, ?, ?, ?)");
        $stmt->execute([$registration_id, $amount, $payment_date, $payment_method]);
        
        // Ödeme durumunu güncelle
        $stmt = $pdo->prepare("UPDATE student_registrations SET payment_status = 'completed' WHERE id = ?");
        $stmt->execute([$registration_id]);
        
        $success = "Ödeme başarıyla kaydedildi!";
    } catch (PDOException $e) {
        $error = "Ödeme kaydedilirken bir hata oluştu: " . $e->getMessage();
    }
}

// Ödeme güncelleme işlemi
if (isset($_POST['update_payment'])) {
    $id = $_POST['payment_id'];
    $amount = $_POST['amount'];
    $payment_date = $_POST['payment_date'];
    $payment_method = $_POST['payment_method'];
    
    try {
        $stmt = $pdo->prepare("UPDATE payments SET amount = ?, payment_date = ?, payment_method = ? WHERE id = ?");
        $stmt->execute([$amount, $payment_date, $payment_method, $id]);
        
        $success = "Ödeme başarıyla güncellendi!";
    } catch (PDOException $e) {
        $error = "Ödeme güncellenirken bir hata oluştu: " . $e->getMessage();
    }
}

// Ödeme silme işlemi
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM payments WHERE id = ?");
        $stmt->execute([$id]);
        
        $success = "Ödeme başarıyla silindi!";
    } catch (PDOException $e) {
        $error = "Ödeme silinirken bir hata oluştu: " . $e->getMessage();
    }
}

// Ödemeleri listele
$stmt = $pdo->query("SELECT p.*, sr.id as registration_id, u.name, u.surname, c.name as course_name 
                    FROM payments p 
                    JOIN student_registrations sr ON p.registration_id = sr.id 
                    JOIN users u ON sr.student_id = u.id 
                    JOIN courses c ON sr.course_id = c.id");
$payments = $stmt->fetchAll();

// Öğrenci kayıtlarını getir
$stmt = $pdo->query("SELECT sr.*, u.name, u.surname, c.name as course_name 
                    FROM student_registrations sr 
                    JOIN users u ON sr.student_id = u.id 
                    JOIN courses c ON sr.course_id = c.id 
                    WHERE sr.payment_status = 'pending'");
$pending_registrations = $stmt->fetchAll();
?>

<!-- Ödeme Yönetimi Sayfası -->
<div class="row mb-4">
    <div class="col-12">
        <h5 class="mb-4">Bekleyen Ödemeler</h5>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Öğrenci</th>
                        <th>Ders</th>
                        <th>Kayıt Tarihi</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending_registrations as $reg): ?>
                        <tr>
                            <td><?php echo $reg['name'] . ' ' . $reg['surname']; ?></td>
                            <td><?php echo $reg['course_name']; ?></td>
                            <td><?php echo date('d.m.Y', strtotime($reg['registration_date'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#addPaymentModal" 
                                        onclick="addPayment(<?php echo json_encode($reg); ?>)">
                                    <i class="fas fa-plus me-2"></i> Ödeme Ekle
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Ödeme Listesi -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title mb-4">Ödeme Listesi</h5>
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
                        <th>Öğrenci</th>
                        <th>Ders</th>
                        <th>Tutar</th>
                        <th>Ödeme Tarihi</th>
                        <th>Ödeme Yöntemi</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?php echo $payment['name'] . ' ' . $payment['surname']; ?></td>
                            <td><?php echo $payment['course_name']; ?></td>
                            <td><?php echo number_format($payment['amount'], 2); ?> TL</td>
                            <td><?php echo date('d.m.Y', strtotime($payment['payment_date'])); ?></td>
                            <td><?php echo $payment['payment_method']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editPaymentModal" 
                                        onclick="editPayment(<?php echo json_encode($payment); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?page=payments&delete=<?php echo $payment['id']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Bu ödemeni silmek istediğinize emin misiniz?')">
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

<!-- Yeni Ödeme Ekle Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Ödeme Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <input type="hidden" name="registration_id" id="registration_id">
                    <div class="mb-3">
                        <label class="form-label">Tutar (TL)</label>
                        <input type="number" class="form-control" name="amount" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ödeme Tarihi</label>
                        <input type="date" class="form-control" name="payment_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ödeme Yöntemi</label>
                        <select class="form-select" name="payment_method" required>
                            <option value="nakit">Nakit</option>
                            <option value="kredi_karti">Kredi Kartı</option>
                            <option value="banka_transferi">Banka Transferi</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        <button type="submit" name="add_payment" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Ödeme Düzenle Modal -->
<div class="modal fade" id="editPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ödeme Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <input type="hidden" name="payment_id" id="payment_id">
                    <!-- Form içeriği editPayment fonksiyonu tarafından doldurulacak -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        <button type="submit" name="update_payment" class="btn btn-primary">Güncelle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function addPayment(registration) {
    document.getElementById('registration_id').value = registration.id;
}

function editPayment(payment) {
    document.getElementById('payment_id').value = payment.id;
    document.querySelector('input[name="amount"]').value = payment.amount;
    document.querySelector('input[name="payment_date"]').value = payment.payment_date;
    document.querySelector('select[name="payment_method"]').value = payment.payment_method;
}
</script>
