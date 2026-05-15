---
id: "actividadtarifas.tarifa_ubi.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadtarifas"
nombre: "Flujo - Gestionar Tarifa Ubi"
capacidad: "actividadtarifas.tarifa_ubi.gestionar"
pantallas_principales: ["actividadtarifas.pantalla.tarifa_ubi"]
fragmentos: ["actividadtarifas.pantalla.tarifa_ubi_form", "actividadtarifas.pantalla.tarifa_ubi_lista"]
acciones: ["actualizar_incremento", "copiar", "crear_actualizar", "eliminar", "listar", "ver_formulario"]
endpoints: ["/src/actividadtarifas/tarifa_ubi_copiar", "/src/actividadtarifas/tarifa_ubi_eliminar", "/src/actividadtarifas/tarifa_ubi_form_data", "/src/actividadtarifas/tarifa_ubi_lista_data", "/src/actividadtarifas/tarifa_ubi_update", "/src/actividadtarifas/tarifa_ubi_update_inc"]
estado_revision: "generado"
---

# Flujo - Gestionar Tarifa Ubi

Propuesta generada automaticamente desde la capacidad `actividadtarifas.tarifa_ubi.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Pendiente de revisar. Redactar aqui el objetivo en lenguaje de usuario, no tecnico.

## Punto De Entrada

- `actividadtarifas.pantalla.tarifa_ubi`

## Fragmentos O Pantallas Auxiliares

- `actividadtarifas.pantalla.tarifa_ubi_form`
- `actividadtarifas.pantalla.tarifa_ubi_lista`

## Escenarios Inferidos

### Actualizar Incremento

Pasos propuestos:
1. Abrir la pantalla o proceso que permite actualizacion en lote.
2. Revisar el conjunto de registros afectados.
3. Ejecutar la actualizacion.
4. Comprobar importes o valores recalculados.

Endpoints asociados:
- `/src/actividadtarifas/tarifa_ubi_update_inc`

### Copiar

Pasos propuestos:
1. Abrir el listado en el contexto origen/destino correspondiente.
2. Pulsar la accion de copiar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que los datos copiados aparecen en el listado.

Endpoints asociados:
- `/src/actividadtarifas/tarifa_ubi_copiar`

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/actividadtarifas/tarifa_ubi_update`

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/actividadtarifas/tarifa_ubi_eliminar`

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Endpoints asociados:
- `/src/actividadtarifas/tarifa_ubi_lista_data`

### Ver Formulario

Pasos propuestos:
1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Endpoints asociados:
- `/src/actividadtarifas/tarifa_ubi_form_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.cantidad`
- `form.id_item`
- `form.id_serie`
- `form.id_tarifa`
- `form.id_ubi`
- `form.letra`
- `form.year`
- `html.buscar`
- `html.cantidad`
- `html.ctx_eliminar`
- `html.ctx_update`
- `html.id_item`
- `html.id_ubi`
- `html.year`
- `post.id_item`
- `post.id_ubi`
- `post.letra`
- `post.year`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_comprobar_dinero`
- `fnjs_copiar_tarifas`
- `fnjs_guardar`
- `fnjs_modificar`
- `fnjs_update_div`
- `fnjs_ver`

## Endpoints Del Flujo

- `/src/actividadtarifas/tarifa_ubi_copiar`
- `/src/actividadtarifas/tarifa_ubi_eliminar`
- `/src/actividadtarifas/tarifa_ubi_form_data`
- `/src/actividadtarifas/tarifa_ubi_lista_data`
- `/src/actividadtarifas/tarifa_ubi_update`
- `/src/actividadtarifas/tarifa_ubi_update_inc`

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
