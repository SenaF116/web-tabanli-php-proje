<?php
require_once '../../config/database.php';

// İstatistikleri al
try {
    // Toplam öğrenci sayısı
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE user_type_id = 3");
    $total_students = $stmt->fetch()['total'];

    // Toplam eğitmen sayısı
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE user_type_id = 2");
    $total_teachers = $stmt->fetch()['total'];

    // Aktif ders sayısı
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM courses WHERE start_date <= CURRENT_DATE AND end_date >= CURRENT_DATE");
    $active_courses = $stmt->fetch()['total'];

    // Bu ayki ödemeler
    $stmt = $pdo->query("SELECT SUM(amount) as total FROM payments WHERE payment_date >= DATE_FORMAT(CURRENT_DATE, '%Y-%m-01')");
    $monthly_income = $stmt->fetch()['total'];

    // En çok kaydedilen diller
    $stmt = $pdo->query("SELECT l.name, COUNT(*) as count 
                        FROM student_registrations sr 
                        JOIN courses c ON sr.course_id = c.id 
                        JOIN languages l ON c.language_id = l.id 
                        GROUP BY l.name 
                        ORDER BY count DESC 
                        LIMIT 3");
    $popular_languages = $stmt->fetchAll();

    // Bu ayki kayıt sayısı
    $stmt = $pdo->query("SELECT COUNT(*) as total 
                        FROM student_registrations 
                        WHERE registration_date >= DATE_FORMAT(CURRENT_DATE, '%Y-%m-01')");
    $monthly_registrations = $stmt->fetch()['total'];

    // En çok kaydedilen seviyeler
    $stmt = $pdo->query("SELECT lv.name, COUNT(*) as count 
                        FROM student_registrations sr 
                        JOIN courses c ON sr.course_id = c.id 
                        JOIN levels lv ON c.level_id = lv.id 
                        GROUP BY lv.name 
                        ORDER BY count DESC 
                        LIMIT 3");
    $popular_levels = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "İstatistikler yüklenirken bir hata oluştu: " . $e->getMessage();
}
?>

<!-- Dashboard İçeriği -->
<div class="row">
    <!-- İstatistik Kartları -->
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Toplam Öğrenci</h5>
                <h2 class="mb-0"><?php echo $total_students; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Toplam Eğitmen</h5>
                <h2 class="mb-0"><?php echo $total_teachers; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">Aktif Dersler</h5>
                <h2 class="mb-0"><?php echo $active_courses; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h5 class="card-title">Bu Ayki Gelir</h5>
                <h2 class="mb-0"><?php echo number_format($monthly_income ?? 0, 2); ?> TL</h2>
            </div>
        </div>
    </div>
</div>

<!-- En Popüler Diller -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">En Popüler Diller</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Dil</th>
                                <th>Kayıt Sayısı</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($popular_languages as $lang): ?>
                                <tr>
                                    <td><?php echo $lang['name']; ?></td>
                                    <td><?php echo $lang['count']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bu Ayki Kayıtlar -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Bu Ayki Kayıtlar</h5>
            </div>
            <div class="card-body">
                <p class="mb-0">Bu ay <?php echo $monthly_registrations; ?> yeni kayıt yapıldı.</p>
            </div>
        </div>
    </div>
</div>

<!-- En Popüler Seviyeler -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">En Popüler Seviyeler</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Seviye</th>
                                <th>Kayıt Sayısı</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($popular_levels as $level): ?>
                                <tr>
                                    <td><?php echo $level['name']; ?></td>
                                    <td><?php echo $level['count']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
