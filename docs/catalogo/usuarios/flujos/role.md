---
id: "usuarios.role.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Consultar los roles"
capacidad: "usuarios.role.gestionar"
pantallas_principales: ["usuarios.pantalla.role_lista"]
fragmentos: ["usuarios.pantalla.role_form", "usuarios.pantalla.role_lista"]
acciones: ["eliminar", "guardar", "listar"]
endpoints: ["/src/usuarios/role_eliminar", "/src/usuarios/role_guardar", "/src/usuarios/role_lista"]
estado_revision: "revisado"
---

# Flujo - Role

## Objetivo De Usuario

Consultar los roles y asignar a cada uno los grupos de menús de la delegación.

## Punto De Entrada

Menú **sistema > usuarios web > lista de roles** (Pills2: ADMIN LOCAL > usuarios web > lista de roles).

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.role_form`
- `usuarios.pantalla.role_lista`

## Escenarios Inferidos

### Consultar los roles

Pasos propuestos:
1. Entrar en **lista de roles**.
2. Los roles son comunes a todas las delegaciones y solo los modifica el superusuario.
3. Pulsar un rol para ver su ficha y los grupos de menús que tiene.

Endpoints asociados:
- `/src/usuarios/role_eliminar`
- `/src/usuarios/role_guardar`
- `/src/usuarios/role_lista`

### Asignar grupos de menús a un rol

Pasos propuestos:
1. Abrir la ficha del rol.
2. Pulsar **añadir grup menu** y marcar los grupos de menús que se quieren dar.
3. Guardar. Los grupos de menús son propios de cada delegación (sv y sf distintos) y los puede modificar el administrador.

Endpoints asociados:
- `/src/usuarios/role_eliminar`
- `/src/usuarios/role_guardar`
- `/src/usuarios/role_lista`

### Quitar un grupo de menús a un rol

Pasos propuestos:
1. En la ficha del rol, en la tabla de grupos de menús, elegir el que se quiere quitar y confirmar.

Endpoints asociados:
- `/src/usuarios/role_eliminar`
- `/src/usuarios/role_guardar`
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
