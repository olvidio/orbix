---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividades"
titulo: "Gestión de tipos de actividad"
pantalla: "actividades.pantalla.tipo_activ"
preguntas: ["Que se puede hacer en Gestión de tipos de actividad?", "Que campos tiene Gestión de tipos de actividad?", "Que acciones hay en Gestión de tipos de actividad?"]
capacidades: ["actividades.tipo_activ.gestionar", "actividades.tipo_activ_form.gestionar", "actividades.tipo_activ_form_modificar.gestionar"]
endpoints: ["/src/actividades/tipo_activ_eliminar", "/src/actividades/tipo_activ_form_modificar", "/src/actividades/tipo_activ_form_nuevo", "/src/actividades/tipo_activ_lista", "/src/actividades/tipo_activ_nuevo", "/src/actividades/tipo_activ_update"]
source: "docs/catalogo/actividades/pantallas/tipo_activ.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Gestión de tipos de actividad

## Resumen

Pantalla de **administración del catálogo de tipos** (código compuesto sf/sv + asistentes + actividad + nombre). Carga la tabla vía AJAX (`tipo_activ_lista`), permite alta (`tipo_activ_form_nuevo` → `tipo_activ_nuevo`), edición (`tipo_activ_form_modificar` → `tipo_activ_update`) y borrado (`tipo_activ_eliminar` con confirmación). Renderizada con `ViewNewTwig` + `tipo_activ.html.twig`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_tipo_activ`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `actividades.tipo_activ.gestionar`
- `actividades.tipo_activ_form.gestionar`
- `actividades.tipo_activ_form_modificar.gestionar`

## Endpoints Relacionados

- `/src/actividades/tipo_activ_eliminar`
- `/src/actividades/tipo_activ_form_modificar`
- `/src/actividades/tipo_activ_form_nuevo`
- `/src/actividades/tipo_activ_lista`
- `/src/actividades/tipo_activ_nuevo`
- `/src/actividades/tipo_activ_update`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
