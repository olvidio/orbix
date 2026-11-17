---
id: "ubis.pantalla.plano_bytea"
tipo: "pantalla_frontend"
subtipo: "descarga"
modulo: "ubis"
nombre: "Plano Bytea"
controller: "frontend/ubis/controller/plano_bytea.php"
vistas: []
fragmentos_frontend: ["frontend/ubis/controller/plano_bytea.php"]
endpoints: ["/src/ubis/plano_existe", "/src/ubis/plano_subir", "/src/ubis/plano_download"]
capacidades: []
campos: ["get.act", "get.id_direccion", "get.obj_dir", "get.ctx_plano_subir", "html.userfile", "post.act", "post.id_direccion", "post.obj_dir"]
acciones: ["fnjs_introducir", "fnjs_subir"]
estado_revision: "revisado"
---

# Plano Bytea

Plano (bytea) de una dirección. `act=comprobar` pregunta a `/src/ubis/plano_existe` y devuelve la URL de descarga firmada (`/src/ubis/plano_download?tk=`); `act=adjuntar` abre la ventana que sube el fichero por AJAX a `/src/ubis/plano_subir` con la cápsula `ctx_plano_subir`. Quitar el plano lo hace la ficha `direcciones_editar` contra `/src/ubis/plano_borrar`.

## Tipo

- Subtipo: `descarga`


- Controller: `frontend/ubis/controller/plano_bytea.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/ubis/controller/plano_bytea.php`

## Endpoints Usados

- `/src/ubis/plano_existe`
- `/src/ubis/plano_subir` (desde el navegador)
- `/src/ubis/plano_download` (desde el navegador, con `tk`)

## Capacidades Relacionadas

No se han detectado capacidades relacionadas.

## Campos Detectados

- `get.act`
- `get.id_direccion`
- `get.obj_dir`
- `get.ctx_plano_subir`
- `html.userfile`
- `post.act`
- `post.id_direccion`
- `post.obj_dir`

## Acciones Detectadas

- `fnjs_introducir`
- `fnjs_subir`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
