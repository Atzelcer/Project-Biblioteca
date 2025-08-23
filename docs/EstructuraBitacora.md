# 📚 Sistema de Biblioteca - Documentación de Estructura

## 📋 Información del Proyecto
- **Proyecto**: Sistema de Gestión de Biblioteca
- **Tecnologías**: PHP, JavaScript, HTML, CSS, MySQL
- **Arquitectura**: MVC (Modelo-Vista-Controlador)
- **Fecha de Creación**: 2 de agosto de 2025
- **Autor**: Estudiante USFX - 6to Semestre Arquitectura de Software

---

## 🏗️ Estructura General del Proyecto

```
project-biblioteca/
├── 📁 public/                     # Archivos públicos accesibles desde el navegador
├── 📁 src/                        # Código fuente principal de la aplicación
├── 📁 database/                   # Gestión de base de datos
├── 📁 docs/                       # Documentación del proyecto
├── 📁 tests/                      # Pruebas y testing
├── 📁 vendor/                     # Dependencias externas
└── 📄 EstructuraBitacora.md       # Este archivo de documentación
```

---

## 📁 Descripción Detallada de Carpetas

### 🌐 **public/** - *Directorio Público Web*

**Propósito**: Contiene todos los archivos que son directamente accesibles desde el navegador web.

**¿Por qué existe?**
- **Seguridad**: Separa archivos públicos de código fuente sensible
- **Rendimiento**: Los archivos estáticos se sirven directamente por el servidor web
- **Organización**: Centraliza recursos web en un solo lugar

#### 📂 **public/css/** - *Hojas de Estilo*
- **Contendrá**: 
  - `main.css` - Estilos principales de la aplicación
  - `auth.css` - Estilos específicos para login/registro
  - `dashboard.css` - Estilos del panel administrativo
  - `responsive.css` - Estilos para dispositivos móviles
  - `components.css` - Estilos de componentes reutilizables

#### 📂 **public/js/** - *JavaScript del Cliente*
- **Contendrá**:
  - `main.js` - Funcionalidades JavaScript principales
  - `auth.js` - Validaciones de formularios de autenticación
  - `books.js` - Funciones para gestión de libros (búsqueda, filtros)
  - `loans.js` - Manejo de préstamos y devoluciones
  - `ajax.js` - Peticiones AJAX para interactividad
  - `validation.js` - Validaciones del lado del cliente

#### 📂 **public/images/** - *Recursos Gráficos Estáticos*
- **Contendrá**:
  - `logo.png` - Logo de la biblioteca
  - `favicon.ico` - Icono del navegador
  - `default-book.jpg` - Imagen por defecto para libros sin portada
  - `user-avatar.png` - Avatar por defecto de usuarios
  - Iconos para diferentes categorías de libros

#### 📂 **public/uploads/** - *Archivos Subidos por Usuarios*
- **Contendrá**:
  - Portadas de libros subidas por administradores
  - Fotos de perfil de usuarios
  - Documentos adjuntos (PDFs, etc.)
- **Nota**: Esta carpeta necesitará permisos de escritura

---

### 💻 **src/** - *Código Fuente Principal*

**Propósito**: Contiene toda la lógica de la aplicación, siguiendo el patrón MVC.

#### ⚙️ **src/config/** - *Configuración de la Aplicación*
- **¿Por qué es importante?**
  - Centraliza todas las configuraciones
  - Facilita el cambio entre entornos (desarrollo/producción)
  - Mejora la seguridad al separar credenciales

- **Contendrá**:
  - `database.php` - Configuración de conexión a base de datos
  - `app.php` - Configuraciones generales de la aplicación
  - `auth.php` - Configuraciones de autenticación y sesiones
  - `mail.php` - Configuración para envío de emails
  - `constants.php` - Constantes globales de la aplicación

#### 🎮 **src/controllers/** - *Controladores (Lógica de Negocio)*
- **¿Qué hacen los controladores?**
  - Procesan las peticiones HTTP
  - Coordinan entre modelos y vistas
  - Implementan la lógica de negocio
  - Manejan la validación de datos

- **Contendrá**:
  - `AuthController.php` - Login, logout, registro de usuarios
  - `BookController.php` - CRUD de libros, búsqueda, categorías
  - `UserController.php` - Gestión de usuarios y perfiles
  - `LoanController.php` - Préstamos, devoluciones, renovaciones
  - `DashboardController.php` - Estadísticas y reportes
  - `BaseController.php` - Funcionalidades comunes a todos los controladores

#### 🗃️ **src/models/** - *Modelos (Entidades de Base de Datos)*
- **¿Para qué sirven los modelos?**
  - Representan las entidades del sistema
  - Manejan la interacción con la base de datos
  - Implementan validaciones de datos
  - Definen relaciones entre entidades

- **Contendrá**:
  - `User.php` - Modelo de usuario (estudiantes, bibliotecarios, admin)
  - `Book.php` - Modelo de libro con todas sus propiedades
  - `Author.php` - Modelo de autor de libros
  - `Category.php` - Modelo de categorías de libros
  - `Loan.php` - Modelo de préstamos
  - `Reservation.php` - Modelo de reservas de libros
  - `Database.php` - Clase base para conexión a BD

#### 🖼️ **src/views/** - *Vistas (Interfaz de Usuario)*
- **¿Por qué separar las vistas?**
  - Separa presentación de lógica
  - Facilita el mantenimiento del diseño
  - Permite reutilización de componentes
  - Mejora la colaboración entre desarrolladores y diseñadores

##### 📂 **src/views/auth/** - *Autenticación*
- `login.php` - Formulario de inicio de sesión
- `register.php` - Formulario de registro de usuarios
- `forgot-password.php` - Recuperación de contraseña
- `reset-password.php` - Cambio de contraseña

##### 📂 **src/views/books/** - *Gestión de Libros*
- `index.php` - Lista de todos los libros
- `show.php` - Detalles de un libro específico
- `create.php` - Formulario para agregar nuevo libro
- `edit.php` - Formulario para editar libro existente
- `search.php` - Página de búsqueda avanzada

##### 📂 **src/views/users/** - *Gestión de Usuarios*
- `profile.php` - Perfil del usuario
- `index.php` - Lista de usuarios (solo admin)
- `edit.php` - Editar información de usuario
- `history.php` - Historial de préstamos del usuario

##### 📂 **src/views/loans/** - *Préstamos*
- `index.php` - Lista de préstamos activos
- `create.php` - Formulario para nuevo préstamo
- `history.php` - Historial de préstamos
- `overdue.php` - Lista de préstamos vencidos

##### 📂 **src/views/dashboard/** - *Panel de Administración*
- `index.php` - Dashboard principal con estadísticas
- `reports.php` - Página de reportes
- `statistics.php` - Estadísticas detalladas

##### 📂 **src/views/layouts/** - *Plantillas Base*
- `header.php` - Encabezado común de todas las páginas
- `footer.php` - Pie de página común
- `sidebar.php` - Barra lateral de navegación
- `main.php` - Plantilla principal que incluye header y footer

#### 🔒 **src/middleware/** - *Middleware (Filtros)*
- **¿Qué es middleware?**
  - Código que se ejecuta antes de llegar al controlador
  - Maneja autenticación, autorización, validaciones
  - Implementa filtros y verificaciones

- **Contendrá**:
  - `AuthMiddleware.php` - Verifica si el usuario está autenticado
  - `AdminMiddleware.php` - Verifica permisos de administrador
  - `CSRFMiddleware.php` - Protección contra ataques CSRF
  - `RateLimitMiddleware.php` - Limita número de peticiones

#### 🛠️ **src/utils/** - *Utilidades y Helpers*
- **¿Por qué separar utilidades?**
  - Funciones reutilizables en toda la aplicación
  - Evita duplicación de código
  - Centraliza funcionalidades comunes

- **Contendrá**:
  - `Validator.php` - Funciones de validación
  - `FileUpload.php` - Manejo de subida de archivos
  - `Email.php` - Funciones para envío de emails
  - `Pagination.php` - Funciones de paginación
  - `DateHelper.php` - Manejo de fechas
  - `SecurityHelper.php` - Funciones de seguridad

#### 🛣️ **src/routes/** - *Definición de Rutas*
- **¿Para qué sirven las rutas?**
  - Mapean URLs a controladores específicos
  - Definen qué acción ejecutar para cada petición
  - Organizan la navegación de la aplicación

- **Contendrá**:
  - `web.php` - Rutas principales de la aplicación
  - `api.php` - Rutas para API (si se implementa)
  - `auth.php` - Rutas específicas de autenticación
  - `admin.php` - Rutas del panel administrativo

---

### 🗄️ **database/** - *Gestión de Base de Datos*

**¿Por qué separar los archivos de BD?**
- Mantiene el control de versiones de la estructura
- Facilita la colaboración en equipo
- Permite migraciones automáticas

#### 📂 **database/migrations/** - *Scripts de Migración*
- **Contendrá**:
  - `001_create_users_table.sql` - Tabla de usuarios
  - `002_create_books_table.sql` - Tabla de libros
  - `003_create_authors_table.sql` - Tabla de autores
  - `004_create_categories_table.sql` - Tabla de categorías
  - `005_create_loans_table.sql` - Tabla de préstamos
  - `006_create_reservations_table.sql` - Tabla de reservas

#### 📂 **database/seeders/** - *Datos Iniciales*
- **Contendrá**:
  - `users_seeder.sql` - Usuarios por defecto (admin)
  - `categories_seeder.sql` - Categorías de libros predefinidas
  - `books_seeder.sql` - Libros de ejemplo para testing
  - `demo_data.sql` - Datos de demostración

---

### 📖 **docs/** - *Documentación*

**¿Por qué documentar?**
- Facilita el mantenimiento futuro
- Ayuda a nuevos desarrolladores
- Documenta decisiones arquitectónicas

**Contendrá**:
- `API.md` - Documentación de endpoints
- `INSTALL.md` - Instrucciones de instalación
- `DEPLOYMENT.md` - Guía de despliegue
- `CONTRIBUTING.md` - Guía para contribuidores

---

### 🧪 **tests/** - *Pruebas y Testing*

**¿Por qué hacer pruebas?**
- Asegura calidad del código
- Previene errores en producción
- Facilita refactoring seguro

**Contendrá**:
- `unit/` - Pruebas unitarias de modelos y funciones
- `integration/` - Pruebas de integración
- `functional/` - Pruebas funcionales de la aplicación

---

### 📦 **vendor/** - *Dependencias Externas*

**¿Qué contiene?**
- Librerías de terceros instaladas via Composer
- Frameworks CSS como Bootstrap
- Librerías JavaScript como jQuery

**Nota**: Esta carpeta típicamente se excluye del control de versiones.

---

## 🎯 Beneficios de Esta Estructura

### ✅ **Mantenibilidad**
- Código organizado y fácil de encontrar
- Separación clara de responsabilidades
- Estructura predecible

### ✅ **Escalabilidad**
- Fácil agregar nuevas funcionalidades
- Estructura preparada para crecimiento
- Modular y extensible

### ✅ **Seguridad**
- Archivos sensibles fuera del directorio público
- Separación entre código y assets
- Estructura que facilita implementar medidas de seguridad

### ✅ **Colaboración en Equipo**
- Estructura estándar que cualquier desarrollador puede entender
- Separación que permite trabajo paralelo
- Documentación clara de responsabilidades

### ✅ **Testing y Debugging**
- Estructura que facilita la implementación de pruebas
- Separación que facilita el debugging
- Logs y errores organizados

---

*Este documento será actualizado conforme evolucione el proyecto.*
