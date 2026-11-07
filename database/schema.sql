-- DDL базы данных телемедицинской платформы Livio (mysite)
CREATE DATABASE IF NOT EXISTS mysite CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mysite;

-- 1. Пользователи системы (Центральная сущность)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL UNIQUE,
    photo VARCHAR(255) DEFAULT 'icons/default-user.png',
    password VARCHAR(255) NOT NULL,
    clinic VARCHAR(150) NULL,
    oms_photo VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Дневник менструального цикла
CREATE TABLE cycle_diary (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_cycle_date (user_id, date),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 3. Дневник питания и добавленные блюда
CREATE TABLE dishes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    photo VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Записи на прием к врачу
CREATE TABLE doctor_appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    patient_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Результаты обследований сердечно-сосудистой системы
CREATE TABLE heart_exams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    exam_date DATE NOT NULL,
    ecg TEXT NULL,
    echo TEXT NULL,
    mri TEXT NULL,
    ct_angio TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Мониторинг параметров тела (антропометрия)
CREATE TABLE metrics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date DATE NOT NULL,
    weight DECIMAL(5,2) NULL,
    height DECIMAL(5,2) NULL,
    waist DECIMAL(5,2) NULL,
    chest DECIMAL(5,2) NULL,
    hips DECIMAL(5,2) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 7. Заявки на ветеринарные услуги
CREATE TABLE veterinary_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    animal VARCHAR(50) NOT NULL,
    breed VARCHAR(80) NULL,
    pet_info TEXT NULL,
    service_date DATETIME NOT NULL,
    meeting_place VARCHAR(200) NOT NULL,
    wishes TEXT NULL,
    photo VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 8. Мониторинг потребления воды
CREATE TABLE water_intake (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date DATE NOT NULL,
    goal_ml INT DEFAULT 2000,
    consumed_ml INT DEFAULT 0,
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_water_date (user_id, date),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Индексы для оптимизации выборок
CREATE INDEX idx_heart_user_date ON heart_exams(user_id, exam_date);
CREATE INDEX idx_metrics_user_date ON metrics(user_id, date);
CREATE INDEX idx_doctor_app_user ON doctor_appointments(user_id, appointment_date);
CREATE INDEX idx_vet_req_user ON veterinary_requests(user_id, created_at);
