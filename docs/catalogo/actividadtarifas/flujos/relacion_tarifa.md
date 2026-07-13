---
id: "actividadtarifas.relacion_tarifa.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadtarifas"
nombre: "Flujo - Gestionar Relacion Tarifa"
capacidad: "actividadtarifas.relacion_tarifa.gestionar"
pantallas_principales: ["actividadtarifas.pantalla.tarifa_tipo_actividad"]
fragmentos: ["actividadtarifas.pantalla.tarifa_tipo_actividad_form", "actividadtarifas.pantalla.tarifa_tipo_actividad_lista"]
acciones: ["crear_actualizar", "eliminar", "listar", "ver_formulario"]
endpoints: ["/src/actividadtarifas/relacion_tarifa_eliminar", "/src/actividadtarifas/relacion_tarifa_form_data", "/src/actividadtarifas/relacion_tarifa_lista_data", "/src/actividadtarifas/relacion_tarifa_update"]
estado_revision: "revisado"
---

# Flujo - Gestionar Relacion Tarifa

Definir qué tipo de tarifa del catálogo corresponde a cada tipo de actividad.

## Objetivo De Usuario

Definir qué tipo de tarifa del catálogo corresponde a cada tipo de actividad.

Plantilla de redacción revisada en `docs/manual/actividadtarifas.md` (sección Relacion Tarifa).

## Punto De Entrada

- `actividadtarifas.pantalla.tarifa_tipo_actividad`

## Fragmentos O Pantallas Auxiliares

- `actividadtarifas.pantalla.tarifa_tipo_actividad_form`
- `actividadtarifas.pantalla.tarifa_tipo_actividad_lista`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/actividadtarifas/relacion_tarifa_update`

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/actividadtarifas/relacion_tarifa_eliminar`

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Endpoints asociados:
- `/src/actividadtarifas/relacion_tarifa_lista_data`

### Ver Formulario

Pasos propuestos:
1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Endpoints asociados:
- `/src/actividadtarifas/relacion_tarifa_form_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_item`
- `form.id_tarifa`
- `form.id_tipo_activ`
- `form.inom_tipo_val`
- `form.isfsv_val`
- `post.id_item`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_guardar`
- `fnjs_id_activ`
- `fnjs_modificar`
- `fnjs_update_div`
- `fnjs_ver`

## Endpoints Del Flujo

- `/src/actividadtarifas/relacion_tarifa_eliminar`
- `/src/actividadtarifas/relacion_tarifa_form_data`
- `/src/actividadtarifas/relacion_tarifa_lista_data`
- `/src/actividadtarifas/relacion_tarifa_update`

## Errores Conocidos

- `debe indicar el tipo de actividad`
- `debe indicar la tarifa`
- `hay un error, no se ha borrado`
- `hay un error, no se ha guardado`
- `no se encuentra la relación`
- `no sé cuál he de borrar`

## Permisos

- Modificar fila: `have_perm_oficina('adl')` y sección del tipo de actividad coincidente.
- Alta: `adl`, `pr` o `calendario`. Formulario usa `HashFront` (no HashB).

## Ruta de menú

- **Legacy:** adl > Tarifas > tarifa <-> tipo de actividad; dre/Calendario/exterior > Tarifas > tarifa <-> tipo actividad.
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Tarifas > Tarifa-tipo actividad.
