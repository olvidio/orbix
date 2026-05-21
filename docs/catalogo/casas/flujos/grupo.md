---
id: "casas.grupo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "casas"
nombre: "Flujo - Gestionar Grupo"
capacidad: "casas.grupo.gestionar"
pantallas_principales: ["casas.pantalla.grupo"]
fragmentos: ["casas.pantalla.grupo_form", "casas.pantalla.grupo_lista"]
acciones: ["crear_actualizar", "eliminar", "listar", "ver_formulario"]
endpoints: ["/src/casas/grupo_eliminar", "/src/casas/grupo_form_data", "/src/casas/grupo_lista_data", "/src/casas/grupo_update"]
estado_revision: "generado"
---

# Flujo - Gestionar Grupo

Propuesta generada automaticamente desde la capacidad `casas.grupo.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona GrupoCasa. Crea o actualiza un GrupoCasa. Datos del formulario GrupoCasa (nuevo/editar). Elimina un GrupoCasa. Listado de GrupoCasa (relaciones padre ↔ hijo).

## Punto De Entrada

- `casas.pantalla.grupo`

## Fragmentos O Pantallas Auxiliares

- `casas.pantalla.grupo_form`
- `casas.pantalla.grupo_lista`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/casas/grupo_update`

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/casas/grupo_eliminar`

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Endpoints asociados:
- `/src/casas/grupo_lista_data`

### Ver Formulario

Pasos propuestos:
1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Endpoints asociados:
- `/src/casas/grupo_form_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_item`
- `form.id_ubi_hijo`
- `form.id_ubi_padre`
- `html.cancelar`
- `html.id_item`
- `html.ok`
- `post.id_item`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_eliminar`
- `fnjs_guardar`
- `fnjs_modificar`
- `fnjs_update_div`
- `fnjs_ver`

## Endpoints Del Flujo

- `/src/casas/grupo_eliminar`
- `/src/casas/grupo_form_data`
- `/src/casas/grupo_lista_data`
- `/src/casas/grupo_update`

## Errores Conocidos

- ``Hay un error, no se ha eliminado.``
- ``Hay un error, no se ha guardado.``
- ``No puede ser la misma casa``
- ``debe indicar el grupo a eliminar``
- ``debe indicar las dos casas``
- ``no se encuentra el grupo``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
