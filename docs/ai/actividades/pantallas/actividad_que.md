---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividades"
titulo: "Buscar actividad (filtros)"
pantalla: "actividades.pantalla.actividad_que"
preguntas: ["Que se puede hacer en Buscar actividad (filtros)?", "Que campos tiene Buscar actividad (filtros)?", "Que acciones hay en Buscar actividad (filtros)?"]
capacidades: ["actividades.actividad_que.gestionar", "actividades.actividad_que_filtros.gestionar", "actividades.actividad_tipo.gestionar"]
endpoints: ["/src/actividades/actividad_que_datos", "/src/actividades/actividad_que_filtros", "/src/actividades/actividad_tipo_get", "/src/procesos/actividad_que_fases_ajax"]
source: "docs/catalogo/actividades/pantallas/actividad_que.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Buscar actividad (filtros)

## Resumen

Pantalla de **filtros de busqueda de actividades**. Es la entrada de los menus *Buscar*, *Importar* (`modo=importar`), *Publicar* (`modo=publicar`) y de los listados conjuntos (`que=list_cjto…`). El usuario concreta tipo de actividad (cascada sf/sv → asistentes → actividad → tipo), estado, nombre, lugar, organiza, publicada, periodo y (con `procesos`) fases marcadas/sin marcar; el boton **buscar** envia el formulario a la pantalla de resultados.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.modo`
- `post.que`
- `post.status`
- `post.id_tipo_activ`
- `post.filtro_lugar`
- `post.id_ubi`
- `post.nom_activ`
- `post.periodo`
- `post.year`
- `post.dl_org`
- `post.empiezamin`
- `post.empiezamax`
- `post.fases_on`
- `post.fases_off`
- `post.listar_asistentes`
- `post.publicado`
- `post.sasistentes`
- `post.sactividad`
- `post.sactividad2`
- `post.snom_tipo`
- `post.extendida`
- `post.stack`

## Acciones Detectadas

- `fnjs_buscar`
- `fnjs_cargar_filtros_extra`
- `fnjs_actualizar_fases`
- `fnjs_lugar`
- `fnjs_asistentes`
- `fnjs_actividad`
- `fnjs_nom_tipo`
- `fnjs_id_activ`
- `fnjs_comprobar_fase_no_duplicadas`

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
