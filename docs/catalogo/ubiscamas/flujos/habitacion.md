---
id: "ubiscamas.habitacion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubiscamas"
nombre: "Flujo - Gestionar Habitacion"
capacidad: "ubiscamas.habitacion.gestionar"
pantallas_principales: []
fragmentos: ["ubiscamas.pantalla.habitacion_form"]
acciones: ["crear_actualizar", "eliminar", "ver_formulario"]
endpoints: ["/src/ubiscamas/habitacion_delete", "/src/ubiscamas/habitacion_form_data", "/src/ubiscamas/habitacion_update"]
estado_revision: "revisado"
---

# Flujo - Habitacion

## Objetivo De Usuario

Dar de alta, modificar o eliminar habitaciones de un ubi CDC, incluyendo creación automática de camas según número indicado.

## Punto De Entrada

Dossier CDC habitaciones (`select_habitaciones_cdc` en ficha ubi) o navegación desde formulario de habitación. Sin entrada de menú en el índice.

## Fragmentos O Pantallas Auxiliares

- `ubiscamas.pantalla.habitacion_form`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/ubiscamas/habitacion_update`

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/ubiscamas/habitacion_delete`

### Ver Formulario

Pasos propuestos:
1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Endpoints asociados:
- `/src/ubiscamas/habitacion_form_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.adaptada`
- `html.despacho`
- `html.new_camas_desc[${rowIdx}]`
- `html.new_camas_larga[${rowIdx}]`
- `html.new_camas_vip[${rowIdx}]`
- `html.nombre`
- `html.numero_camas`
- `html.numero_camas_vip`
- `html.observaciones`
- `html.orden`
- `html.planta`
- `html.refresh`
- `html.sillon`
- `html.tipoLavabo`
- `post.refresh`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_anadir_cama_dinamica`
- `fnjs_cancelar`
- `fnjs_editar_cama`
- `fnjs_eliminar_cama`
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_nueva_cama`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/ubiscamas/habitacion_delete`
- `/src/ubiscamas/habitacion_form_data`
- `/src/ubiscamas/habitacion_update`

## Errores Conocidos

- `No se encontró la habitación a eliminar`
- `hay un error, no se ha eliminado la habitación`
- `Error al eliminar la habitación`
- `Habitación no válida`
- `Error al guardar la habitación`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
