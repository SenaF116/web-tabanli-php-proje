-- Tabloları oluşturma (Veritabanı zaten oluşturulmuş olacak);

-- Kullanıcı tipleri tablosu
CREATE TABLE IF NOT EXISTS user_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL
);

-- Kullanıcı tiplerini ekle
INSERT INTO user_types (name) VALUES ('admin'), ('teacher'), ('student');

-- Kullanıcılar tablosu
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_type_id INT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    surname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_type_id) REFERENCES user_types(id)
);

-- Diller tablosu
CREATE TABLE IF NOT EXISTS languages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    code VARCHAR(10) NOT NULL
);

-- Dilleri ekle
INSERT INTO languages (name, code) VALUES 
('İngilizce', 'en'),
('Fransızca', 'fr'),
('İspanyolca', 'es');

-- Ders seviyeleri tablosu
CREATE TABLE IF NOT EXISTS levels (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT
);

-- Ders seviyelerini ekle
INSERT INTO levels (name, description) VALUES 
('Başlangıç', 'Temel dil bilgisi'),
('Orta', 'Gelişmiş dil bilgisi'),
('İleri', 'Uzman dil bilgisi');

-- Dersler tablosu
CREATE TABLE IF NOT EXISTS courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    language_id INT,
    level_id INT,
    teacher_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    start_date DATE,
    end_date DATE,
    duration INT,
    price DECIMAL(10,2),
    FOREIGN KEY (language_id) REFERENCES languages(id),
    FOREIGN KEY (level_id) REFERENCES levels(id),
    FOREIGN KEY (teacher_id) REFERENCES users(id)
);

-- Öğrenci detayları tablosu
CREATE TABLE IF NOT EXISTS student_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    address TEXT,
    birth_date DATE,
    FOREIGN KEY (student_id) REFERENCES users(id)
);

-- Eğitmen detayları tablosu
CREATE TABLE IF NOT EXISTS teacher_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    teacher_id INT,
    education VARCHAR(100),
    experience INT,
    languages TEXT,
    FOREIGN KEY (teacher_id) REFERENCES users(id)
);

-- Öğrenci kayıtları tablosu
CREATE TABLE IF NOT EXISTS student_registrations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    course_id INT,
    registration_date DATE,
    payment_status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

-- Sınavlar tablosu
CREATE TABLE IF NOT EXISTS exams (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT,
    exam_date DATE,
    duration INT,
    total_points INT,
    description TEXT,
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

-- Sınav sonuçları tablosu
CREATE TABLE exam_results (
    id INT PRIMARY KEY AUTO_INCREMENT,
    exam_id INT,
    student_id INT,
    score DECIMAL(5,2),
    notes TEXT,
    FOREIGN KEY (exam_id) REFERENCES exams(id),
    FOREIGN KEY (student_id) REFERENCES users(id)
);

-- Ödeme kayıtları tablosu
CREATE TABLE IF NOT EXISTS payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    registration_id INT,
    amount DECIMAL(10,2),
    payment_date DATE,
    payment_method VARCHAR(50),
    FOREIGN KEY (registration_id) REFERENCES student_registrations(id)
);

-- Sınav sonuçları tablosu
CREATE TABLE exam_results (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    course_id INT,
    exam_date DATE,
    score DECIMAL(5,2),
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

-- Admin hesap oluşturma
INSERT INTO users (user_type_id, username, password, name, surname, email) 
VALUES (1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'Admin', 'admin@speakitkurs.com');

-- Default password: password123
