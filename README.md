# Alpy — Formato de curso personalizado para Moodle

El formato **Alpy** para Moodle personaliza la experiencia de aprendizaje adaptando la visualización del curso según el estilo de aprendizaje del estudiante y ofreciendo una organización visual dinámica mediante etiquetas e iconos.

## Contenido

- [Funcionalidades](#funcionalidades)
- [Sección técnica](#sección-técnica)
- [Instalación](#instalación)
- [Consideraciones de despliegue](#consideraciones-de-despliegue)
- [Contribuciones](#contribuciones)
- [Equipo de desarrollo](#equipo-de-desarrollo)

---

## Funcionalidades

### Para estudiantes
- **Adaptabilidad**: Reordenamiento dinámico de actividades basado en el perfil de aprendizaje (obtenido de la tabla `learning_style` procedente del plugin `learning_style`).
- **Indicadores visuales**: Iconos dinámicos que cambian según la etiqueta de la actividad (ej. lectura, proyecto, etc...).

### Para docentes
- **Etiquetado de actividades**: Sistema simple para asignar etiquetas a los módulos mediante el sistema de tags de Moodle.
- **Configuración de secciones**: Dos modos disponibles:
   - **Periodo Académico (Automático)**: Crea automáticamente 16 secciones semanales, iniciando el primer lunes de febrero o agosto según el semestre actual.
   - **Modo Manual**: Permite especificar el número de secciones deseadas.
- **Recomendación**: Para facilitar el etiquetado de actividades/recursos, se recomienda usar el plugin complementario [**alpy_toolkit**](https://github.com/ISCOUTB/alpy_toolkit/).

---

## Sección técnica

### 1) Estructura y Renderizado

El formato utiliza una arquitectura basada en clases de salida (Output Classes) y plantillas Mustache para sobreescribir la presentación estándar de Moodle.

**Archivos clave:**
- `classes/output/courseformat/content.php`: Punto de entrada principal.
- `classes/output/courseformat/content/section/cmlist.php`: Implementa la lógica de reordenamiento de actividades basada en el estilo de aprendizaje del estudiante y pesos de recursos.
- `classes/output/courseformat/content/cm/cmname.php`: Gestiona el renderizado del nombre de la actividad y la inyección de iconos personalizados.
- `lib.php`: Define los pesos de los recursos (`get_resource_weights`) y alias de etiquetas.

### 2) Sistema de Iconos Personalizados

El formato implementa un sistema de reemplazo de iconos integrado en la clase `cmname`:
- Los iconos se buscan automáticamente en la carpeta `/pix/`.
- Soporta formatos **SVG** (prioritario) y **PNG**.
- El sistema asocia etiquetas de actividad con nombres de archivo (ej. `lectura.svg`).
- Utiliza la plantilla `templates/local/content/cm/cmname.mustache` para renderizar el icono customizado junto al nombre.

### 3) Algoritmo de Reordenamiento

El formato calcula un score de compatibilidad para cada actividad según el perfil de aprendizaje del estudiante:

**Proceso:**
1. Se obtiene el perfil de aprendizaje del estudiante desde la tabla `learning_style` (activo, reflexivo, sensorial, intuitivo, visual, verbal, secuencial, global).
2. Para cada actividad, se extraen sus etiquetas (tags) asociadas.
3. Se consultan los pesos predefinidos en `lib.php` (método `get_resource_weights()`) para cada tipo de recurso.
4. Se calcula el score mediante la fórmula:
   ```
   score_actividad = Σ (perfil[dimensión] × peso[etiqueta][dimensión])
   ```
5. Las actividades se ordenan de mayor a menor score (descendente).
6. Si hay empate en scores, se respeta el orden original.

**Ejemplo práctico:**
- Estudiante con perfil `{visual: 7, activo: 5, ...}`
- Actividad etiquetada como `reading` con pesos `{visual: 2, activo: 3, ...}`
- Score = (7×2) + (5×3) + ... = score_total

**Nota importante:** Los docentes siempre ven el orden original sin reordenamiento.

### 4) Integración de Datos

- **Tabla de dependencias**: Requiere la existencia de la tabla `learning_style` para obtener el perfil del estudiante.
- **Caché**: Implementa caché de sesión (TTL 1 hora) para perfiles (`learning_profiles`) y caché de solicitud para tags (`activity_tags`), definidos en `db/caches.php`.

### 5) Configuración de Secciones

El formato ofrece dos modos de configuración durante la creación del curso:

**Modo Académico (Automático):**
- Selecciona automáticamente el semestre actual basándose en la fecha.
- Crea 16 secciones semanales.
- Establece la fecha de inicio en el primer lunes de febrero (primer semestre) o agosto (segundo semestre).

**Modo Manual:**
- Permite seleccionar el número de secciones deseadas (0-52).
- Requiere especificar manualmente la fecha de inicio (se ajusta al siguiente lunes si no es lunes).
- Mayor flexibilidad para cursos no estándar.

---

## Instalación

1. Descargar el plugin desde las *releases* del repositorio oficial: https://github.com/ISCOUTB/alpy/releases
2. En Moodle (como administrador):
   - Ir a **Administración del sitio → Extensiones → Instalar plugins**.
   - Subir el archivo ZIP.
   - Completar el asistente de instalación.
3. Si el curso es:
    - **Nuevo:** seleccionar el formato **Alpy** al crearlo y configurar el "Modo de configuración de fecha".
    - **Existente:** cambiar el formato en **Administración del curso → Editar ajustes → Formato de curso**. Aquí no se podrá cambiar el modo de configuración de fecha.
5. (Opcional) Instalar el plugin complementario [**alpy_toolkit**](https://github.com/ISCOUTB/alpy_toolkit/) para facilitar el etiquetado masivo de actividades.

---

## Consideraciones de despliegue

- **Compatibilidad declarada:** Moodle 4.0+.
- **Dependencias**: Bloque `learning_style`.
- **Cachés**: Se recomienda purgar cachés tras la actualización o carga de nuevos iconos.

---

## Contribuciones

¡Las contribuciones son bienvenidas! Si deseas mejorar este bloque, por favor sigue estos pasos:

1. Haz un fork del repositorio.
2. Crea una nueva rama para tu característica o corrección de errores.
3. Realiza tus cambios y asegúrate de que todo funcione correctamente.
4. Envía un pull request describiendo tus cambios.

---

## Equipo de desarrollo

- Jairo Enrique Serrano Castañeda
- Yuranis Henriquez Núñez
- Isaac David Sánchez Sánchez
- Santiago Andrés Orejuela Cueter
- María Valentina Serna González

<div align="center">
<strong>Desarrollado con ❤️ para la Universidad Tecnológica de Bolívar</strong>
</div>
