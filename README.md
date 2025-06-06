# Alpy — Formato de curso personalizado para Moodle

**Alpy** es un formato de curso basado en secciones que permite personalizar la experiencia de aprendizaje en Moodle. Este formato ofrece las siguientes funcionalidades clave:

- **Etiquetas visibles por actividad**: Cada módulo puede tener una etiqueta asociada (como `lectura`, `simulación`, `proyecto`, etc.), que se muestra junto al recurso.
- **Organización personalizada**: Las actividades dentro de cada sección se reordenan automáticamente según la relación entre su etiqueta y el estilo de aprendizaje dominante del estudiante.
- **Bloque de estilos de aprendizaje**: Se muestra un resumen visual del perfil del estudiante (activo/reflexivo, sensitivo/intuitivo, etc.), tanto para el alumno como para el docente.
- **Estadísticas para el docente**: El profesor puede visualizar qué estilos de aprendizaje predominan en su grupo de estudiantes, facilitando el análisis pedagógico.
- **Iconos dinámicos por etiqueta**: El icono de cada recurso cambia automáticamente dependiendo de su etiqueta asignada, mejorando la identificación visual de los contenidos.

---

## Funcionamiento

En este formato de curso Alpy, el renderizado de cada actividad se gestiona por medio de una combinación de clases PHP que preparan los datos y plantillas Mustache que definen cómo se visualizan. En las últimas versiones, todas las clases dentro de `output/courseformat` anulan las predeterminadas automáticamente.

### Etiquetas y Iconos

- `cmitem.php`: recopila las **etiquetas** asignadas a cada módulo de actividad y las envía a `cmitem.mustache` para ser renderizadas.
- `cmicon.php`: obtiene los **iconos** en función de las etiquetas del módulo y se los entrega a la plantilla Mustache para mostrarlos.

### Renderizado y Reordenamiento

- `content.php`: es el **punto de entrada principal**. Obtiene toda la información del curso, recorre todas las secciones visibles y delega la representación de las mismas a `section.php`.
- `section.php`: es donde se implementa la **lógica de reordenamiento** de actividades dentro de una sección:
  - Filtra las actividades por sección.
  - Obtiene el estilo de aprendizaje dominante del estudiante.
  - Calcula un **puntaje** para cada módulo según la afinidad entre el estilo de aprendizaje del estudiante y la etiqueta del recurso.
  - Ordena los módulos con `usort()` de mayor a menor puntaje.

---

## Plantillas Mustache

Las plantillas `.mustache` controlan la presentación visual de las secciones y actividades:

- `content.mustache`: recorre todas las secciones del curso.
- `section.mustache`: renderiza el título de la sección y la lista de sus actividades.
- `cmitem.mustache`: muestra cada actividad con sus etiquetas e iconos personalizados.
- `cm.mustache`: se encarga de mostrar el contenido del módulo.
- `cmlist.mustache`: organiza visualmente el listado de actividades.
