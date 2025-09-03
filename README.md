# Alpy — Formato de curso personalizado para Moodle

**Alpy** es un formato de curso para Moodle que personaliza la experiencia de aprendizaje. Funcionalidades clave:

- **Etiquetas por actividad**: Asigna etiquetas como `lectura` o `proyecto` a cada módulo.
- **Organización dinámica**: Reordena las actividades según el estilo de aprendizaje del estudiante.
- **Perfil de aprendizaje**: Muestra un resumen visual del perfil del alumno (activo/reflexivo, etc.).
- **Estadísticas para docentes**: Visualiza los estilos de aprendizaje predominantes en el grupo.
- **Iconos dinámicos**: El icono del recurso cambia según su etiqueta.

---

## Compatibilidad

Este plugin es compatible con **Moodle 4.1** y versiones posteriores.

---

## Funcionamiento

El renderizado de actividades se gestiona mediante una combinación de clases PHP y plantillas Mustache. Las clases en `classes/output/courseformat/` sobreescriben la lógica por defecto de Moodle.

### Archivos clave

- `classes/output/courseformat/content/section/cmitem.php`: Recopila las **etiquetas** de cada actividad.
- `classes/output/courseformat/content/cm/cmicon.php`: Obtiene los **iconos** según la etiqueta.
- `classes/output/courseformat/content.php`: **Punto de entrada** que procesa el curso y sus secciones.
- `classes/output/courseformat/content/section.php`: Implementa la **lógica de reordenamiento** de actividades basada en el estilo de aprendizaje del estudiante.

---

## Plantillas Mustache

Las plantillas `.mustache` definen la estructura visual:

- `templates/local/content.mustache`: Recorre las secciones del curso.
- `templates/local/content/section.mustache`: Renderiza el título de la sección y la lista de actividades.
- `templates/local/content/section/cmitem.mustache`: Muestra cada actividad con sus etiquetas e iconos.
- `templates/local/content/cm.mustache`: Muestra el contenido del módulo.
- `templates/local/content/section/cmlist.mustache`: Organiza la lista de actividades.

