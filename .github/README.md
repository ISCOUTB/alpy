# 🚀 GitHub Actions para Alpy

Este directorio contiene los workflows de GitHub Actions que automatizan el proceso de desarrollo, validación y release del plugin Alpy para Moodle.

## 📋 Workflows Disponibles

### 1. `release.yml` - Creación de Releases
**Trigger:** Tags con formato `v*.*.*` o ejecución manual

**Funciones:**
- ✅ Valida la estructura del plugin
- 📦 Crea un archivo ZIP optimizado
- 🔍 Genera hash SHA256 del archivo
- 📝 Crea notas de release detalladas
- 🚀 Publica el release en GitHub
- 💾 Guarda el artefacto por 90 días

**Uso manual:**
1. Ve a Actions → Create Release
2. Haz clic en "Run workflow"
3. Opcionalmente especifica una versión

### 2. `auto-release.yml` - Release Automático
**Trigger:** Push a `main` que modifique `version.php`

**Funciones:**
- 🔍 Detecta cambios en la versión del plugin
- 🏷️ Crea automáticamente un tag
- ⚡ Dispara el workflow de release

**Formato de versión:**
- Formato en `version.php`: `YYYYMMDDXX` (ej: `2025090400`)
- Tag generado: `vYYYY.MM.DD.XX` (ej: `v2025.09.04.00`)

### 3. `validate.yml` - Validación del Plugin
**Trigger:** Pull requests y push a `main`

**Validaciones:**
- 📁 Estructura de archivos requeridos
- 🔧 Formato del archivo `version.php`
- 🌐 Archivos de idioma válidos
- 🎨 Presencia de templates Mustache
- 📦 Creación exitosa del archivo ZIP

## 🔄 Flujo de Trabajo Recomendado

### Para Desarrollo:
1. Crea una rama feature: `git checkout -b feature/nueva-funcionalidad`
2. Desarrolla y commitea cambios
3. Abre un Pull Request → se ejecuta `validate.yml`
4. Merge a `main` después de la revisión

### Para Release:
1. Actualiza la versión en `version.php`
2. Commitea: `git commit -m "bump version to 2025090400"`
3. Push a `main` → se ejecuta `auto-release.yml`
4. El sistema crea automáticamente el tag y release

### Para Release Manual:
1. Ve a Actions → Create Release
2. Ejecuta manualmente especificando la versión
3. O crea un tag manualmente: `git tag v2025.09.04.00 && git push origin v2025.09.04.00`

## 📦 Archivo ZIP Generado

El archivo final incluye:
```
alpy/
├── backup/
├── classes/
├── db/
├── lang/
├── templates/
├── tests/
├── config.php
├── format.php
├── lib.php
├── README.md
├── settings.php
└── version.php
```

**Excluidos automáticamente:**
- Archivos de Git (`.git*`)
- Archivos temporales (`*.log`, `*.tmp`)
- Archivos del sistema (`.DS_Store`, `Thumbs.db`)
- Dependencias de desarrollo (`node_modules`, `vendor`)
- Archivos de configuración del IDE

## 🔧 Configuración

### Permisos Requeridos:
- `contents: write` - Para crear releases y tags
- `GITHUB_TOKEN` - Proporcionado automáticamente

### Variables de Entorno:
- `archive_size` - Tamaño del archivo ZIP
- `archive_hash` - Hash SHA256 del archivo

## 📊 Outputs y Artefactos

### Cada Release Incluye:
- 📄 **Notas detalladas** con características y instrucciones
- 🔗 **Archivo ZIP** listo para instalar en Moodle
- 🔑 **Hash SHA256** para verificación de integridad
- 📊 **Metadatos** (tamaño, número de archivos, etc.)

### Artefactos de Build:
- Disponibles por 90 días en GitHub Actions
- Útiles para testing antes del release oficial

## 🐛 Solución de Problemas

### Error: "Missing required file"
- Verifica que todos los archivos obligatorios estén presentes
- Consulta la validación en `validate.yml`

### Error: "Plugin component name not found"
- Asegúrate de que `version.php` contenga `format_alpy`

### Error: "Tag already exists"
- El tag ya existe, incrementa la versión en `version.php`

### Release no se ejecuta automáticamente:
- Verifica que el commit modifique `version.php`
- Revisa los logs en Actions

## 📚 Recursos Adicionales

- [Documentación de GitHub Actions](https://docs.github.com/en/actions)
- [Guía de Plugins de Moodle](https://docs.moodle.org/dev/Plugin_files)
- [Estándares de Versionado](https://semver.org/)

---
*🤖 Este sistema está completamente automatizado y no requiere intervención manual para releases estándar.*
