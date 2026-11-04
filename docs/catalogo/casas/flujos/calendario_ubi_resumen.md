---
id: "casas.calendario_ubi_resumen.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "casas"
nombre: "Flujo - Consultar la previsión económica de una casa"
capacidad: "casas.calendario_ubi_resumen.gestionar"
pantallas_principales: ["casas.pantalla.calendario_ubi_resumen"]
fragmentos: ["casas.pantalla.calendario_ubi_resumen", "casas.pantalla.calendario_ubi_resumen_body"]
acciones: ["obtener_datos"]
endpoints: ["/src/casas/calendario_ubi_resumen_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Calendario Ubi Resumen

Propuesta generada automaticamente desde la capacidad `casas.calendario_ubi_resumen.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Ver el estudio económico de una casa: ocupación, ingresos y gastos.

## Punto De Entrada

Menú **adl > Nuevo Calendario > Previsión económica** (Calendario > Nuevo calendario > Previsión económica).

## Fragmentos O Pantallas Auxiliares

- `casas.pantalla.calendario_ubi_resumen`
- `casas.pantalla.calendario_ubi_resumen_body`

## Escenarios Inferidos

### Consultar la previsión económica de una casa

Pasos propuestos:
1. Entrar en **Previsión económica**.
2. Elegir la casa y el periodo.
3. Se muestra el estudio económico.

Endpoints asociados:
- `/src/casas/calendario_ubi_resumen_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.G`
- `form.id_ubi`
- `form.inc_cantidad`
- `form.inc_t`
- `form.seccion`
- `form.year`
- `html.G`
- `html.id_ubi`
- `html.inc_t`
- `html.seccion`
- `html.year`
- `post.G`
- `post.id_ubi`
- `post.inc_t`
- `post.seccion`

Acciones JavaScript:
- `button:grabar tarifas`
- `button:resumen sf`
- `button:resumen sv`
- `fnjs_guardar`
- `fnjs_update_div`
- `fnjs_ver`

## Endpoints Del Flujo

- `/src/casas/calendario_ubi_resumen_data`

## Errores Conocidos

No se han documentado errores en la capacidad.
## Ruta de menú

- **Legacy:** adl > Nuevo Calendario > Previsión económica
- **Pills2:** ACTIVIDADES > Estadísticas económicas > Previsión económica

