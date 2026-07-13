---
id: "ubiscamas.cama.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubiscamas"
nombre: "Flujo - Gestionar Cama"
capacidad: "ubiscamas.cama.gestionar"
pantallas_principales: []
fragmentos: ["ubiscamas.pantalla.cama_form"]
acciones: ["crear_actualizar", "eliminar", "ver_formulario"]
endpoints: ["/src/ubiscamas/cama_delete", "/src/ubiscamas/cama_form_data", "/src/ubiscamas/cama_update"]
estado_revision: "revisado"
---

# Flujo - Cama

## Objetivo De Usuario

Crear, editar o eliminar camas individuales asociadas a una habitación.

## Punto De Entrada

Modal invocado desde `habitacion_form` (editar/nueva cama). Sin entrada de menú en el índice.

## Fragmentos O Pantallas Auxiliares

- `ubiscamas.pantalla.cama_form`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/ubiscamas/cama_update`

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/ubiscamas/cama_delete`

### Ver Formulario

Pasos propuestos:
1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Endpoints asociados:
- `/src/ubiscamas/cama_form_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.descripcion`
- `html.larga`
- `html.vip`

Acciones JavaScript:
- `fnjs_cancelar`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/ubiscamas/cama_delete`
- `/src/ubiscamas/cama_form_data`
- `/src/ubiscamas/cama_update`

## Errores Conocidos

- `ID de cama no proporcionado`
- `No se encontró la cama a eliminar`
- `hay un error, no se ha eliminado la cama`
- `Error al eliminar la cama`
- `Habitación no válida`
- `Cama no válida`
- `Error al guardar la cama`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
