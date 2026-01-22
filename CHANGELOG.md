# Changelog

Todos los cambios notables de este proyecto se documentan en este archivo.

El formato se basa en [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
y este proyecto sigue [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.1] - 2026-01-21

### Corregido
- Nuevos estilos CSS para asegurar que los iconos personalizados mantengan el color original sin filtros aplicados por Moodle y con fondo adecuado.
- Botón de "Guardar cambios y regresar al curso" en la configuración del formato de curso ahora redirige correctamente al curso tras guardar.

## [1.0.0] — 2026-01-20

### Agregado
- **Sistema de iconos integrado**: Implementado en `cmname.php` para reemplazar iconos basados en etiquetas, con soporte para SVG y PNG desde `/pix/`.
- **Configuración de secciones**: Dos modos implementados:
  - **Modo "Periodo Académico"**: Creación automática de 16 secciones semanales con fechas basadas en el semestre actual.
  - **Modo Manual**: Configuración flexible del número de secciones (0-52).
- **Algoritmo de reordenamiento**: Sistema de scoring basado en pesos predefinidos por tipo de recurso y perfil de aprendizaje del estudiante.
- **Nuevas capacidades**: Implementado `format/alpy:viewlearningprofile` en `db/access.php`.
- **Caché multinivel**: Implementado sistema de caché de sesión (perfiles) y request (tags) definidos en `db/caches.php` para mejorar rendimiento.
- **Nuevos archivos y plantillas**: `styles.css` para estilos, `cmname.mustache` para renderizado personalizado de nombres e iconos, y `cmlist.mustache` para listados reordenados.
- **Manejo de errores**: Agregado manejo robusto de excepciones en consultas SQL dentro de la lógica de reordenamiento.
- **Plugin complementario recomendado**: Integración sugerida con `alpy_toolkit` para gestión masiva de etiquetas.

### Cambiado
- **Lógica de reordenamiento**: Centralizada en `classes/output/courseformat/content/section/cmlist.php` para organizar actividades según estilos de aprendizaje.
- **Optimización SQL**: Eliminado problema N+1 en obtención de tags usando `core_tag_tag::get_items_tags` y caché.
- **UI/UX**: Estilos inline movidos a archivo CSS externo y soporte de clase `alpy-custom-icon`.
- **Calidad de código**: Uso de `cmname` para integración transparente de iconos en lugar de componentes separados.

### Corregido
- **Integración de iconos**: La clase `cmname` ahora maneja correctamente la resolución de iconos personalizados y compatibilidad con el renderizado core.

### Seguridad
- **Validaciones**: Implementada validación de existencia de tabla `learning_style` y limpieza de nombres de tags para evitar path traversal.
- **Consultas**: Uso consistente de parámetros preparados y validación de capabilities antes de alterar el orden de actividades.
