-- Tarea Corta 2 -

CREATE DATABASE control_tareas;
USE control_tareas;

CREATE TABLE responsables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    identificacion VARCHAR(20) NOT NULL
);

CREATE TABLE grupos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE tareas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    detalle TEXT NOT NULL,
    prioridad ENUM('Baja', 'Media', 'Alta', 'Urgente') NOT NULL DEFAULT 'Media',
    estado ENUM('Pendiente', 'En progreso', 'Bloqueada', 'Finalizada') NOT NULL DEFAULT 'Pendiente',
    fecha_limite DATE,
    fecha_finalizacion DATETIME,
    id_responsable INT,
    id_grupo INT,
    FOREIGN KEY (id_responsable) REFERENCES responsables(id) ON DELETE SET NULL,
    FOREIGN KEY (id_grupo) REFERENCES grupos(id) ON DELETE SET NULL
);
