CREATE DATABASE IF NOT EXISTS portfolio_db;
USE portfolio_db;

CREATE TABLE roles (
    role_id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) NOT NULL
);

CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    role_id INT,
    is_hidden BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id)
);

-- Вставляем базовые роли
INSERT INTO roles (role_name) VALUES 
('admin'),
('user'); 

-- Таблица для постов портфолио
CREATE TABLE portfolio_posts (
    post_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    category VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Таблица для файлов
CREATE TABLE post_files (
    file_id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    file_path VARCHAR(255) COMMENT 'Imgur URL',
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES portfolio_posts(post_id)
);

-- Таблица для категорий
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT
);

-- Добавляем базовые категории
INSERT INTO categories (name, description) VALUES 
('Проекты', 'Личные и рабочие проекты'),
('Достижения', 'Награды, сертификаты и достижения'),
('Образование', 'Образование и курсы'),
('Опыт работы', 'Опыт работы и стажировки'),
('Другое', 'Прочие материалы'); 

-- Добавляем поле is_hidden в таблицу users
ALTER TABLE users ADD COLUMN is_hidden BOOLEAN DEFAULT FALSE; 