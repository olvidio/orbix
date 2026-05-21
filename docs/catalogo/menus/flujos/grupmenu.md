---
id: "menus.grupmenu.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Gestionar Grupmenu"
capacidad: "menus.grupmenu.gestionar"
pantallas_principales: []
fragmentos: ["menus.pantalla.grupmenu_lista", "menus.pantalla.menus_get", "menus.pantalla.menus_que"]
acciones: ["eliminar", "guardar", "listar"]
endpoints: ["/src/menus/grupmenu_eliminar", "/src/menus/grupmenu_guardar", "/src/menus/grupmenu_lista"]
estado_revision: "generado"
---

# Flujo - Gestionar Grupmenu

Propuesta generada automaticamente desde la capacidad `menus.grupmenu.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona GrupMenuListaUseCase. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `menus.pantalla.grupmenu_lista`
- `menus.pantalla.menus_get`
- `menus.pantalla.menus_que`

## Escenarios Inferidos

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/menus/grupmenu_eliminar`

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
- `/src/menus/grupmenu_lista`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.$campos_chk`
- `form.filtro_grupo`
- `form.gm_new`
- `form.id_menu`
- `form.id_metamenu`
- `form.orden`
- `form.parametros`
- `form.perm_menu`
- `form.sel`
- `form.txt_menu`
- `html.bnada`
- `html.btodo`
- `html.orden`
- `html.parametros`
- `html.txt_menu`
- `post.filtro_grupo`
- `post.id_menu`
- `post.nuevo`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_eliminar`
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_left_side_hide`
- `fnjs_lista_menus`
- `fnjs_modificar`
- `fnjs_selectAll`
- `fnjs_solo_uno`
- `fnjs_update_div`
- `fnjs_ver_ficha`

## Endpoints Del Flujo

- `/src/menus/grupmenu_eliminar`
- `/src/menus/grupmenu_guardar`
- `/src/menus/grupmenu_lista`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
