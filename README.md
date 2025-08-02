# 📚 Sistema de Gestión de Biblioteca

## 👥 Equipo de Desarrollo

- **Antezana B. Maria Elena**
- **Cervantes Torres Atzel Alan**
- **Mendez Condori Alex Ramiro**
- **Sanchez Lima Diego Francho**


## 🏗️ Estructura del Proyecto

```
project-biblioteca/
├── 📁 public/                     # Archivos públicos accesibles desde el navegador
│   ├── 📁 css/                    # Hojas de estilo CSS
│   ├── 📁 js/                     # Archivos JavaScript del cliente
│   ├── 📁 images/                 # Imágenes estáticas (logos, iconos)
│   └── 📁 uploads/                # Archivos subidos por usuarios
│
├── 📁 src/                        # Código fuente principal
│   ├── 📁 config/                 # Configuración de la aplicación
│   ├── 📁 controllers/            # Controladores PHP (lógica de negocio)
│   ├── 📁 models/                 # Modelos PHP (entidades de BD)
│   ├── 📁 views/                  # Plantillas HTML
│   │   ├── 📁 auth/               # Vistas de autenticación
│   │   ├── 📁 books/              # Vistas de gestión de libros
│   │   ├── 📁 users/              # Vistas de gestión de usuarios
│   │   ├── 📁 loans/              # Vistas de préstamos
│   │   ├── 📁 dashboard/          # Panel de administración
│   │   └── 📁 layouts/            # Plantillas base
│   ├── 📁 middleware/             # Middleware (autenticación, etc.)
│   ├── 📁 utils/                  # Funciones auxiliares
│   └── 📁 routes/                 # Definición de rutas
│
├── 📁 database/                   # Archivos de base de datos
│   ├── 📁 migrations/             # Scripts de migración
│   └── 📁 seeders/                # Datos iniciales
│
├── 📁 docs/                       # Documentación del proyecto
├── 📁 tests/                      # Pruebas unitarias e integración
└── 📁 vendor/                     # Dependencias de terceros
```

