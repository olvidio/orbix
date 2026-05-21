---
id: "actividadestudios.pantalla.ca_posibles"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadestudios"
nombre: "Ca Posibles"
controller: "frontend/actividadestudios/controller/ca_posibles.php"
vistas: ["frontend/actividadestudios/view/ca_posibles_cuadro.phtml", "frontend/actividadestudios/view/ca_posibles_lista.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadestudios/ca_posibles_data"]
capacidades: ["actividadestudios.ca_posibles.gestionar"]
campos: ["html.observ", "post.ca_estudios", "post.ca_repaso", "post.ca_todos", "post.empiezamax", "post.empiezamin", "post.grupo_estudios", "post.id_ctr_agd", "post.id_ctr_n", "post.idca", "post.na", "post.obj_pau", "post.periodo", "post.ref", "post.sel", "post.stack", "post.texto", "post.year"]
acciones: ["fnjs_update_div"]
estado_revision: "generado"
---

# Ca Posibles

Esta página sirve para calcular los créditos cursables para cada alumno en cada ca.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadestudios/controller/ca_posibles.php`

## Vistas Relacionadas

- `frontend/actividadestudios/view/ca_posibles_cuadro.phtml`
- `frontend/actividadestudios/view/ca_posibles_lista.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadestudios/ca_posibles_data`

## Capacidades Relacionadas

- `actividadestudios.ca_posibles.gestionar`

## Campos Detectados

- `html.observ`
- `post.ca_estudios`
- `post.ca_repaso`
- `post.ca_todos`
- `post.empiezamax`
- `post.empiezamin`
- `post.grupo_estudios`
- `post.id_ctr_agd`
- `post.id_ctr_n`
- `post.idca`
- `post.na`
- `post.obj_pau`
- `post.periodo`
- `post.ref`
- `post.sel`
- `post.stack`
- `post.texto`
- `post.year`

## Acciones Detectadas

- `fnjs_update_div`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
