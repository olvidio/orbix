---
id: "casas.casa_ingreso.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "casas"
nombre: "Flujo - Gestionar Casa Ingreso"
capacidad: "casas.casa_ingreso.gestionar"
pantallas_principales: ["casas.pantalla.casa"]
fragmentos: ["casas.pantalla.casa_ingreso_form"]
acciones: ["crear_actualizar", "eliminar", "ver_formulario"]
endpoints: ["/src/casas/casa_ingreso_eliminar", "/src/casas/casa_ingreso_form_data", "/src/casas/casa_ingreso_update"]
estado_revision: "revisado"
---

# Flujo - Gestionar Casa Ingreso

Propuesta generada automaticamente desde la capacidad `casas.casa_ingreso.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CasaIngreso. Crear/actualizar el Ingreso de una actividad. Datos para el formulario de ingreso de una actividad (casa_ingreso_form). Eliminar el Ingreso de una actividad.

## Punto De Entrada

- `casas.pantalla.casa`

## Fragmentos O Pantallas Auxiliares

- `casas.pantalla.casa_ingreso_form`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/casas/casa_ingreso_update`

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/casas/casa_ingreso_eliminar`

### Ver Formulario

Pasos propuestos:
1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Endpoints asociados:
- `/src/casas/casa_ingreso_form_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_activ`
- `form.id_tarifa`
- `form.ingresos`
- `form.num_asistentes`
- `form.observ`
- `form.precio`
- `get.id_activ`
- `html.buscar`
- `html.id_activ`
- `html.ingresos`
- `html.num_asistentes`
- `html.observ`
- `html.precio`
- `post.id_activ`
- `post.id_ubi`
- `post.periodo`
- `post.tipo_lista`
- `post.ver_ctr`
- `post.year`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_guardar`
- `fnjs_mas_casas`
- `fnjs_modificar`
- `fnjs_update_div`
- `fnjs_ver`

## Endpoints Del Flujo

- `/src/casas/casa_ingreso_eliminar`
- `/src/casas/casa_ingreso_form_data`
- `/src/casas/casa_ingreso_update`

## Errores Conocidos

- `Falta id_activ`
- `Actividad no encontrada`
- `Hay un error, no se ha guardado la actividad.`
- `Hay un error, no se ha guardado.`
- `no sé cuál he de borar`
- `Ingreso no encontrado`
- `Hay un error, no se ha eliminado`
## Ruta de menú

- **Legacy:** exterior > casas > Gestión económica
- **Pills2:** CASAS Y CTR > Gestión casas > Gestión económica

