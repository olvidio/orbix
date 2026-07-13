---
id: "usuarios.role.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Role"
capacidad: "usuarios.role.gestionar"
pantallas_principales: []
fragmentos: ["usuarios.pantalla.role_form", "usuarios.pantalla.role_lista"]
acciones: ["eliminar", "guardar", "listar"]
endpoints: ["/src/usuarios/role_eliminar", "/src/usuarios/role_guardar", "/src/usuarios/role_lista"]
estado_revision: "revisado"
---

# Flujo - Role

## Objetivo De Usuario

Administración de roles: listar, crear/editar flags sf/sv/pau/dmz y asignar grupmenus.

## Punto De Entrada

Menú Legacy: sistema > usuarios web > lista de roles. Pills2: ADMIN LOCAL > usuarios web > lista de roles.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.role_form`
- `usuarios.pantalla.role_lista`

## Escenarios Inferidos

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/usuarios/role_eliminar`

### Guardar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Endpoints asociados:
- `/src/usuarios/role_lista`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.dmz`
- `form.pau`
- `form.que`
- `form.role`
- `form.sel`
- `form.sf`
- `form.sv`
- `html.dmz`
- `html.que`
- `html.role`
- `html.sf`
- `html.sv`
- `post.id_role`
- `post.id_sel`
- `post.que`
- `post.refresh`
- `post.scroll_id`
- `post.sel`
- `post.stack`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_add_grupmenu`
- `fnjs_del_grupmenu`
- `fnjs_eliminar`
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_left_side_hide`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/usuarios/role_eliminar`
- `/src/usuarios/role_guardar`
- `/src/usuarios/role_lista`

## Errores Conocidos

- `no existe el registro`
- `hay un error, no se ha eliminado`

## Ruta de menú

- **Legacy:** sistema > usuarios web > lista de roles
- **Pills2:** ADMIN LOCAL > usuarios web > lista de roles
