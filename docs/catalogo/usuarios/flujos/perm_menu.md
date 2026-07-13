---
id: "usuarios.perm_menu.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Perm Menu"
capacidad: "usuarios.perm_menu.gestionar"
pantallas_principales: []
fragmentos: ["usuarios.pantalla.grupo_form", "usuarios.pantalla.perm_menu_form"]
acciones: ["eliminar", "guardar", "listar"]
endpoints: ["/src/usuarios/perm_menu_eliminar", "/src/usuarios/perm_menu_guardar", "/src/usuarios/perm_menu_lista"]
estado_revision: "revisado"
---

# Flujo - Perm Menu

## Objetivo De Usuario

Gestión permisos menú DL de un usuario desde su ficha.

## Punto De Entrada

Sin entrada de menú directa; login, preferencias personales o fragmento/modal desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.grupo_form`
- `usuarios.pantalla.perm_menu_form`

## Escenarios Inferidos

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/usuarios/perm_menu_eliminar`

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
