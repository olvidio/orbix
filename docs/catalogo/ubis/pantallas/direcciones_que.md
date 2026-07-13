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
estado_revision: "revisado"
---

# Direcciones Que

Formulario de criterios para buscar direcciones existentes a asignar a un ubi.

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

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
