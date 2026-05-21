---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividades"
titulo: "Actividad Select"
pantalla: "actividades.pantalla.actividad_select"
preguntas: ["Que se puede hacer en Actividad Select?", "Que campos tiene Actividad Select?", "Que acciones hay en Actividad Select?"]
capacidades: ["actividades.actividad_select.gestionar"]
endpoints: ["/src/actividades/actividad_select_datos"]
source: "docs/catalogo/actividades/pantallas/actividad_select.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Actividad Select

## Resumen

Lista de actividades que cumplen con los filtros de actividad_que.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_dossier`
- `form.mod`
- `form.queSel`
- `html.b_buscar`
- `html.id_dossier`
- `html.mod`
- `html.queSel`
- `post.Gstack`
- `post.continuar`
- `post.dl_org`
- `post.empiezamax`
- `post.empiezamin`
- `post.fases_off`
- `post.fases_on`
- `post.filtro_lugar`
- `post.id_tipo_activ`
- `post.id_ubi`
- `post.modo`
- `post.nom_activ`
- `post.periodo`
- `post.publicado`
- `post.sactividad`
- `post.sactividad2`
- `post.sasistentes`
- `post.scroll_id`
- `post.sel`
- `post.ssfsv`
- `post.stack`
- `post.status`
- `post.year`

## Acciones Detectadas

- `button:. _(`
- `fnjs_borrar`
- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_solo_uno`
- `fnjs_update_div`

## Capacidades Relacionadas

- `actividades.actividad_select.gestionar`

## Endpoints Relacionados

- `/src/actividades/actividad_select_datos`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
