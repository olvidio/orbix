---
id: "actividadtarifas.relacion_tarifa.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadtarifas"
nombre: "Flujo - Asignar una tarifa a un tipo de actividad"
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

Decidir qué tipo de tarifa corresponde a cada tipo de actividad.

## Punto De Entrada

Menú **adl > Tarifas > tarifa <-> tipos actividad**.

## Fragmentos O Pantallas Auxiliares

- `actividadtarifas.pantalla.tarifa_tipo_actividad_form`
- `actividadtarifas.pantalla.tarifa_tipo_actividad_lista`

## Escenarios Inferidos

### Asignar una tarifa a un tipo de actividad

Pasos propuestos:
1. Entrar en **tarifa <-> tipos actividad**.
2. Elegir el tipo de actividad (obligatorio: «debe indicar el tipo de actividad») y la tarifa (obligatoria: «debe indicar la id_tarifa»).
3. Guardar. Para cambiar la relación existente, abrirla, elegir otra tarifa y guardar; para quitarla, eliminarla.

Endpoints asociados:
- `/src/actividadtarifas/relacion_tarifa_eliminar`
- `/src/actividadtarifas/relacion_tarifa_form_data`
- `/src/actividadtarifas/relacion_tarifa_lista_data`
- `/src/actividadtarifas/relacion_tarifa_update`

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
- Alta: `adl`, `pr` o `calendario`. Formulario usa `HashF` (no HashB).

## Ruta de menú

- **Legacy:** adl > Tarifas > tarifa <-> tipo de actividad; dre/Calendario/exterior > Tarifas > tarifa <-> tipo actividad.
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Tarifas > Tarifa-tipo actividad.
