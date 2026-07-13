---
id: "ubis.pantalla.direcciones_tabla"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "ubis"
nombre: "Direcciones Tabla"
controller: "frontend/ubis/controller/direcciones_tabla.php"
vistas: ["frontend/ubis/view/direcciones_tabla.phtml"]
fragmentos_frontend: ["frontend/ubis/controller/direcciones_editar.php"]
endpoints: ["/src/ubis/direcciones_tabla"]
capacidades: ["ubis.direcciones_tabla.gestionar"]
campos: ["post.c_p", "post.ciudad", "post.id_ubi", "post.obj_dir", "post.pais"]
acciones: ["fnjs_update_div"]
estado_revision: "revisado"
---

# Direcciones Tabla

Tabla AJAX de direcciones encontradas para asignar a un ubi.

## Tipo

- Subtipo: `fragmento_ajax`


- Controller: `frontend/ubis/controller/direcciones_tabla.php`

## Vistas Relacionadas

- `frontend/ubis/view/direcciones_tabla.phtml`

## Fragmentos Frontend Relacionados

- `frontend/ubis/controller/direcciones_editar.php`

## Endpoints Usados

- `/src/ubis/direcciones_tabla`

## Capacidades Relacionadas

- `ubis.direcciones_tabla.gestionar`

## Campos Detectados

- `post.c_p`
- `post.ciudad`
- `post.id_ubi`
- `post.obj_dir`
- `post.pais`

## Acciones Detectadas

- `fnjs_update_div`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
