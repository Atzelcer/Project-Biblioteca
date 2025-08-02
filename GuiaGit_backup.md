# 🚀 Guía de Git para Trabajo Remoto - Proyecto Biblioteca

--------------------------
-----



## 📋 Información del Proyecto
- **Proyecto**: Sistema de Gestión de Biblioteca
- **Repositorio**: Project-Biblioteca
- **Rama Principal**: `main`
- **Ramas de Desarrollo**: `feature/cervantes`, `feature/mendez`, `feature/sanchez`, `feature/antezana`
- **Fecha**: Agosto 2025

---

## 🎯 **GUÍA BÁSICA: CÓMO TRABAJAR EN TU RAMA SIN MORIR EN EL INTENTO**

### 📝 **PASO A PASO - PRIMER DÍA DE TRABAJO**

#### **🔧 Configuración inicial (solo una vez)**
```bash
# 1. Configurar tu identidad en Git
git config --global user.name "Tu Nombre Completo"
git config --global user.email "tu.email@ejemplo.com"

# 2. Verificar configuración
git config --list
```

#### **📥 Clonar el repositorio (solo primera vez)**
```bash
# Si aún no tienes el proyecto en tu computadora
git clone https://github.com/Atzelcer/Project-Biblioteca.git
cd Project-Biblioteca
```

#### **🌿 Ir a tu rama de trabajo**
```bash
# Ver todas las ramas disponibles
git branch -a

# Cambiar a TU rama específica
git checkout feature/cervantes    # Si eres Cervantes
git checkout feature/mendez       # Si eres Mendez  
git checkout feature/sanchez      # Si eres Sanchez
git checkout feature/antezana     # Si eres Antezana

# Verificar que estás en la rama correcta
git branch
```

---

## 💼 **RUTINA DIARIA DE TRABAJO - PASO A PASO**

### 🌅 **AL COMENZAR EL DÍA (SIEMPRE HACER ESTO PRIMERO)**

#### **Paso 1: Actualizar el proyecto**
```bash
# 1. Ir a la rama principal
git checkout main

# 2. Descargar los últimos cambios
git pull origin main

# 3. Volver a TU rama
git checkout feature/tu-apellido

# 4. Traer cambios de main a tu rama
git merge main
```

**🤔 ¿Por qué hacer esto?**
- Asegura que tienes la versión más reciente
- Evita conflictos grandes al final
- Mantiene tu rama actualizada con el trabajo del equipo

#### **Paso 2: Verificar estado**
```bash
# Ver en qué rama estás
git branch

# Ver qué archivos están modificados
git status
```

---

### ✍️ **MIENTRAS TRABAJAS (DURANTE EL DÍA)**

#### **Paso 1: Hacer cambios en tus archivos**
- Edita los archivos que te corresponden
- Guarda los cambios en tu editor

#### **Paso 2: Guardar cambios localmente**
```bash
# Ver qué archivos modificaste
git status

# Agregar archivos específicos (RECOMENDADO)
git add src/controllers/AuthController.php
git add src/views/auth/login.php

# O agregar todo (si estás seguro)
git add .

# Hacer commit con mensaje claro
git commit -m "feat: agregar validación de login"
```

**📝 Mensajes de commit recomendados:**
```bash
git commit -m "feat: agregar nueva funcionalidad"
git commit -m "fix: corregir error en búsqueda"
git commit -m "docs: actualizar documentación"
git commit -m "style: mejorar diseño de botones"
```

#### **Paso 3: Subir cambios a tu rama remota**
```bash
# Subir tus cambios a GitHub/GitLab
git push origin feature/tu-apellido

# Si es la primera vez, usar:
git push -u origin feature/tu-apellido
```

---

### 🌙 **AL FINALIZAR EL DÍA**

```bash
# 1. Asegurarse de guardar todo
git add .
git commit -m "docs: avance del día - [descripción breve]"

# 2. Subir cambios
git push origin feature/tu-apellido

# 3. Verificar que se subió correctamente
git status
```

---

## 🔄 **CÓMO FUSIONAR TU TRABAJO A LA RAMA PRINCIPAL**

### 📋 **PREPARAR TU RAMA PARA FUSIÓN**

#### **Paso 1: Asegurar que tu trabajo esté completo**
```bash
# 1. Hacer últimos commits
git add .
git commit -m "feat: completar [nombre de tu módulo]"

# 2. Subir a tu rama
git push origin feature/tu-apellido
```

#### **Paso 2: Actualizar desde main**
```bash
# 1. Ir a main y actualizar
git checkout main
git pull origin main

# 2. Volver a tu rama
git checkout feature/tu-apellido

# 3. Fusionar cambios de main (para evitar conflictos)
git merge main

# 4. Si hay conflictos, resolverlos y hacer commit
git add .
git commit -m "resolve: fusionar cambios de main"

# 5. Subir la rama actualizada
git push origin feature/tu-apellido
```

### 🎯 **FUSIONAR A MAIN (DOS OPCIONES)**

#### **Opción A: Fusión directa (más simple)**
```bash
# 1. Ir a main
git checkout main

# 2. Fusionar tu rama
git merge feature/tu-apellido

# 3. Subir los cambios
git push origin main

# 4. Eliminar tu rama local (opcional)
git branch -d feature/tu-apellido
```

#### **Opción B: Pull Request (RECOMENDADO para equipos)**
```bash
# 1. Tu rama ya debe estar actualizada en GitHub/GitLab
git push origin feature/tu-apellido

# 2. Ir a GitHub/GitLab en el navegador
# 3. Crear Pull Request desde tu rama hacia main
# 4. Esperar revisión del equipo
# 5. Una vez aprobado, hacer merge desde la interfaz web
```

---

## 🆘 **COMANDOS DE RESCATE BÁSICOS**

### 😱 **"¡Metí la pata! ¿Cómo lo arreglo?"**

#### **Caso 1: Hice commit pero quiero cambiarlo**
```bash
# Modificar el último commit
git commit --amend -m "mensaje corregido"
```

#### **Caso 2: Quiero deshacer el último commit**
```bash
# Deshacer commit pero mantener cambios
git reset --soft HEAD~1

# Deshacer commit y cambios (¡CUIDADO!)
git reset --hard HEAD~1
```

#### **Caso 3: Quiero deshacer cambios en un archivo**
```bash
# Deshacer cambios no guardados
git checkout -- nombre-archivo.php
```

#### **Caso 4: Estoy en la rama equivocada**
```bash
# Guardar cambios temporalmente
git stash

# Cambiar a la rama correcta
git checkout feature/tu-apellido

# Recuperar los cambios
git stash pop
```

#### **Caso 5: Tengo conflictos en merge**
```bash
# Ver archivos con conflictos
git status

# Editar archivos manualmente (buscar <<<<<<< y >>>>>>>)
# Después de resolver:
git add archivo-resuelto.php
git commit -m "resolve: conflictos resueltos"
```

## ✅ **REGLAS DE ORO PARA NO TENER PROBLEMAS**

### 🟢 **SÍ HACER:**
1. **Siempre trabajar en tu rama**: `git checkout feature/tu-apellido`
2. **Actualizar antes de empezar**: `git pull origin main`
3. **Commits frecuentes**: Cada vez que completes algo
4. **Mensajes claros**: "feat: agregar login" no "cambios"
5. **Push diario**: Subir cambios al final del día
6. **Probar antes de fusionar**: Asegurar que funciona

### 🔴 **NO HACER:**
1. **Trabajar directamente en main**: ¡Siempre usar tu rama!
2. **Commits gigantes**: Mejor varios commits pequeños
3. **Push a main directo**: Siempre revisar antes
4. **Ignorar conflictos**: Resolverlos inmediatamente
5. **Mensajes vagos**: "asdasd" o "cambios" no sirven


### 🤔 **Preguntas frecuentes:**

**P: ¿En qué rama debo trabajar?**
R: Siempre en `feature/tu-apellido`, nunca en `main`

**P: ¿Cuándo debo hacer commit?**
R: Cada vez que completes una funcionalidad pequeña

**P: ¿Cómo sé si estoy en la rama correcta?**
R: Ejecuta `git branch` y verifica que tienes un * en tu rama

**P: ¿Qué hago si tengo conflictos?**
R: Edita los archivos manualmente, busca `<<<<<<<` y resuelve

**P: ¿Puedo trabajar sin internet?**
R: Sí, Git funciona offline. Solo necesitas internet para `push/pull`

---

## 🌿 **COMANDOS BÁSICOS DE NAVEGACIÓN**

## � **COMANDOS ADICIONALES PARA USUARIOS AVANZADOS**

### 🌿 **Navegación básica**
```bash
# Ver estado de archivos (modificados, agregados, etc.)
git status

# Ver en qué rama estás actualmente
git branch

# Ver todas las ramas (locales y remotas)
git branch -a

# Ver historial de commits
git log --oneline
```

### 🔄 **Cambiar entre ramas**
```bash
# Cambiar a una rama específica
git checkout feature/cervantes
git checkout feature/mendez
git checkout feature/sanchez
git checkout feature/antezana

# Volver a la rama principal
git checkout main

# Crear y cambiar a una nueva rama en un solo comando
git checkout -b nueva-rama
```

### 📥 **Actualizar repositorio local**
```bash
# Descargar cambios del repositorio remoto
git fetch origin

# Descargar y fusionar cambios de la rama actual
git pull origin main

# Actualizar todas las ramas
git fetch --all
```

### ❌ **DESHACER COMMITS**

#### **Deshacer el último commit (mantener cambios)**
```bash
# Deshacer commit pero mantener cambios en área de trabajo
git reset --soft HEAD~1

# Deshacer commit y sacar cambios del staging
git reset HEAD~1

# Deshacer commit y ELIMINAR todos los cambios (¡CUIDADO!)
git reset --hard HEAD~1
```

#### **Deshacer múltiples commits**
```bash
# Deshacer los últimos 3 commits
git reset --soft HEAD~3

# Ver el hash del commit al que quieres volver
git log --oneline

# Volver a un commit específico
git reset --soft abc1234
```

### 🔙 **REVERTIR CAMBIOS**

#### **Deshacer cambios en archivo específico**
```bash
# Deshacer cambios no guardados en un archivo
git checkout -- nombre-archivo.php

# Deshacer cambios en todos los archivos
git checkout -- .

# Remover archivo del área de staging
git reset HEAD nombre-archivo.php
```

#### **Revertir un commit específico**
```bash
# Crear un commit que revierte los cambios de otro commit
git revert abc1234

# Revertir múltiples commits
git revert abc1234..def5678
```

### 🔧 **CORREGIR COMMITS**

#### **Modificar el último commit**
```bash
# Cambiar el mensaje del último commit
git commit --amend -m "nuevo mensaje corregido"

# Agregar archivos olvidados al último commit
git add archivo-olvidado.php
git commit --amend --no-edit

# Modificar autor del último commit
git commit --amend --author="Nombre <email@ejemplo.com>"
```

### 🌊 **PROBLEMAS CON MERGE**

#### **Resolver conflictos de merge**
```bash
# Cuando hay conflictos, Git te mostrará los archivos
git status

# Editar archivos manualmente para resolver conflictos
# Buscar marcadores: <<<<<<< ======= >>>>>>>

# Después de resolver conflictos
git add archivo-resuelto.php
git commit -m "resolve: conflictos resueltos en merge"
```

#### **Cancelar un merge**
```bash
# Si el merge salió mal y quieres cancelarlo
git merge --abort

# Volver al estado antes del merge
git reset --hard HEAD~1
```

### 🚫 **PROBLEMAS CON ARCHIVOS**

#### **Ignorar archivos que ya están en el repositorio**
```bash
# Dejar de trackear archivo pero mantenerlo localmente
git rm --cached nombre-archivo.txt

# Dejar de trackear carpeta completa
git rm -r --cached carpeta/

# Después agregar archivo a .gitignore
echo "nombre-archivo.txt" >> .gitignore
git add .gitignore
git commit -m "chore: agregar archivo a .gitignore"
```

#### **Recuperar archivos eliminados**
```bash
# Ver archivos eliminados
git log --diff-filter=D --summary

# Recuperar archivo específico
git checkout HEAD~1 -- archivo-eliminado.php

# Recuperar archivo de un commit específico
git checkout abc1234 -- archivo-eliminado.php
```

---

## 🔀 **TRABAJAR CON RAMAS AVANZADO**

### 🌿 **Gestión de ramas**
```bash
# Crear nueva rama desde la actual
git branch nueva-funcionalidad

# Crear rama desde un commit específico
git branch nueva-rama abc1234

# Eliminar rama local
git branch -d feature/rama-vieja

# Eliminar rama remota
git push origin --delete feature/rama-vieja

# Renombrar rama actual
git branch -m nuevo-nombre

# Ver diferencias entre ramas
git diff main..feature/tu-apellido
```

### 🔄 **Merge vs Rebase**
```bash
# MERGE: Mantiene historial de ramas
git checkout main
git merge feature/tu-apellido

# REBASE: Coloca commits en línea recta
git checkout feature/tu-apellido
git rebase main

# Si hay conflictos durante rebase
git add archivo-resuelto.php
git rebase --continue

# Cancelar rebase
git rebase --abort
```

### 🎯 **Cherry-pick (aplicar commit específico)**
```bash
# Aplicar un commit específico a la rama actual
git cherry-pick abc1234

# Aplicar múltiples commits
git cherry-pick abc1234 def5678

# Cherry-pick sin hacer commit automático
git cherry-pick --no-commit abc1234
```

---

## 🆘 **COMANDOS DE EMERGENCIA**

### 🚨 **¡Todo está roto! - Comandos de rescate**
```bash
# Ver el estado de todas las ramas
git log --all --graph --oneline

# Encontrar trabajo perdido
git reflog

# Recuperar commit "perdido"
git checkout abc1234

# Crear rama de respaldo con el commit recuperado
git checkout -b respaldo-recuperacion

# Stash: Guardar cambios temporalmente
git stash
git stash list
git stash pop

# Limpiar completamente el directorio de trabajo
git clean -fd
git reset --hard HEAD
```

### 🔄 **Sincronizar con repositorio remoto**
```bash
# Forzar actualización desde remoto (¡CUIDADO!)
git fetch origin
git reset --hard origin/main

# Subir cambios forzadamente (¡CUIDADO!)
git push --force origin feature/tu-apellido

# Mejor opción: force with lease (más seguro)
git push --force-with-lease origin feature/tu-apellido
```
