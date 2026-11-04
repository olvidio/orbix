---
id: "usuarios.perm_menu.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Añadir un permiso de menú"
capacidad: "usuarios.perm_menu.gestionar"
pantallas_principales: ["usuarios.pantalla.perm_menu_form"]
fragmentos: ["usuarios.pantalla.grupo_form", "usuarios.pantalla.perm_menu_form"]
acciones: ["eliminar", "guardar", "listar"]
endpoints: ["/src/usuarios/perm_menu_eliminar", "/src/usuarios/perm_menu_guardar", "/src/usuarios/perm_menu_lista"]
estado_revision: "revisado"
---

# Flujo - Perm Menu

## Objetivo De Usuario

Ver, añadir y quitar los permisos sobre menús de un usuario desde su ficha.

## Punto De Entrada

Ficha del usuario, apartado de permisos de menú.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.grupo_form`
- `usuarios.pantalla.perm_menu_form`

## Escenarios Inferidos

### Añadir un permiso de menú

Pasos propuestos:
1. Abrir la ficha del usuario.
2. Pulsar **añadir grup menu** y elegir el grupo de menús.
3. Guardar.

Endpoints asociados:
- `/src/usuarios/perm_menu_eliminar`
- `/src/usuarios/perm_menu_guardar`
- `/src/usuarios/perm_menu_lista`

### Quitar un permiso de menú

Pasos propuestos:
1. Elegir la fila en la tabla de la ficha y pulsar quitar.
2. Confirmar «¿Esta seguro que desea borrar este permiso?».

Endpoints asociados:
- `/src/usuarios/perm_menu_eliminar`
- `/src/usuarios/perm_menu_guardar`
- `/src/usuarios/perm_menu_lista`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.menu_perm`
- `form.que`
- `form.sel`
- `form.usuario`
- `html.que`
- `html.refresh`
- `post.id_item`
- `post.id_usuario`
- `post.que`
- `post.refresh`
- `post.scroll_id`
- `post.sel`
- `post.stack`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_add_perm_menu`
- `fnjs_del_perm_menu`
- `fnjs_enviar_formulario`
- `fnjs_grabar`
- `fnjs_guardar`
- `fnjs_solo_uno`

## Endpoints Del Flujo

- `/src/usuarios/perm_menu_eliminar`
- `/src/usuarios/perm_menu_guardar`
- `/src/usuarios/perm_menu_lista`

## Errores Conocidos

- `no existe el registro`
- `hay un error, no se ha eliminado`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
