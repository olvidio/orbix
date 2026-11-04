---
id: "casas.casa_ingreso.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "casas"
nombre: "Flujo - Anotar el ingreso de una actividad"
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

Anotar o corregir los ingresos de una actividad en una casa.

## Punto De Entrada

Estudio económico de la casa > actividad.

## Fragmentos O Pantallas Auxiliares

- `casas.pantalla.casa_ingreso_form`

## Escenarios Inferidos

### Anotar el ingreso de una actividad

Pasos propuestos:
1. Abrir el estudio económico de la casa y elegir la actividad.
2. Rellenar los importes del formulario de ingreso y guardar.
3. Para quitar el ingreso, usar eliminar y confirmar.

Endpoints asociados:
- `/src/casas/casa_ingreso_eliminar`
- `/src/casas/casa_ingreso_form_data`
- `/src/casas/casa_ingreso_update`

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

