---
id: "ubis.pantalla.direcciones_que"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "ubis"
nombre: "Direcciones Que"
controller: "frontend/ubis/controller/direcciones_que.php"
vistas: ["frontend/ubis/view/direcciones_que.phtml"]
fragmentos_frontend: ["frontend/ubis/controller/direcciones_tabla.php"]
endpoints: ["/src/ubis/direcciones_que"]
capacidades: ["ubis.direcciones_que.gestionar"]
campos: ["form.c_p", "form.ciudad", "form.id_ubi", "form.obj_dir", "form.pais", "html.btn_ok", "post.id_ubi", "post.obj_dir"]
acciones: ["fnjs_enviar", "fnjs_enviar_formulario"]
estado_revision: "generado"
---

# Direcciones Que

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/ubis/controller/direcciones_que.php`

## Vistas Relacionadas

- `frontend/ubis/view/direcciones_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/ubis/controller/direcciones_tabla.php`

## Endpoints Usados

- `/src/ubis/direcciones_que`

## Capacidades Relacionadas

- `ubis.direcciones_que.gestionar`

## Campos Detectados

- `form.c_p`
- `form.ciudad`
- `form.id_ubi`
- `form.obj_dir`
- `form.pais`
- `html.btn_ok`
- `post.id_ubi`
- `post.obj_dir`

## Acciones Detectadas

- `fnjs_enviar`
- `fnjs_enviar_formulario`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
