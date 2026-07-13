---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividades"
titulo: "Editar actividad desde planning"
pantalla: "actividades.pantalla.planning_casa_modificar"
preguntas: ["Que se puede hacer en Editar actividad desde planning?", "Que campos tiene Editar actividad desde planning?", "Que acciones hay en Editar actividad desde planning?"]
capacidades: ["actividades.actividad_que.gestionar", "actividades.actividad_status_labels.gestionar", "actividades.actividad_ver.gestionar"]
endpoints: ["/src/actividades/actividad_que_datos", "/src/actividades/actividad_status_labels_datos", "/src/actividades/actividad_ver_datos"]
source: "docs/catalogo/actividades/pantallas/planning_casa_modificar.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Editar actividad desde planning

## Resumen

Fragmento con el **formulario de edición** de una actividad existente, incrustado en el planning de casas (`planning_casa_que`). Recibe `id_activ`, carga entidad y desplegables con `actividad_ver_datos`, bloque tipo con `actividad_que_datos` y etiquetas de status con `actividad_status_labels_datos`. Reutiliza las plantillas `_actividad_form_*` en modo `editar`; guardar llama a `actividad_editar` (JS compartido).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.dl_org`
- `form.isfsv`
- `form.ssfsv`
- `post.id_activ`

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
