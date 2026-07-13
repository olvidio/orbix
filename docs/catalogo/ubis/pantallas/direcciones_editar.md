---
id: "ubis.pantalla.direcciones_editar"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "ubis"
nombre: "Direcciones Editar"
controller: "frontend/ubis/controller/direcciones_editar.php"
vistas: ["frontend/ubis/view/direccion_form.phtml"]
fragmentos_frontend: ["frontend/ubis/controller/direccion_update.php", "frontend/ubis/controller/direcciones_asignar.php", "frontend/ubis/controller/direcciones_editar.php", "frontend/ubis/controller/direcciones_que.php", "frontend/ubis/controller/direcciones_quitar.php", "frontend/ubis/controller/info_ubis.php", "frontend/ubis/controller/plano_bytea.php"]
endpoints: ["/src/ubis/direcciones_editar"]
capacidades: ["ubis.direcciones_editar.gestionar"]
campos: ["form.a_p", "form.act", "form.c_p", "form.direccion", "form.f_direccion", "form.id_direccion", "form.id_ubi", "form.latitud", "form.longitud", "form.nom_sede", "form.obj_dir", "form.observ", "form.pais", "form.poblacion", "form.provincia", "form.que", "html.a_p", "html.c_p", "html.cp_dcha", "html.direccion", "html.f_direccion", "html.latitud", "html.longitud", "html.nom_sede", "html.observ", "html.pais", "html.poblacion", "html.principal", "html.propietario", "html.provincia", "html.que", "post.id_direccion", "post.id_ubi", "post.idx", "post.inc", "post.mod", "post.obj_dir", "post.refresh"]
acciones: ["fnjs_add_dir", "fnjs_adjuntar", "fnjs_asignar_dir", "fnjs_eliminar", "fnjs_guardar_dir", "fnjs_otro", "fnjs_quitar_dir", "fnjs_update_div", "fnjs_ver_dir", "fnjs_ver_documento"]
estado_revision: "revisado"
---

# Direcciones Editar

Muestra y edita las direcciones vinculadas a un ubi dentro de la ficha.

## Tipo

- Subtipo: `fragmento_ajax`


- Controller: `frontend/ubis/controller/direcciones_editar.php`

## Vistas Relacionadas

- `frontend/ubis/view/direccion_form.phtml`

## Fragmentos Frontend Relacionados

- `frontend/ubis/controller/direccion_update.php`
- `frontend/ubis/controller/direcciones_asignar.php`
- `frontend/ubis/controller/direcciones_editar.php`
- `frontend/ubis/controller/direcciones_que.php`
- `frontend/ubis/controller/direcciones_quitar.php`
- `frontend/ubis/controller/info_ubis.php`
- `frontend/ubis/controller/plano_bytea.php`

## Endpoints Usados

- `/src/ubis/direcciones_editar`

## Capacidades Relacionadas

- `ubis.direcciones_editar.gestionar`

## Campos Detectados

- `form.a_p`
- `form.act`
- `form.c_p`
- `form.direccion`
- `form.f_direccion`
- `form.id_direccion`
- `form.id_ubi`
- `form.latitud`
- `form.longitud`
- `form.nom_sede`
- `form.obj_dir`
- `form.observ`
- `form.pais`
- `form.poblacion`
- `form.provincia`
- `form.que`
- `html.a_p`
- `html.c_p`
- `html.cp_dcha`
- `html.direccion`
- `html.f_direccion`
- `html.latitud`
- `html.longitud`
- `html.nom_sede`
- `html.observ`
- `html.pais`
- `html.poblacion`
- `html.principal`
- `html.propietario`
- `html.provincia`
- `html.que`
- `post.id_direccion`
- `post.id_ubi`
- `post.idx`
- `post.inc`
- `post.mod`
- `post.obj_dir`
- `post.refresh`

## Acciones Detectadas

- `fnjs_add_dir`
- `fnjs_adjuntar`
- `fnjs_asignar_dir`
- `fnjs_eliminar`
- `fnjs_guardar_dir`
- `fnjs_otro`
- `fnjs_quitar_dir`
- `fnjs_update_div`
- `fnjs_ver_dir`
- `fnjs_ver_documento`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
