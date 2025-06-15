# Speak It Kurs - Kurumsal Eğitim Yönetim Sistemi

## Proje Tanımı
Speak It Kurs, Bursa merkezli bir dil eğitim merkezi için tasarlanan kurumsal eğitim yönetim sistemi olarak geliştirilmiştir. Sistem, eğitim merkezinin tüm eğitim faaliyetlerini, öğrenci yönetimini ve finansal işlemlerini otomatize etmek amacıyla oluşturulmuştur.

## Teknoloji Stack
- **Backend**: PHP (Sürüm 8.0+)
- **Veritabanı**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework**: Bootstrap 5
- **Veri Güvenliği**: PDO ile veritabanı işlemleri, password_hash ile şifreleme

## Sistem Özellikleri

### 1. Kullanıcı Yönetimi
- Çoklu kullanıcı tipi sistemi (Admin, Öğretmen, Öğrenci)
- Güvenli oturum yönetimi
- Şifreleme ile korunan kullanıcı kimlik bilgileri
- Rol bazlı erişim kontrolü

### 2. Ders Yönetimi
- Çoklu dil desteği (İngilizce, Fransızca, İspanyolca)
- Seviye bazlı kurs organizasyonu
- Eğitmen-atama sistemi
- Kurs programlama ve zamanlama
- Kurs kapasite yönetimi
- Sınav sonuçları
- Ders notları

### 3. Öğrenci Yönetimi
- Öğrenci kayıt sistemi
- Kurs kaydı ve takibi
- Ödeme takibi
- Sınav sonuçları yönetimi
- Öğrenci performans takibi

### 4. Ödeme Sistemi
- Online ödeme entegrasyonu
- Ödeme takibi ve raporlama
- Fatura oluşturma
- Ödeme hatırlatmaları

### 5. Sınav Yönetimi
- Sınav programlama
- Sınav sonuçları giriş
- Sınav istatistikleri
- Sınav raporlama

## Veritabanı Şeması
- **Kullanıcılar**: Kullanıcı bilgileri ve kimlik doğrulama
- **Kurslar**: Kurs bilgileri ve programlama
- **Öğrenciler**: Öğrenci bilgileri ve kayıtları
- **Ödemeler**: Ödeme işlemleri ve takibi
- **Sınavlar**: Sınav bilgileri ve sonuçları

## Teknolojiler
- PHP 8.1+
- MySQL
- Bootstrap 5
- jQuery
- Font Awesome

## Kurulum Gereksinimleri
1. PHP 8.0 veya üstü
2. MySQL 5.7 veya üstü
3. Web sunucusu (Apache/Nginx)

## Kurulum Adımları
1. Veritabanı oluşturma:
   ```sql
   CREATE DATABASE speakit_kurs;
   USE speakit_kurs;
   ```
2. Veritabanı şemasını yükleme:
   ```sql
   SOURCE ./install.sql;
   ```
3. Veritabanı bağlantı bilgilerini güncelleme:
   ```php
   // config/database.php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'speakit_kurs');
   ```
4. Web sunucusuna dosyaları yükleyin

## Kullanıcı Tipleri
1. **Admin**: Sistem yöneticisi olarak tüm işlemleri yönetebilir.
2. **Öğretmen**: Kendi kurslarını yönetebilir ve sınav sonuçlarını girebilir.
3. **Öğrenci**: Kurs kaydı yapabilir ve kendi performansını takip edebilir.

## Güvenlik Özellikleri
- Şifreleme ile korunan kullanıcı kimlik bilgileri
- Rol bazlı erişim kontrolü
- Güvenli oturum yönetimi
- PDO ile güvenli veritabanı işlemleri

## Raporlama ve İstatistikler
- Öğrenci performans raporları
- Kurs katılım istatistikleri
- Ödeme takibi raporları
- Sınav başarı oranları

## Ek Özellikler
- Responsive tasarım
- Bootstrap 5 tabanlı kullanıcı arayüzü
- Modern ve kullanıcı dostu arayüz
- Mobil uyumlu tasarım

## Geliştirme Takibi
- Git ile sürüm kontrolü
- Dokümantasyon
- Test senaryoları
- Geliştirme planı

## İletişim
Projenin geliştiricisi ile iletişime geçmek için:
- E-posta: developer@example.com
- Web: www.speakitkurs.com

## Lisans
Bu proje MIT lisansı altında lisanslanmıştır. Detaylı bilgi için LICENSE dosyasını inceleyin.

## Ekran Görüntüleri
![Ana Sayfa](screenshots/home.png)
![Admin Paneli](screenshots/admin.png)
![Öğrenci Paneli](screenshots/student.png)

## Video Demo
[YouTube Video Link](https://youtube.com/your-video-id)
