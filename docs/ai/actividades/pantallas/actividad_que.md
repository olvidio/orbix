---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividades"
titulo: "Actividad Que"
pantalla: "actividades.pantalla.actividad_que"
preguntas: ["Que se puede hacer en Actividad Que?", "Que campos tiene Actividad Que?", "Que acciones hay en Actividad Que?"]
capacidades: ["actividades.actividad_que.gestionar", "actividades.actividad_que_filtros.gestionar", "actividades.actividad_tipo.gestionar"]
endpoints: ["/src/actividades/actividad_que_datos", "/src/actividades/actividad_que_filtros", "/src/actividades/actividad_tipo_get", "/src/procesos/actividad_que_fases_ajax"]
source: "docs/catalogo/actividades/pantallas/actividad_que.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Actividad Que

## Resumen

Pantalla para escoger los filtros que determinan una busqueda de actividades.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.dl_org`
- `form.dl_propia`
- `form.entrada`
- `form.extendida`
- `form.filtro_lugar`
- `form.id_tipo_activ`
- `form.id_ubi`
- `form.isfsv`
- `form.modo`
- `form.opcion_sel`
- `form.publicado`
- `form.salida`
- `form.selected`
- `form.sfsv`
- `post.dl_org`
- `post.empiezamax`
- `post.empiezamin`
- `post.extendida`
- `post.fases_off`
- `post.fases_on`
- `post.filtro_lugar`
- `post.id_tipo_activ`
- `post.id_ubi`
- `post.listar_asistentes`
- `post.modo`
- `post.nom_activ`
- `post.periodo`
- `post.publicado`
- `post.que`
- `post.sactividad`
- `post.sactividad2`
- `post.sasistentes`
- `post.snom_tipo`
- `post.stack`
- `post.status`
- `post.year`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `actividades.actividad_que.gestionar`
- `actividades.actividad_que_filtros.gestionar`
- `actividades.actividad_tipo.gestionar`

## Endpoints Relacionados

- `/src/actividades/actividad_que_datos`
- `/src/actividades/actividad_que_filtros`
- `/src/actividades/actividad_tipo_get`
- `/src/procesos/actividad_que_fases_ajax`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
