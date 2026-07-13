---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "planning"
titulo: "Planning por casas (filtros)"
pantalla: "planning.pantalla.planning_casa_que"
preguntas: ["Que se puede hacer en Planning por casas (filtros)?", "Que campos tiene Planning por casas (filtros)?", "Que acciones hay en Planning por casas (filtros)?"]
capacidades: ["planning.planning_casa_que.gestionar"]
endpoints: ["/src/planning/planning_casa_que_data"]
source: "docs/catalogo/planning/pantallas/planning_casa_que.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Planning por casas (filtros)

## Resumen

Formulario de criterios: grupo de casas (`CasasQue`), periodo y opción de incluir casas sin actividad. Al enviar navega a `planning_casa_select.php`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.cdc_sel`
- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_cdc_mas`
- `form.id_cdc_num`
- `form.modelo`
- `form.periodo`
- `form.sin_activ`
- `form.year`
- `html.modelo`
- `html.sin_activ`
- `post.cdc_sel`
- `post.empiezamax`
- `post.empiezamin`
- `post.periodo`
- `post.propuesta_calendario`
- `post.sSeleccionados`
- `post.sin_activ`
- `post.stack`
- `post.year`

## Acciones Detectadas

- `fnjs_comprobar_fecha`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_ver_planning`

## Capacidades Relacionadas

- `planning.planning_casa_que.gestionar`

## Endpoints Relacionados

- `/src/planning/planning_casa_que_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
