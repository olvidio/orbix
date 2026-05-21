---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividades"
titulo: "Planning Casa Nueva"
pantalla: "actividades.pantalla.planning_casa_nueva"
preguntas: ["Que se puede hacer en Planning Casa Nueva?", "Que campos tiene Planning Casa Nueva?", "Que acciones hay en Planning Casa Nueva?"]
capacidades: ["actividades.actividad_que.gestionar", "actividades.actividad_status_labels.gestionar", "actividades.actividad_ver.gestionar"]
endpoints: ["/src/actividades/actividad_que_datos", "/src/actividades/actividad_status_labels_datos", "/src/actividades/actividad_ver_datos"]
source: "docs/catalogo/actividades/pantallas/planning_casa_nueva.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Planning Casa Nueva

## Resumen

Formulario para crear una actividad nueva desde el planning de casas.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.dl_org`
- `form.isfsv`
- `form.ssfsv`
- `post.id_ubi`

## Acciones Detectadas

- No hay acciones detectadas.

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
