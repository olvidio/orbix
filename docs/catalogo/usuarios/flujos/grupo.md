---
id: "usuarios.grupo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Grupo"
capacidad: "usuarios.grupo.gestionar"
pantallas_principales: []
fragmentos: ["usuarios.pantalla.grupo_lista"]
acciones: ["eliminar", "guardar", "listar"]
endpoints: ["/src/usuarios/grupo_eliminar", "/src/usuarios/grupo_guardar", "/src/usuarios/grupo_lista"]
estado_revision: "revisado"
---

# Flujo - Grupo

## Objetivo De Usuario

Administración de grupos de permisos: listar, alta/edición y borrado.

## Punto De Entrada

Menú Legacy: sistema > usuarios web > grupos. Pills2: ADMIN LOCAL > usuarios web > grupos.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.grupo_lista`

## Escenarios Inferidos

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/usuarios/grupo_eliminar`

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
- `/src/usuarios/grupo_lista`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.sel`
- `form.username`
- `html.btn_ok`
- `post.id_sel`
- `post.scroll_id`
- `post.stack`
- `post.username`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_buscar`
- `fnjs_eliminar`
- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/usuarios/grupo_eliminar`
- `/src/usuarios/grupo_guardar`
- `/src/usuarios/grupo_lista`

## Errores Conocidos

- `Grupo no encontrado`
- `hay un error, no se ha eliminado`

## Ruta de menú

- **Legacy:** sistema > usuarios web > grupos
- **Pills2:** ADMIN LOCAL > usuarios web > grupos
