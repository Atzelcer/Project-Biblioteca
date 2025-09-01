-- =========================================================
-- Base de datos Biblioteca - esquema + datos de ejemplo
-- Compatible con MariaDB/MySQL y phpMyAdmin (sin procedures)
-- =========================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
/*!40101 SET NAMES utf8mb4 */;

DROP DATABASE IF EXISTS `biblioteca_bd`;
CREATE DATABASE `biblioteca_bd` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `biblioteca_bd`;

-- =========================
-- Tablas
-- =========================

-- Categorías
CREATE TABLE categorias (
  id_categoria INT AUTO_INCREMENT PRIMARY KEY,
  slug  VARCHAR(16)  NOT NULL UNIQUE,
  nombre VARCHAR(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Libros (con descripción y cantidad)
CREATE TABLE libros (
  id_libro      INT AUTO_INCREMENT PRIMARY KEY,
  codigo_libro  VARCHAR(20) NOT NULL UNIQUE,
  nombre        VARCHAR(120) NOT NULL,
  autor         VARCHAR(120) NOT NULL,
  descripcion   VARCHAR(255) NOT NULL,
  estado        ENUM('Disponible','Reservado','No disponible') NOT NULL DEFAULT 'Disponible',
  cantidad      INT NOT NULL DEFAULT 3,   -- cantidad de ejemplares de este título
  foto_libro    VARCHAR(255) DEFAULT NULL,
  id_categoria  INT NOT NULL,
  CONSTRAINT fk_libros_categoria FOREIGN KEY (id_categoria)
    REFERENCES categorias(id_categoria) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuarios
CREATE TABLE usuario (
  id_usuario  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  fotografia  VARCHAR(255) DEFAULT NULL,
  CI          INT NOT NULL,
  nombre      VARCHAR(50) NOT NULL,
  apellido    VARCHAR(50) NOT NULL,
  rol         ENUM('bibliotecario','lector') NOT NULL DEFAULT 'lector',
  celular     INT DEFAULT NULL,
  password    VARCHAR(255) NOT NULL,
  direccion   VARCHAR(100) DEFAULT NULL,
  correo      VARCHAR(100) NOT NULL,
  estado      ENUM('bloqueado','normal') NOT NULL DEFAULT 'normal',
  UNIQUE KEY uq_usuario_ci (CI),
  UNIQUE KEY uq_usuario_correo (correo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Reservas
CREATE TABLE reservas (
  id_reserva            INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario            INT UNSIGNED NOT NULL,
  id_libro              INT NOT NULL,
  fichaReserva          VARCHAR(10) NOT NULL UNIQUE,
  fechaReserva          DATETIME DEFAULT NULL,
  fechaDevolucion       DATETIME DEFAULT NULL,
  fechaDevolucionReal   DATETIME DEFAULT NULL,
  estado                ENUM('pendiente','autorizada','finalizada','cancelada') NOT NULL DEFAULT 'pendiente',
  CONSTRAINT fk_reservas_usuario  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_reservas_libro    FOREIGN KEY (id_libro)   REFERENCES libros(id_libro)   ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Historial de eventos de reservas
CREATE TABLE historial (
  id_historial INT AUTO_INCREMENT PRIMARY KEY,
  id_reserva   INT NOT NULL,
  id_usuario   INT UNSIGNED NOT NULL,
  id_libro     INT NOT NULL,
  accion       ENUM('reservado','autorizado','devuelto','cancelado','extendido') NOT NULL,
  fecha        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  nota         VARCHAR(255) DEFAULT NULL,
  CONSTRAINT fk_hist_reserva FOREIGN KEY (id_reserva) REFERENCES reservas(id_reserva) ON DELETE CASCADE,
  CONSTRAINT fk_hist_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
  CONSTRAINT fk_hist_libro   FOREIGN KEY (id_libro)   REFERENCES libros(id_libro)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- Catálogo: Categorías (12 + Otros)
-- =========================
INSERT INTO categorias (slug,nombre) VALUES
('PROG','Programación'),
('MATH','Matemáticas'),
('PHYS','Física'),
('HIST','Historia'),
('ENG','Idiomas'),
('ARQ','Arquitectura'),
('CHEM','Química'),
('LIT','Literatura'),
('INGE','Ingeniería'),
('ELEC','Electrónica'),
('BIO','Biología'),
('ARTE','Arte'),
('OTR','Otros');

-- =========================
-- Libros (≥ 5 por categoría, 60+ registros)
-- Nota: asumimos que las portadas existen en /public/images/books
--       con los nombres dados.
-- =========================

-- PROG (Programación)
INSERT INTO libros (codigo_libro,nombre,autor,descripcion,estado,cantidad,foto_libro,id_categoria) VALUES
('PROG001','Estructura de Datos con C','Luis Joyanes Aguilar','Aborda estructuras lineales y no lineales, algoritmos y análisis básico aplicado en C.', 'Disponible',6,'Estructura_de_Datos_con_C.jpg', (SELECT id_categoria FROM categorias WHERE slug='PROG')),
('PROG002','Fundamentos de Programacion','Kenneth A. Lambert','Conceptos esenciales de programación estructurada y diseño modular para principiantes.', 'Disponible',8,'Fundamentos_de_Programacion.jpg',(SELECT id_categoria FROM categorias WHERE slug='PROG')),
('PROG003','Clean Code','Robert C. Martin','Buenas prácticas para escribir código legible, mantenible y orientado a la calidad.', 'Reservado',4,'Clean_Code.jpg',(SELECT id_categoria FROM categorias WHERE slug='PROG')),
('PROG004','Eloquent JavaScript','Marijn Haverbeke','Introducción moderna a JavaScript con ejemplos prácticos y ejercicios guiados.', 'Disponible',7,'Eloquent_JavaScript.jpg',(SELECT id_categoria FROM categorias WHERE slug='PROG')),
('PROG005','Python para Todos','Charles Severance','Curso introductorio de Python aplicado a datos, archivos, redes y servicios web.', 'Disponible',9,'Python_para_Todos.jpg',(SELECT id_categoria FROM categorias WHERE slug='PROG')),
('PROG006','The Pragmatic Programmer','Andrew Hunt, David Thomas','Consejos pragmáticos para desarrolladores sobre diseño, herramientas y carrera profesional.', 'No disponible',5,'The_Pragmatic_Programmer.jpg',(SELECT id_categoria FROM categorias WHERE slug='PROG')),
('PROG007','Java The Complete Reference','Herbert Schildt','Guía integral de Java: sintaxis, API, ejemplos prácticos y mejores prácticas.', 'Disponible',6,'Java_The_Complete_Reference.jpg',(SELECT id_categoria FROM categorias WHERE slug='PROG')),
('PROG008','Learning Python','Mark Lutz','Manual extenso de Python: lenguaje, bibliotecas, patrones y aplicaciones prácticas.', 'Disponible',5,'Learning_Python.jpg',(SELECT id_categoria FROM categorias WHERE slug='PROG')),
('PROG009','Design Patterns','Gamma, Helm, Johnson, Vlissides','Patrones de diseño clásicos para crear software flexible, reutilizable y mantenible.', 'Reservado',4,'Design_Patterns.jpg',(SELECT id_categoria FROM categorias WHERE slug='PROG')),
('PROG010','Introduction to Algorithms','Cormen, Leiserson, Rivest, Stein','Tratado formal de algoritmos: análisis, diseño y estructuras de datos avanzadas.', 'Reservado',3,'Introduction_to_Algorithms.jpg',(SELECT id_categoria FROM categorias WHERE slug='PROG')),
('PROG011','Code Complete','Steve McConnell','Construcción de software efectiva: diseño, depuración, pruebas y calidad del código.', 'Reservado',4,'Code_Complete.jpg',(SELECT id_categoria FROM categorias WHERE slug='PROG')),
('PROG012','You Dont Know JS','Kyle Simpson','Serie que profundiza en el núcleo de JavaScript, su modelo y trampas comunes.', 'Disponible',6,'You_Dont_Know_JS.jpg',(SELECT id_categoria FROM categorias WHERE slug='PROG')),
('PROG013','Effective Java','Joshua Bloch','Mejores prácticas y patrones de uso de Java para escribir código robusto.', 'No disponible',2,'Effective_Java.jpg',(SELECT id_categoria FROM categorias WHERE slug='PROG')),
('PROG014','Head First Design Patterns','Eric Freeman','Patrones explicados de manera visual y práctica con ejemplos en Java.', 'Disponible',5,'Head_First_Design_Patterns.jpg',(SELECT id_categoria FROM categorias WHERE slug='PROG')),
('PROG015','Refactoring','Martin Fowler','Técnicas para mejorar el diseño interno sin cambiar el comportamiento externo.', 'Disponible',7,'Refactoring.jpg',(SELECT id_categoria FROM categorias WHERE slug='PROG')),
('PROG016','Clean Architecture','Robert C. Martin','Principios de arquitectura limpia: capas, independencia y separación de responsabilidades.', 'Reservado',4,'Clean_Architecture.jpg',(SELECT id_categoria FROM categorias WHERE slug='PROG')),
('PROG017','Cracking the Coding Interview','Gayle McDowell','Preparación para entrevistas técnicas con preguntas, soluciones y estrategias.', 'Reservado',3,'Cracking_the_Coding_Interview.jpg',(SELECT id_categoria FROM categorias WHERE slug='PROG')),
('PROG018','JavaScript The Good Parts','Douglas Crockford','Selección de las mejores características del lenguaje JavaScript clásico.', 'No disponible',3,'JavaScript_The_Good_Parts.jpg',(SELECT id_categoria FROM categorias WHERE slug='PROG')),
('PROG019','Python Crash Course','Eric Matthes','Introducción práctica a Python centrada en proyectos reales y ejercicios.', 'Disponible',8,'Python_Crash_Course.jpg',(SELECT id_categoria FROM categorias WHERE slug='PROG')),
('PROG020','Automate the Boring Stuff','Al Sweigart','Automatización de tareas cotidianas con Python: archivos, hojas de cálculo y web.', 'No disponible',5,'Automate_the_Boring_Stuff.jpg',(SELECT id_categoria FROM categorias WHERE slug='PROG'));

-- MATH (Matemáticas)
INSERT INTO libros (codigo_libro,nombre,autor,descripcion,estado,cantidad,foto_libro,id_categoria) VALUES
('MATH001','Álgebra Lineal y sus Aplicaciones','David C. Lay','Espacios vectoriales, transformaciones lineales y aplicaciones computacionales modernas.', 'Reservado',4,'Algebra_Lineal_y_sus_Aplicaciones.jpg',(SELECT id_categoria FROM categorias WHERE slug='MATH')),
('MATH002','Cálculo Infinitesimal','Michael Spivak','Fundamentos rigurosos del cálculo diferencial e integral con enfoque teórico.', 'No disponible',3,'Calculo_Infinitesimal.jpg',(SELECT id_categoria FROM categorias WHERE slug='MATH')),
('MATH003','Introducción a la Teoría de Números','G.H. Hardy','Propiedades de los enteros, divisibilidad, primos y resultados clásicos.', 'Reservado',3,'Introduccion_a_la_Teoria_de_Numeros.jpg',(SELECT id_categoria FROM categorias WHERE slug='MATH')),
('MATH004','Análisis Matemático','Tom M. Apostol','Curso completo de cálculo y análisis con pruebas formales y ejercicios.', 'Reservado',4,'Analisis_Matematico.jpg',(SELECT id_categoria FROM categorias WHERE slug='MATH')),
('MATH005','Cálculo de Variable Compleja','James Ward Brown','Funciones complejas, integración, series y aplicaciones físicas.', 'No disponible',3,'Calculo_de_Variable_Compleja.jpg',(SELECT id_categoria FROM categorias WHERE slug='MATH')),
('MATH006','Álgebra Abstracta','David S. Dummit','Grupos, anillos y campos con numerosos ejemplos y problemas resueltos.', 'Reservado',3,'Algebra_Abstracta.jpg',(SELECT id_categoria FROM categorias WHERE slug='MATH')),
('MATH007','Ecuaciones Diferenciales','Dennis G. Zill','Métodos y aplicaciones de ecuaciones diferenciales ordinarias y series.', 'Reservado',3,'Ecuaciones_Diferenciales.jpg',(SELECT id_categoria FROM categorias WHERE slug='MATH')),
('MATH008','Probabilidad y Estadística','Ronald E. Walpole','Modelos probabilísticos, inferencia estadística y diseño de experimentos.', 'Disponible',6,'Probabilidad_y_Estadistica.jpg',(SELECT id_categoria FROM categorias WHERE slug='MATH')),
('MATH009','Geometría Analítica','Charles H. Lehmann','Analítica de la recta, cónicas y superficies con enfoque geométrico.', 'Disponible',5,'Geometria_Analitica.jpg',(SELECT id_categoria FROM categorias WHERE slug='MATH')),
('MATH010','Topología General','John L. Kelley','Conceptos de topología: bases, continuidad, compacidad y separación.', 'No disponible',2,'Topologia_General.jpg',(SELECT id_categoria FROM categorias WHERE slug='MATH')),
('MATH011','Análisis Real','Walter Rudin','Tratado riguroso sobre análisis real: medida, integración y series.', 'Reservado',3,'Analisis_Real.jpg',(SELECT id_categoria FROM categorias WHERE slug='MATH')),
('MATH012','Álgebra Lineal Numérica','Lloyd N. Trefethen','Cálculo numérico para sistemas lineales, autovalores y estabilidad.', 'Reservado',3,'Algebra_Lineal_Numerica.jpg',(SELECT id_categoria FROM categorias WHERE slug='MATH'));

-- PHYS (Física)
INSERT INTO libros (codigo_libro,nombre,autor,descripcion,estado,cantidad,foto_libro,id_categoria) VALUES
('PHYS001','Física Universitaria','Hugh D. Young','Curso completo de física clásica con problemas y aplicaciones de ingeniería.', 'Disponible',7,'Fisica_Universitaria.jpg',(SELECT id_categoria FROM categorias WHERE slug='PHYS')),
('PHYS002','Mecánica Clásica','Herbert Goldstein','Formulación lagrangiana y hamiltoniana para sistemas complejos y avanzados.', 'Disponible',4,'Mecanica_Clasica.jpg',(SELECT id_categoria FROM categorias WHERE slug='PHYS')),
('PHYS003','Electromagnetismo','David J. Griffiths','Campos eléctricos y magnéticos, ondas y aplicaciones modernas.', 'No disponible',3,'Electromagnetismo.jpg',(SELECT id_categoria FROM categorias WHERE slug='PHYS')),
('PHYS004','Termodinámica','Mark W. Zemansky','Leyes de la termodinámica, ciclos y fenómenos macroscópicos de la materia.', 'Disponible',5,'Termodinamica.jpg',(SELECT id_categoria FROM categorias WHERE slug='PHYS')),
('PHYS005','Óptica','Eugene Hecht','Teoría y aplicaciones de fenómenos ópticos, instrumentos y tecnología.', 'Reservado',3,'Optica.jpg',(SELECT id_categoria FROM categorias WHERE slug='PHYS')),
('PHYS006','Física Moderna','Kenneth S. Krane','Relatividad, mecánica cuántica básica y física nuclear introductoria.', 'Disponible',5,'Fisica_Moderna.jpg',(SELECT id_categoria FROM categorias WHERE slug='PHYS'));

-- HIST (Historia)
INSERT INTO libros (codigo_libro,nombre,autor,descripcion,estado,cantidad,foto_libro,id_categoria) VALUES
('HIST001','Historia Universal','William J. Duiker','Panorama global de procesos históricos claves desde la antigüedad hasta hoy.', 'Disponible',6,'Historia_Universal.jpg',(SELECT id_categoria FROM categorias WHERE slug='HIST')),
('HIST002','Historia de América Latina','Leslie Bethell','Ensayos sobre procesos políticos, sociales y culturales latinoamericanos.', 'Reservado',3,'Historia_de_America_Latina.jpg',(SELECT id_categoria FROM categorias WHERE slug='HIST')),
('HIST003','Historia del Arte','Ernst Gombrich','Recorrido claro por movimientos artísticos y obras fundamentales.', 'Disponible',4,'Historia_del_Arte.jpg',(SELECT id_categoria FROM categorias WHERE slug='HIST')),
('HIST004','Historia Contemporánea','John Roberts','Transformaciones del siglo XX: guerras, economía, cultura y globalización.', 'Disponible',4,'Historia_Contemporanea.jpg',(SELECT id_categoria FROM categorias WHERE slug='HIST')),
('HIST005','Antigua Roma','Mary Beard','Vida cotidiana, política y legado cultural del mundo romano clásico.', 'Disponible',4,'Antigua_Roma.jpg',(SELECT id_categoria FROM categorias WHERE slug='HIST'));

-- ENG (Idiomas)
INSERT INTO libros (codigo_libro,nombre,autor,descripcion,estado,cantidad,foto_libro,id_categoria) VALUES
('ENG001','Technical Writing','John M. Lannon','Técnicas para escribir documentos técnicos claros, concisos y efectivos.', 'Disponible',5,'Technical_Writing.jpg',(SELECT id_categoria FROM categorias WHERE slug='ENG')),
('ENG002','English Grammar in Use','Raymond Murphy','Gramática inglesa intermedia con explicaciones simples y ejercicios.', 'No disponible',4,'English_Grammar_in_Use.jpg',(SELECT id_categoria FROM categorias WHERE slug='ENG')),
('ENG003','Business English','David Cotton','Inglés aplicado a contextos de negocios: reuniones, correos y presentaciones.', 'Reservado',3,'Business_English.jpg',(SELECT id_categoria FROM categorias WHERE slug='ENG')),
('ENG004','Oxford English Grammar','Sidney Greenbaum','Manual de gramática avanzada con uso real y ejemplos.', 'Disponible',4,'Oxford_English_Grammar.jpg',(SELECT id_categoria FROM categorias WHERE slug='ENG')),
('ENG005','Cambridge IELTS Practice','Cambridge ESOL','Prácticas de examen IELTS con modelos de respuestas y estrategias.', 'Disponible',4,'Cambridge_IELTS_Practice.jpg',(SELECT id_categoria FROM categorias WHERE slug='ENG'));

-- ARQ (Arquitectura)
INSERT INTO libros (codigo_libro,nombre,autor,descripcion,estado,cantidad,foto_libro,id_categoria) VALUES
('ARQ001','Fundamentos de Arquitectura','Francis D.K. Ching','Principios visuales, composición, escala y lenguaje arquitectónico esencial.', 'Disponible',5,'Fundamentos_de_Arquitectura.jpg',(SELECT id_categoria FROM categorias WHERE slug='ARQ')),
('ARQ002','Historia de la Arquitectura','Spiro Kostof','Evolución de estilos y tipologías desde la antigüedad hasta la actualidad.', 'Disponible',4,'Historia_de_la_Arquitectura.jpg',(SELECT id_categoria FROM categorias WHERE slug='ARQ')),
('ARQ003','Arquitectura Moderna','Kenneth Frampton','Teorías y obras clave del movimiento moderno y sus derivaciones.', 'Disponible',4,'Arquitectura_Moderna.jpg',(SELECT id_categoria FROM categorias WHERE slug='ARQ')),
('ARQ004','Diseño Arquitectónico','Simon Unwin','Métodos de proyecto, análisis espacial y proceso creativo.', 'Reservado',3,'Diseno_Arquitectonico.jpg',(SELECT id_categoria FROM categorias WHERE slug='ARQ')),
('ARQ005','Construcción y Materiales','Edward Allen','Sistemas constructivos, materiales y detalles técnicos fundamentales.', 'Disponible',4,'Construccion_y_Materiales.jpg',(SELECT id_categoria FROM categorias WHERE slug='ARQ'));

-- CHEM (Química)
INSERT INTO libros (codigo_libro,nombre,autor,descripcion,estado,cantidad,foto_libro,id_categoria) VALUES
('CHEM001','Química General','Raymond Chang','Fundamentos atómicos, enlaces, reacciones y soluciones con ejemplos.', 'Disponible',6,'Quimica_General.jpg',(SELECT id_categoria FROM categorias WHERE slug='CHEM')),
('CHEM002','Química Orgánica','John McMurry','Estructura, mecanismos y síntesis con enfoque en reactividad.', 'Disponible',5,'Quimica_Organica.jpg',(SELECT id_categoria FROM categorias WHERE slug='CHEM')),
('CHEM003','Fisicoquímica','Peter Atkins','Termodinámica, cinética y estructura a nivel molecular con rigor.', 'Disponible',4,'Fisicoquimica.jpg',(SELECT id_categoria FROM categorias WHERE slug='CHEM')),
('CHEM004','Química Inorgánica','Gary L. Miessler','Compuestos, simetría y química de coordinación moderna.', 'Disponible',4,'Quimica_Inorganica.jpg',(SELECT id_categoria FROM categorias WHERE slug='CHEM')),
('CHEM005','Química Analítica','Daniel C. Harris','Métodos de análisis cuantitativo, instrumentación y validación.', 'Disponible',4,'Quimica_Analitica.jpg',(SELECT id_categoria FROM categorias WHERE slug='CHEM'));

-- LIT (Literatura)
INSERT INTO libros (codigo_libro,nombre,autor,descripcion,estado,cantidad,foto_libro,id_categoria) VALUES
('LIT001','Don Quijote de la Mancha','Miguel de Cervantes','Novela clásica sobre idealismo, locura y aventuras caballerescas.', 'Reservado',3,'Don_Quijote_de_la_Mancha.jpg',(SELECT id_categoria FROM categorias WHERE slug='LIT')),
('LIT002','Cien Años de Soledad','Gabriel García Márquez','Saga familiar y realismo mágico en el pueblo de Macondo.', 'Reservado',3,'Cien_Anos_de_Soledad.jpg',(SELECT id_categoria FROM categorias WHERE slug='LIT')),
('LIT003','El Principito','Antoine de Saint-Exupery','Fábula poética sobre amistad, amor y sentido de la vida.', 'Reservado',3,'El_Principito.jpg',(SELECT id_categoria FROM categorias WHERE slug='LIT')),
('LIT004','Crónica de una Muerte Anunciada','Gabriel García Márquez','Relato coral sobre destino, honor y presagio inevitable.', 'Reservado',3,'Cronica_de_una_Muerte_Anunciada.jpg',(SELECT id_categoria FROM categorias WHERE slug='LIT')),
('LIT005','Rayuela','Julio Cortázar','Novela experimental que invita a múltiples recorridos de lectura.', 'Disponible',4,'Rayuela.jpg',(SELECT id_categoria FROM categorias WHERE slug='LIT'));

-- INGE (Ingeniería)
INSERT INTO libros (codigo_libro,nombre,autor,descripcion,estado,cantidad,foto_libro,id_categoria) VALUES
('INGE001','Mechanics of Materials','R.C. Hibbeler','Esfuerzos, deformaciones, torsión y flexión en elementos estructurales.', 'Disponible',5,'Mechanics_of_Materials.jpg',(SELECT id_categoria FROM categorias WHERE slug='INGE')),
('INGE002','Fluid Mechanics','F.M. White','Estática y dinámica de fluidos con aplicaciones industriales.', 'Disponible',4,'Fluid_Mechanics.jpg',(SELECT id_categoria FROM categorias WHERE slug='INGE')),
('INGE003','Control Systems Engineering','Norman S. Nise','Modelado, estabilidad y diseño de controladores clásicos y modernos.', 'Reservado',3,'Control_Systems_Engineering.jpg',(SELECT id_categoria FROM categorias WHERE slug='INGE')),
('INGE004','Signals and Systems','Alan V. Oppenheim','Análisis de señales continuas y discretas, transformadas y filtrado.', 'Disponible',4,'Signals_and_Systems.jpg',(SELECT id_categoria FROM categorias WHERE slug='INGE')),
('INGE005','Digital Design','M. Morris Mano','Diseño digital con lógica combinacional y secuencial aplicada.', 'Disponible',4,'Digital_Design.jpg',(SELECT id_categoria FROM categorias WHERE slug='INGE'));

-- ELEC (Electrónica)
INSERT INTO libros (codigo_libro,nombre,autor,descripcion,estado,cantidad,foto_libro,id_categoria) VALUES
('ELEC001','Microelectronic Circuits','Sedra / Smith','Dispositivos, amplificadores y diseño de circuitos analógicos.', 'Disponible',4,'Microelectronic_Circuits.jpg',(SELECT id_categoria FROM categorias WHERE slug='ELEC')),
('ELEC002','Electronic Principles','Albert Malvino','Fundamentos de electrónica analógica con ejemplos prácticos.', 'Disponible',4,'Electronic_Principles.jpg',(SELECT id_categoria FROM categorias WHERE slug='ELEC')),
('ELEC003','Power System Analysis','Hadi Saadat','Modelado y análisis de sistemas de potencia y estabilidad.', 'Reservado',3,'Power_System_Analysis.jpg',(SELECT id_categoria FROM categorias WHERE slug='ELEC')),
('ELEC004','Digital Signal Processing','John G. Proakis','Procesamiento digital: muestreo, filtros y aplicaciones de señal.', 'Disponible',4,'Digital_Signal_Processing.jpg',(SELECT id_categoria FROM categorias WHERE slug='ELEC')),
('ELEC005','Arduino Cookbook','Michael Margolis','Recetas para prototipado rápido con Arduino y sensores.', 'Disponible',5,'Arduino_Cookbook.jpg',(SELECT id_categoria FROM categorias WHERE slug='ELEC'));

-- BIO (Biología)
INSERT INTO libros (codigo_libro,nombre,autor,descripcion,estado,cantidad,foto_libro,id_categoria) VALUES
('BIO001','Biología','Neil A. Campbell','Introducción integral a procesos de la vida desde células a ecosistemas.', 'Disponible',6,'Biologia.jpg',(SELECT id_categoria FROM categorias WHERE slug='BIO')),
('BIO002','Molecular Biology of the Cell','Bruce Alberts','Estructura y función celulares con enfoque molecular detallado.', 'Disponible',4,'Molecular_Biology_of_the_Cell.jpg',(SELECT id_categoria FROM categorias WHERE slug='BIO')),
('BIO003','Genética','Benjamin A. Pierce','Heredabilidad, ADN, genes y técnicas modernas de análisis.', 'Disponible',4,'Genetica.jpg',(SELECT id_categoria FROM categorias WHERE slug='BIO')),
('BIO004','Anatomía Humana','Elaine N. Marieb','Organización del cuerpo humano, sistemas y correlación clínica.', 'Reservado',3,'Anatomia_Humana.jpg',(SELECT id_categoria FROM categorias WHERE slug='BIO')),
('BIO005','Microbiología','Prescott, Harley','Microorganismos, metabolismo, patogenicidad y biotecnología.', 'Disponible',4,'Microbiologia.jpg',(SELECT id_categoria FROM categorias WHERE slug='BIO'));

-- ARTE (Arte)
INSERT INTO libros (codigo_libro,nombre,autor,descripcion,estado,cantidad,foto_libro,id_categoria) VALUES
('ARTE001','Arte Moderno','Norbert Lynton','Movimientos y artistas clave del arte desde finales del siglo XIX.', 'Disponible',4,'Arte_Moderno.jpg',(SELECT id_categoria FROM categorias WHERE slug='ARTE')),
('ARTE002','Teoría del Arte','R.G. Collingwood','Reflexiones filosóficas sobre la naturaleza y función del arte.', 'Disponible',3,'Teoria_del_Arte.jpg',(SELECT id_categoria FROM categorias WHERE slug='ARTE')),
('ARTE003','Historia de la Pintura','Wendy Beckett','Recorrido por técnicas, escuelas y obras maestras de pintura.', 'Disponible',4,'Historia_de_la_Pintura.jpg',(SELECT id_categoria FROM categorias WHERE slug='ARTE')),
('ARTE004','Escultura: Material y Forma','Herbert Read','Evolución de la escultura y su lenguaje formal a través del tiempo.', 'Disponible',3,'Escultura_Material_y_Forma.jpg',(SELECT id_categoria FROM categorias WHERE slug='ARTE')),
('ARTE005','Arte y Percepción Visual','Rudolf Arnheim','Cómo percibimos y comprendemos las composiciones visuales.', 'Disponible',3,'Arte_y_Percepcion_Visual.jpg',(SELECT id_categoria FROM categorias WHERE slug='ARTE'));

-- =========================
-- Usuarios (bibliotecario + lectores)
-- Nota: para nuevos usuarios se define password = SHA1(CI)
-- =========================

INSERT INTO usuario (id_usuario,fotografia,CI,nombre,apellido,rol,celular,password,direccion,correo,estado) VALUES
(1,'1_Maria_Martinez.jpg',12345,'Maria Elena','Antezana','bibliotecario',70123456,'8cb2237d0679ca88db6464eac60da96345513964','Av. Principal 123','admin@gmail.com','normal'),

(2,'7_Pedro_Lopez.jpg',11111,'Juan Carlos','Perez','lector',70234567,'7b21848ac9af35be0ddb2d6b9fc3851934db8420','Calle Los Pinos 456','juan.perez@gmail.com','normal'),
(3,'4_Ana_Gonzalez.jpg',22222,'Ana Maria','Rodriguez','lector',70345678,'1a9b9508b6003b68ddfe03a9c8cbc4bd4388339b','Av. Libertadores 789','ana.rodriguez@gmail.com','normal'),
(4,'10_Diego_Lopez.jpg',33333,'Carlos Alberto','Lopez','lector',70456789,'403d9917c3e950798601addf7ba82cd3c83f344b','Calle San Martin 321','carlos.lopez@gmail.com','normal'),
(5,'5_Carmen_Rodriguez.jpg',44444,'Sofia','Gonzalez','lector',70567890,'3e511da7577d1864871b760ab30e05b56943c9b2','Av. America 654','sofia.gonzalez@gmail.com','normal'),
(6,'8_Jorge_Martinez.jpg',55555,'Diego Fernando','Martinez','lector',70678901,'69df79bef9287d3bcb8f104a408b06de6a108fd8','Calle Bolivar 987','diego.martinez@gmail.com','normal'),
(7,'6_Luis_Vargas.jpg',67890,'Laura Patricia','Vargas','lector',70789012,'230991abcd77e8173edb0af392e1f11120051e29','Calle Murillo 147','laura.vargas@gmail.com','normal'),
(8,'15_Roberto_Lopez.jpg',13579,'Roberto','Silva','lector',70890123,'93ba1608fc10b710894fb9f8c89724c6eeb44d11','Av. 6 de Agosto 258','roberto.silva@gmail.com','normal'),
(9,'12_Carlos_Ramirez.jpg',24680,'Carmen','Torres','lector',70901234,'23fbf23c9201b754adcb2fca0c8e4b19b8a7c740','Calle Comercio 369','carmen.torres@gmail.com','normal'),
(10,'9_Juan_Rodriguez.jpg',35790,'Fernando','Morales','lector',71012345,'c7b47a7beafeaccac334dd6b4b18f5c00b8797ec','Av. Ballivián 741','fernando.morales@gmail.com','normal'),
(11,'11_Jorge_Gonzalez.jpg',46801,'Patricia','Herrera','lector',71123456,'5e8c5c98e7e1dda8a8c61e13ef9c7b5c7b93c5bb','Calle Sucre 852','patricia.herrera@gmail.com','normal'),
(12,'3_Maria_Rodriguez.jpg',57912,'Luis','Mendoza','lector',71234567,'a12b4b24e25eb2f68e3b5c83b6fba2c9c4e8f9af','Av. Arce 963','luis.mendoza@gmail.com','normal'),

-- nuevos (algunos bloqueados). Password = SHA1(CI)
(13,'20_Carlos_Martinez.jpg',60123,'Carlos','Martinez','lector',71311111,SHA1('60123'),'Av. Aroma 101','carlos.martinez@gmail.com','normal'),
(14,'21_Andres_Rodriguez.jpg',60234,'Andres','Rodriguez','lector',71422222,SHA1('60234'),'Calle Junin 55','andres.rodriguez@gmail.com','bloqueado'),
(15,'22_Fernando_Rodriguez.jpg',60345,'Fernanda','Rodriguez','lector',71533333,SHA1('60345'),'Calle Uyuni 88','fernanda.rodriguez@gmail.com','normal'),
(16,'23_Diego_Ramirez.jpg',60456,'Diego','Ramirez','lector',71644444,SHA1('60456'),'Av. Montes 12','diego.ramirez@gmail.com','normal'),
(17,'24_Andres_Perez.jpg',60567,'Andrea','Perez','lector',71755555,SHA1('60567'),'Av. Peru 301','andrea.perez@gmail.com','bloqueado');

-- =========================
-- Reservas (4–5 por lector con estados variados)
-- Fechas de ejemplo en agosto 2025
-- =========================

-- Helper: para referenciar IDs de libros rápidamente (comentarios)
-- PROG019 Python Crash Course -> buscar por codigo
-- MATH011 Análisis Real
-- PHYS001 Física Universitaria
-- LIT005 Rayuela
-- ENG004 Oxford English Grammar
-- ELEC004 Digital Signal Processing
-- INGE002 Fluid Mechanics
-- CHEM003 Fisicoquímica
-- ARQ003 Arquitectura Moderna
-- BIO001 Biología
-- ARTE001 Arte Moderno

-- Usuario 2
INSERT INTO reservas (id_usuario,id_libro,fichaReserva,fechaReserva,fechaDevolucion,fechaDevolucionReal,estado) VALUES
(2,(SELECT id_libro FROM libros WHERE codigo_libro='PROG019'),'JP1A2B','2025-08-12 10:00:00','2025-08-14 10:00:00','2025-08-15 09:00:00','finalizada'),
(2,(SELECT id_libro FROM libros WHERE codigo_libro='MATH011'),'KQ3Z7X','2025-08-16 09:00:00','2025-08-18 09:00:00',NULL,'autorizada'),
(2,(SELECT id_libro FROM libros WHERE codigo_libro='PHYS001'),'L8H2TT','2025-08-20 11:00:00','2025-08-22 11:00:00',NULL,'pendiente'),
(2,(SELECT id_libro FROM libros WHERE codigo_libro='LIT005'),'R9M3QW','2025-08-22 15:00:00','2025-08-24 15:00:00',NULL,'cancelada');

-- Usuario 3
INSERT INTO reservas (id_usuario,id_libro,fichaReserva,fechaReserva,fechaDevolucion,fechaDevolucionReal,estado) VALUES
(3,(SELECT id_libro FROM libros WHERE codigo_libro='ENG004'),'A1B2C3','2025-08-10 10:30:00','2025-08-12 10:30:00','2025-08-12 09:00:00','finalizada'),
(3,(SELECT id_libro FROM libros WHERE codigo_libro='ELEC004'),'E5F6G7','2025-08-14 09:20:00','2025-08-16 09:20:00',NULL,'autorizada'),
(3,(SELECT id_libro FROM libros WHERE codigo_libro='INGE002'),'H8I9J0','2025-08-18 14:10:00','2025-08-20 14:10:00',NULL,'pendiente'),
(3,(SELECT id_libro FROM libros WHERE codigo_libro='CHEM003'),'Z1X2C3','2025-08-22 08:05:00','2025-08-24 08:05:00',NULL,'cancelada');

-- Usuario 4
INSERT INTO reservas (id_usuario,id_libro,fichaReserva,fechaReserva,fechaDevolucion,fechaDevolucionReal,estado) VALUES
(4,(SELECT id_libro FROM libros WHERE codigo_libro='ARQ003'),'QW12ER','2025-08-11 08:00:00','2025-08-13 08:00:00','2025-08-13 08:10:00','finalizada'),
(4,(SELECT id_libro FROM libros WHERE codigo_libro='BIO001'),'TY34UI','2025-08-15 12:00:00','2025-08-17 12:00:00',NULL,'autorizada'),
(4,(SELECT id_libro FROM libros WHERE codigo_libro='PROG007'),'OP56AS','2025-08-19 10:00:00','2025-08-21 10:00:00',NULL,'pendiente'),
(4,(SELECT id_libro FROM libros WHERE codigo_libro='MATH008'),'DF78GH','2025-08-22 16:00:00','2025-08-24 16:00:00',NULL,'pendiente');

-- Usuario 5
INSERT INTO reservas (id_usuario,id_libro,fichaReserva,fechaReserva,fechaDevolucion,fechaDevolucionReal,estado) VALUES
(5,(SELECT id_libro FROM libros WHERE codigo_libro='PHYS006'),'JK12LM','2025-08-12 13:00:00','2025-08-14 13:00:00','2025-08-14 12:40:00','finalizada'),
(5,(SELECT id_libro FROM libros WHERE codigo_libro='LIT003'),'NO34PQ','2025-08-16 15:00:00','2025-08-18 15:00:00',NULL,'autorizada'),
(5,(SELECT id_libro FROM libros WHERE codigo_libro='CHEM005'),'RS56TU','2025-08-21 10:30:00','2025-08-23 10:30:00',NULL,'pendiente'),
(5,(SELECT id_libro FROM libros WHERE codigo_libro='PROG010'),'VW78XY','2025-08-24 09:50:00','2025-08-26 09:50:00',NULL,'cancelada');

-- Usuario 6
INSERT INTO reservas (id_usuario,id_libro,fichaReserva,fechaReserva,fechaDevolucion,fechaDevolucionReal,estado) VALUES
(6,(SELECT id_libro FROM libros WHERE codigo_libro='PROG015'),'AB11CD','2025-08-10 09:00:00','2025-08-12 09:00:00','2025-08-12 08:30:00','finalizada'),
(6,(SELECT id_libro FROM libros WHERE codigo_libro='MATH006'),'EF22GH','2025-08-14 10:00:00','2025-08-16 10:00:00',NULL,'autorizada'),
(6,(SELECT id_libro FROM libros WHERE codigo_libro='ELEC001'),'IJ33KL','2025-08-18 11:00:00','2025-08-20 11:00:00',NULL,'pendiente'),
(6,(SELECT id_libro FROM libros WHERE codigo_libro='ARTE002'),'MN44OP','2025-08-22 12:00:00','2025-08-24 12:00:00',NULL,'pendiente');

-- Usuario 7 (bloqueado puede tener historial previo)
INSERT INTO reservas (id_usuario,id_libro,fichaReserva,fechaReserva,fechaDevolucion,fechaDevolucionReal,estado) VALUES
(7,(SELECT id_libro FROM libros WHERE codigo_libro='INGE003'),'QR55ST','2025-08-09 08:00:00','2025-08-11 08:00:00','2025-08-11 07:40:00','finalizada'),
(7,(SELECT id_libro FROM libros WHERE codigo_libro='PROG003'),'UV66WX','2025-08-13 09:00:00','2025-08-15 09:00:00',NULL,'autorizada'),
(7,(SELECT id_libro FROM libros WHERE codigo_libro='BIO004'),'YZ77A1','2025-08-17 10:00:00','2025-08-19 10:00:00',NULL,'pendiente'),
(7,(SELECT id_libro FROM libros WHERE codigo_libro='MATH004'),'B2C3D4','2025-08-21 11:00:00','2025-08-23 11:00:00',NULL,'cancelada');

-- Usuario 8
INSERT INTO reservas (id_usuario,id_libro,fichaReserva,fechaReserva,fechaDevolucion,fechaDevolucionReal,estado) VALUES
(8,(SELECT id_libro FROM libros WHERE codigo_libro='ENG003'),'E5F6G8','2025-08-11 16:00:00','2025-08-13 16:00:00','2025-08-13 15:30:00','finalizada'),
(8,(SELECT id_libro FROM libros WHERE codigo_libro='CHEM001'),'H7I8J9','2025-08-15 09:30:00','2025-08-17 09:30:00',NULL,'autorizada'),
(8,(SELECT id_libro FROM libros WHERE codigo_libro='PHYS002'),'K1L2M3','2025-08-19 14:50:00','2025-08-21 14:50:00',NULL,'pendiente'),
(8,(SELECT id_libro FROM libros WHERE codigo_libro='LIT001'),'N4O5P6','2025-08-23 12:10:00','2025-08-25 12:10:00',NULL,'pendiente');

-- Usuario 9
INSERT INTO reservas (id_usuario,id_libro,fichaReserva,fechaReserva,fechaDevolucion,fechaDevolucionReal,estado) VALUES
(9,(SELECT id_libro FROM libros WHERE codigo_libro='ARQ005'),'Q7R8S9','2025-08-12 13:20:00','2025-08-14 13:20:00','2025-08-14 12:55:00','finalizada'),
(9,(SELECT id_libro FROM libros WHERE codigo_libro='MATH009'),'T0U1V2','2025-08-16 15:40:00','2025-08-18 15:40:00',NULL,'autorizada'),
(9,(SELECT id_libro FROM libros WHERE codigo_libro='ELEC003'),'W3X4Y5','2025-08-20 10:00:00','2025-08-22 10:00:00',NULL,'pendiente'),
(9,(SELECT id_libro FROM libros WHERE codigo_libro='PROG001'),'Z6A7B8','2025-08-24 09:10:00','2025-08-26 09:10:00',NULL,'cancelada');

-- Usuario 10
INSERT INTO reservas (id_usuario,id_libro,fichaReserva,fechaReserva,fechaDevolucion,fechaDevolucionReal,estado) VALUES
(10,(SELECT id_libro FROM libros WHERE codigo_libro='BIO003'),'C9D0E1','2025-08-10 09:05:00','2025-08-12 09:05:00','2025-08-12 08:40:00','finalizada'),
(10,(SELECT id_libro FROM libros WHERE codigo_libro='ARTE001'),'F2G3H4','2025-08-14 10:15:00','2025-08-16 10:15:00',NULL,'autorizada'),
(10,(SELECT id_libro FROM libros WHERE codigo_libro='PROG014'),'I5J6K7','2025-08-18 11:25:00','2025-08-20 11:25:00',NULL,'pendiente'),
(10,(SELECT id_libro FROM libros WHERE codigo_libro='PHYS003'),'L8M9N0','2025-08-22 12:35:00','2025-08-24 12:35:00',NULL,'pendiente');

-- Usuario 11
INSERT INTO reservas (id_usuario,id_libro,fichaReserva,fechaReserva,fechaDevolucion,fechaDevolucionReal,estado) VALUES
(11,(SELECT id_libro FROM libros WHERE codigo_libro='CHEM004'),'P1Q2R3','2025-08-11 08:30:00','2025-08-13 08:30:00','2025-08-13 08:20:00','finalizada'),
(11,(SELECT id_libro FROM libros WHERE codigo_libro='MATH002'),'S4T5U6','2025-08-15 09:40:00','2025-08-17 09:40:00',NULL,'autorizada'),
(11,(SELECT id_libro FROM libros WHERE codigo_libro='ENG005'),'V7W8X9','2025-08-19 10:50:00','2025-08-21 10:50:00',NULL,'pendiente'),
(11,(SELECT id_libro FROM libros WHERE codigo_libro='LIT004'),'Y0Z1A2','2025-08-23 12:00:00','2025-08-25 12:00:00',NULL,'pendiente');

-- Usuario 12
INSERT INTO reservas (id_usuario,id_libro,fichaReserva,fechaReserva,fechaDevolucion,fechaDevolucionReal,estado) VALUES
(12,(SELECT id_libro FROM libros WHERE codigo_libro='ARQ001'),'B3C4D5','2025-08-12 09:00:00','2025-08-14 09:00:00','2025-08-14 08:30:00','finalizada'),
(12,(SELECT id_libro FROM libros WHERE codigo_libro='INGE004'),'E6F7G8','2025-08-16 10:10:00','2025-08-18 10:10:00',NULL,'autorizada'),
(12,(SELECT id_libro FROM libros WHERE codigo_libro='ELEC005'),'H9I0J1','2025-08-20 11:20:00','2025-08-22 11:20:00',NULL,'pendiente'),
(12,(SELECT id_libro FROM libros WHERE codigo_libro='PROG008'),'K2L3M4','2025-08-24 12:30:00','2025-08-26 12:30:00',NULL,'cancelada');

-- Usuario 13
INSERT INTO reservas (id_usuario,id_libro,fichaReserva,fechaReserva,fechaDevolucion,fechaDevolucionReal,estado) VALUES
(13,(SELECT id_libro FROM libros WHERE codigo_libro='MATH005'),'N5O6P7','2025-08-10 10:00:00','2025-08-12 10:00:00','2025-08-12 09:30:00','finalizada'),
(13,(SELECT id_libro FROM libros WHERE codigo_libro='PROG002'),'Q8R9S0','2025-08-14 11:00:00','2025-08-16 11:00:00',NULL,'autorizada'),
(13,(SELECT id_libro FROM libros WHERE codigo_libro='PHYS006'),'T1U2V3','2025-08-18 12:00:00','2025-08-20 12:00:00',NULL,'pendiente'),
(13,(SELECT id_libro FROM libros WHERE codigo_libro='ARTE005'),'W4X5Y6','2025-08-22 13:00:00','2025-08-24 13:00:00',NULL,'pendiente');

-- Usuario 14 (bloqueado)
INSERT INTO reservas (id_usuario,id_libro,fichaReserva,fechaReserva,fechaDevolucion,fechaDevolucionReal,estado) VALUES
(14,(SELECT id_libro FROM libros WHERE codigo_libro='BIO002'),'Z7A8B9','2025-08-11 08:15:00','2025-08-13 08:15:00','2025-08-13 08:00:00','finalizada'),
(14,(SELECT id_libro FROM libros WHERE codigo_libro='CHEM002'),'C0D1E2','2025-08-15 09:25:00','2025-08-17 09:25:00',NULL,'autorizada'),
(14,(SELECT id_libro FROM libros WHERE codigo_libro='ENG002'),'F3G4H5','2025-08-19 10:35:00','2025-08-21 10:35:00',NULL,'pendiente'),
(14,(SELECT id_libro FROM libros WHERE codigo_libro='MATH003'),'I6J7K8','2025-08-23 11:45:00','2025-08-25 11:45:00',NULL,'cancelada');

-- Usuario 15
INSERT INTO reservas (id_usuario,id_libro,fichaReserva,fechaReserva,fechaDevolucion,fechaDevolucionReal,estado) VALUES
(15,(SELECT id_libro FROM libros WHERE codigo_libro='PROG004'),'L9M0N1','2025-08-12 09:40:00','2025-08-14 09:40:00','2025-08-14 09:00:00','finalizada'),
(15,(SELECT id_libro FROM libros WHERE codigo_libro='ELEC002'),'O2P3Q4','2025-08-16 10:50:00','2025-08-18 10:50:00',NULL,'autorizada'),
(15,(SELECT id_libro FROM libros WHERE codigo_libro='ARQ004'),'R5S6T7','2025-08-20 12:00:00','2025-08-22 12:00:00',NULL,'pendiente'),
(15,(SELECT id_libro FROM libros WHERE codigo_libro='LIT002'),'U8V9W0','2025-08-24 13:10:00','2025-08-26 13:10:00',NULL,'pendiente');

-- Usuario 16
INSERT INTO reservas (id_usuario,id_libro,fichaReserva,fechaReserva,fechaDevolucion,fechaDevolucionReal,estado) VALUES
(16,(SELECT id_libro FROM libros WHERE codigo_libro='PHYS004'),'X1Y2Z3','2025-08-10 09:10:00','2025-08-12 09:10:00','2025-08-12 08:55:00','finalizada'),
(16,(SELECT id_libro FROM libros WHERE codigo_libro='MATH007'),'A3B4C5','2025-08-14 10:20:00','2025-08-16 10:20:00',NULL,'autorizada'),
(16,(SELECT id_libro FROM libros WHERE codigo_libro='ENG005'),'D6E7F8','2025-08-18 11:30:00','2025-08-20 11:30:00',NULL,'pendiente'),
(16,(SELECT id_libro FROM libros WHERE codigo_libro='CHEM001'),'G9H0I1','2025-08-22 12:40:00','2025-08-24 12:40:00',NULL,'pendiente');

-- Usuario 17 (bloqueado)
INSERT INTO reservas (id_usuario,id_libro,fichaReserva,fechaReserva,fechaDevolucion,fechaDevolucionReal,estado) VALUES
(17,(SELECT id_libro FROM libros WHERE codigo_libro='ARTE003'),'J2K3L4','2025-08-11 08:25:00','2025-08-13 08:25:00','2025-08-13 08:15:00','finalizada'),
(17,(SELECT id_libro FROM libros WHERE codigo_libro='PROG011'),'M5N6O7','2025-08-15 09:35:00','2025-08-17 09:35:00',NULL,'autorizada'),
(17,(SELECT id_libro FROM libros WHERE codigo_libro='BIO005'),'P8Q9R0','2025-08-19 10:45:00','2025-08-21 10:45:00',NULL,'pendiente'),
(17,(SELECT id_libro FROM libros WHERE codigo_libro='PHYS005'),'S1T2U3','2025-08-23 11:55:00','2025-08-25 11:55:00',NULL,'cancelada');

-- =========================
-- Historial (un par de eventos por reserva)
-- =========================
INSERT INTO historial (id_reserva,id_usuario,id_libro,accion,fecha,nota)
SELECT r.id_reserva, r.id_usuario, r.id_libro, 'reservado', COALESCE(r.fechaReserva, NOW()), 'Reserva creada'
FROM reservas r;

-- Autorizadas
INSERT INTO historial (id_reserva,id_usuario,id_libro,accion,fecha,nota)
SELECT r.id_reserva, r.id_usuario, r.id_libro, 'autorizado',
       COALESCE(r.fechaReserva, NOW()) + INTERVAL 1 HOUR, 'Reserva autorizada'
FROM reservas r WHERE r.estado IN ('autorizada','finalizada');

-- Finalizadas
INSERT INTO historial (id_reserva,id_usuario,id_libro,accion,fecha,nota)
SELECT r.id_reserva, r.id_usuario, r.id_libro, 'devuelto',
       COALESCE(r.fechaDevolucionReal, r.fechaDevolucion, NOW()),
       'Libro devuelto'
FROM reservas r WHERE r.estado = 'finalizada';

-- Canceladas
INSERT INTO historial (id_reserva,id_usuario,id_libro,accion,fecha,nota)
SELECT r.id_reserva, r.id_usuario, r.id_libro, 'cancelado',
       COALESCE(r.fechaReserva, NOW()) + INTERVAL 2 HOUR,
       'Reserva cancelada por el usuario o biblioteca'
FROM reservas r WHERE r.estado = 'cancelada';

-- =========================
-- Índices (ya incluidos en definiciones) y Auto_increment se ajustan solos
-- =========================

-- FIN
