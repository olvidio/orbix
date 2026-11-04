---
id: "actividadplazas.plazas_balance_que.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadplazas"
nombre: "Flujo - Abrir el balance de plazas"
capacidad: "actividadplazas.plazas_balance_que.gestionar"
pantallas_principales: ["actividadplazas.pantalla.plazas_balance_que"]
fragmentos: ["actividadplazas.pantalla.plazas_balance_que"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadplazas/plazas_balance_que_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Plazas Balance Que

Pantalla de filtro para el balance de plazas: carga las delegaciones comparables y el tipo de
actividad, y dispara la comparativa al elegir una dl.

## Objetivo De Usuario

Entrar en el balance de plazas entre delegaciones y elegir con qué delegación comparar la propia.

## Punto De Entrada

Menú de plazas > **Balance de plazas**.

## Fragmentos O Pantallas Auxiliares

- `actividadplazas.pantalla.plazas_balance_que`
- `actividadplazas.pantalla.plazas_balance_dl` (grid cargado por AJAX)

## Escenarios Inferidos

### Abrir el balance de plazas

Pasos propuestos:
1. Abrir **Balance de plazas** desde el menú (según el tipo de actividad y el colectivo).
2. Elegir la delegación en el desplegable.
3. La comparación aparece debajo, sin salir de la pantalla (ver «Comparar con otra delegación»).

Endpoints asociados:
- `/src/actividadplazas/plazas_balance_que_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.dl`
- `form.id_tipo_activ`
- `post.id_tipo_activ`
- `post.sactividad`
- `post.sasistentes`

Acciones JavaScript:
- `fnjs_comparativa`

## Endpoints Del Flujo

- `/src/actividadplazas/plazas_balance_que_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** vsm > ca > Balance de plazas (y variantes por perfil/tipo: dagd, vsg…)
- **Pills2:** ACTIVIDADES > Gestión de plazas y peticiones > Balance plazas ca n entre r/dl (y variantes por tipo/colectivo)
