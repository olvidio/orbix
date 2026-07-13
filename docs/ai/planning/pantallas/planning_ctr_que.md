---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "planning"
titulo: "Planning por centro (filtros)"
pantalla: "planning.pantalla.planning_ctr_que"
preguntas: ["Que se puede hacer en Planning por centro (filtros)?", "Que campos tiene Planning por centro (filtros)?", "Que acciones hay en Planning por centro (filtros)?"]
capacidades: []
endpoints: []
source: "docs/catalogo/planning/pantallas/planning_ctr_que.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Planning por centro (filtros)

## Resumen

Formulario: centro concreto o todos (`todos_n`/`todos_agd`/`todos_s`), periodo y filtro sacd. Al enviar carga `planning_ctr_select` por AJAX.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.ctr`
- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.periodo`
- `form.sacd`
- `form.year`
- `html.ctr`
- `html.modelo`
- `html.sacd`
- `html.todos_agd`
- `html.todos_n`
- `html.todos_s`
- `post.ctr`
- `post.empiezamax`
- `post.empiezamin`
- `post.obj_pau`
- `post.periodo`
- `post.sacd`
- `post.stack`
- `post.tipo`
- `post.todos_agd`
- `post.todos_n`
- `post.todos_s`
- `post.year`

## Acciones Detectadas

- `fnjs_comprobar_fecha`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_ver_planning`

## Capacidades Relacionadas

- No hay capacidades relacionadas.

## Endpoints Relacionados

- No hay endpoints detectados.

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
