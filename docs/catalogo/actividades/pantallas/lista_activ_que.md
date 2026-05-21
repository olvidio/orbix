---
id: "actividades.pantalla.lista_activ_que"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "actividades"
nombre: "Lista Activ Que"
controller: "frontend/actividades/controller/lista_activ_que.php"
vistas: []
fragmentos_frontend: ["frontend/actividades/controller/lista_activ.php"]
endpoints: ["/src/actividades/lista_activ_datos"]
capacidades: ["actividades.lista_activ.gestionar"]
campos: ["form.asist", "form.c_activ", "form.empiezamax", "form.empiezamin", "form.seccion", "form.status", "form.tit_list_grupo", "post.que"]
acciones: []
estado_revision: "generado"
---

# Lista Activ Que

Pantalla de filtros para listados particulares de sr/sg.

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/actividades/controller/lista_activ_que.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/actividades/controller/lista_activ.php`

## Endpoints Usados

- `/src/actividades/lista_activ_datos`

## Capacidades Relacionadas

- `actividades.lista_activ.gestionar`

## Campos Detectados

- `form.asist`
- `form.c_activ`
- `form.empiezamax`
- `form.empiezamin`
- `form.seccion`
- `form.status`
- `form.tit_list_grupo`
- `post.que`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
