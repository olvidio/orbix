---
id: "actividadtarifas.tipo_tarifa.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadtarifas"
nombre: "Flujo - Gestionar Tipo Tarifa"
capacidad: "actividadtarifas.tipo_tarifa.gestionar"
pantallas_principales: ["actividadtarifas.pantalla.tarifa"]
fragmentos: ["actividadtarifas.pantalla.tarifa_form", "actividadtarifas.pantalla.tarifa_lista"]
acciones: ["crear_actualizar", "eliminar", "listar", "ver_formulario"]
endpoints: ["/src/actividadtarifas/tipo_tarifa_eliminar", "/src/actividadtarifas/tipo_tarifa_form_data", "/src/actividadtarifas/tipo_tarifa_lista_data", "/src/actividadtarifas/tipo_tarifa_update"]
estado_revision: "generado"
---

# Flujo - Gestionar Tipo Tarifa

Propuesta generada automaticamente desde la capacidad `actividadtarifas.tipo_tarifa.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Mantener el catálogo maestro de tipos de tarifa (letra, modo, observaciones).

Plantilla de redacción revisada en `docs/manual/actividadtarifas.md` (sección Tipo Tarifa).

## Punto De Entrada

- `actividadtarifas.pantalla.tarifa`

## Fragmentos O Pantallas Auxiliares

- `actividadtarifas.pantalla.tarifa_form`
- `actividadtarifas.pantalla.tarifa_lista`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/actividadtarifas/tipo_tarifa_update`

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/actividadtarifas/tipo_tarifa_eliminar`

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Endpoints asociados:
- `/src/actividadtarifas/tipo_tarifa_lista_data`

### Ver Formulario

Pasos propuestos:
1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Endpoints asociados:
- `/src/actividadtarifas/tipo_tarifa_form_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_tarifa`
- `form.letra`
- `form.modo`
- `form.observ`
- `html.id_tarifa`
- `html.letra`
- `html.observ`
- `post.id_tarifa`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_guardar`
- `fnjs_modificar`
- `fnjs_update_div`
- `fnjs_ver`

## Endpoints Del Flujo

- `/src/actividadtarifas/tipo_tarifa_eliminar`
- `/src/actividadtarifas/tipo_tarifa_form_data`
- `/src/actividadtarifas/tipo_tarifa_lista_data`
- `/src/actividadtarifas/tipo_tarifa_update`

## Errores Conocidos

- ``hay un error, no se ha borrado``
- ``hay un error, no se ha guardado``
- ``no se encuentra la tarifa``
- ``no sé cuál he de borrar``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
