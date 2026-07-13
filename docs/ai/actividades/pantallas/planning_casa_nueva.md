---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividades"
titulo: "Nueva actividad desde planning"
pantalla: "actividades.pantalla.planning_casa_nueva"
preguntas: ["Que se puede hacer en Nueva actividad desde planning?", "Que campos tiene Nueva actividad desde planning?", "Que acciones hay en Nueva actividad desde planning?"]
capacidades: ["actividades.actividad_que.gestionar", "actividades.actividad_status_labels.gestionar", "actividades.actividad_ver.gestionar"]
endpoints: ["/src/actividades/actividad_que_datos", "/src/actividades/actividad_status_labels_datos", "/src/actividades/actividad_ver_datos"]
source: "docs/catalogo/actividades/pantallas/planning_casa_nueva.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Nueva actividad desde planning

## Resumen

Fragmento para **alta de actividad** en el planning de casas. Recibe `id_ubi` (casa del calendario), precarga delegación y sf/sv del usuario, status inicial *proyecto* y formulario vacío vía `actividad_ver_datos` (`id_activ=0`). Cascada de tipo con `actividad_que_datos`. Guardar usa `actividad_nuevo` (JS compartido con ficha).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.dl_org`
- `form.isfsv`
- `form.ssfsv`
- `post.id_ubi`

## Acciones Detectadas

- `fnjs_guardar`

## Capacidades Relacionadas

- `actividades.actividad_que.gestionar`
- `actividades.actividad_status_labels.gestionar`
- `actividades.actividad_ver.gestionar`

## Endpoints Relacionados

- `/src/actividades/actividad_que_datos`
- `/src/actividades/actividad_status_labels_datos`
- `/src/actividades/actividad_ver_datos`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
