---
id: "actividadtarifas.tarifa_ubi.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadtarifas"
nombre: "Flujo - Consultar las tarifas de una casa"
capacidad: "actividadtarifas.tarifa_ubi.gestionar"
pantallas_principales: ["actividadtarifas.pantalla.tarifa_ubi"]
fragmentos: ["actividadtarifas.pantalla.tarifa_ubi_form", "actividadtarifas.pantalla.tarifa_ubi_lista"]
acciones: ["actualizar_incremento", "copiar", "crear_actualizar", "eliminar", "listar", "ver_formulario"]
endpoints: ["/src/actividadtarifas/tarifa_ubi_copiar", "/src/actividadtarifas/tarifa_ubi_eliminar", "/src/actividadtarifas/tarifa_ubi_form_data", "/src/actividadtarifas/tarifa_ubi_lista_data", "/src/actividadtarifas/tarifa_ubi_update", "/src/actividadtarifas/tarifa_ubi_update_inc"]
estado_revision: "revisado"
---

# Flujo - Gestionar Tarifa Ubi

Consultar y mantener tarifas económicas de una casa para un año.

## Objetivo De Usuario

Consultar y mantener las tarifas de una casa para un año: importes por tipo de tarifa y serie.

## Punto De Entrada

Menú **adl > Tarifas > tarifas por casa y año** (Calendario > Nuevo calendario > Tarifas > tarifas por casa y año).

## Fragmentos O Pantallas Auxiliares

- `actividadtarifas.pantalla.tarifa_ubi_form`
- `actividadtarifas.pantalla.tarifa_ubi_lista`

## Escenarios Inferidos

### Consultar las tarifas de una casa

Pasos propuestos:
1. Entrar en **tarifas por casa y año**.
2. Elegir la casa y el año y pulsar **buscar**.
3. Se listan sus tarifas con el importe en €.

Endpoints asociados:
- `/src/actividadtarifas/tarifa_ubi_copiar`
- `/src/actividadtarifas/tarifa_ubi_eliminar`
- `/src/actividadtarifas/tarifa_ubi_form_data`
- `/src/actividadtarifas/tarifa_ubi_lista_data`
- `/src/actividadtarifas/tarifa_ubi_update`
- `/src/actividadtarifas/tarifa_ubi_update_inc`

### Crear o modificar una tarifa de una casa

Pasos propuestos:
1. En el listado, pulsar nuevo (o la tarifa a cambiar).
2. Elegir la **tarifa**, la **serie** y escribir el importe (**nuevo importe**).
3. Pulsar **guardar**; **eliminar** la quita (con confirmación).

Endpoints asociados:
- `/src/actividadtarifas/tarifa_ubi_copiar`
- `/src/actividadtarifas/tarifa_ubi_eliminar`
- `/src/actividadtarifas/tarifa_ubi_form_data`
- `/src/actividadtarifas/tarifa_ubi_lista_data`
- `/src/actividadtarifas/tarifa_ubi_update`
- `/src/actividadtarifas/tarifa_ubi_update_inc`

### Copiar las tarifas del año anterior

Pasos propuestos:
1. Si aparece el botón de copiar, pulsarlo y confirmar.
2. Aviso: hoy la operación responde «función pendiente de reimplementar» (ver D-TAR-1).

Endpoints asociados:
- `/src/actividadtarifas/tarifa_ubi_copiar`
- `/src/actividadtarifas/tarifa_ubi_eliminar`
- `/src/actividadtarifas/tarifa_ubi_form_data`
- `/src/actividadtarifas/tarifa_ubi_lista_data`
- `/src/actividadtarifas/tarifa_ubi_update`
- `/src/actividadtarifas/tarifa_ubi_update_inc`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.cantidad`
- `form.id_serie`
- `form.id_tarifa`
- `html.buscar`
- `html.cantidad`
- `html.ctx_eliminar`
- `html.ctx_update`
- `post.ctx_form`
- `post.id_ubi`
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

## Errores Conocidos

- `Operación no autorizada` (cápsula HashB inválida en update/eliminar/copiar)
- `función de copiar tarifas pendiente de reimplementar`
- `hay un error, no se ha borrado`
- `hay un error, no se ha guardado`
- `no se encuentra la tarifa`
- `no sé cuál he de borrar`
- `no sé qué casa/año tengo que copiar`

## Permisos

- Mutaciones `TarifaUbi` vía HashB (`ctx_update`, `ctx_eliminar`, `ctx_copiar`).
- Visibilidad: `have_perm_oficina('adl')` para modificar; `adl`|`pr`|`calendario` para añadir/copiar.

## Ruta de menú

- **Legacy:** adl > Tarifas > tarifas por casa y año; Calendario/dre/exterior > Tarifas > tarifas por casa y año.
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Tarifas > Tarifas por casa y año.
