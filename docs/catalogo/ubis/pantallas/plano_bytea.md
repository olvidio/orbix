---
id: "ubis.pantalla.plano_bytea"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "ubis"
nombre: "Plano Bytea"
controller: "frontend/ubis/controller/plano_bytea.php"
vistas: []
fragmentos_frontend: ["frontend/ubis/controller/plano_bytea.php"]
endpoints: []
capacidades: []
campos: ["form.act", "form.id_direccion", "form.obj_dir", "get.act", "get.id_direccion", "get.obj_dir", "html.name_file", "html.userfile", "post.act", "post.id_direccion", "post.obj_dir"]
acciones: ["fnjs_buscar", "fnjs_introducir"]
estado_revision: "generado"
---

# Plano Bytea

Página que pregunta dónde está la foto, y la copia en la base de datos

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/ubis/controller/plano_bytea.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/ubis/controller/plano_bytea.php`

## Endpoints Usados

No se han detectado endpoints `/src/...`.

## Capacidades Relacionadas

No se han detectado capacidades relacionadas.

## Campos Detectados

- `form.act`
- `form.id_direccion`
- `form.obj_dir`
- `get.act`
- `get.id_direccion`
- `get.obj_dir`
- `html.name_file`
- `html.userfile`
- `post.act`
- `post.id_direccion`
- `post.obj_dir`

## Acciones Detectadas

- `fnjs_buscar`
- `fnjs_introducir`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
