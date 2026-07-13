---
id: "actividadplazas.plazas_balance_que.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadplazas"
nombre: "Flujo - Gestionar Plazas Balance Que"
capacidad: "actividadplazas.plazas_balance_que.gestionar"
pantallas_principales: []
fragmentos: ["actividadplazas.pantalla.plazas_balance_que"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadplazas/plazas_balance_que_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Plazas Balance Que

Pantalla de filtro para el balance de plazas: carga las delegaciones comparables y el tipo de
actividad, y dispara la comparativa al elegir una dl.

## Objetivo De Usuario

Acceder al balance de plazas entre delegaciones, elegir con qué dl comparar la propia, y ver el grid
comparativo que se carga debajo.

## Punto De Entrada

Menú de plazas → **Balance de plazas** (ver "Ruta de menú"). Se abre
`actividadplazas.pantalla.plazas_balance_que`.

## Fragmentos O Pantallas Auxiliares

- `actividadplazas.pantalla.plazas_balance_que`
- `actividadplazas.pantalla.plazas_balance_dl` (grid cargado por AJAX)

## Escenarios Inferidos

### Obtener Datos

1. Abrir **Balance de plazas** desde el menú (según tipo y colectivo).
2. El sistema carga `plazas_balance_que_data`: opciones del desplegable de delegaciones e
   `id_tipo_activ` del contexto (`sactividad`/`sasistentes`).
3. Al elegir una delegación (`fnjs_comparativa`), solicita por AJAX `plazas_balance_dl` e inserta el
   HTML en `#comparativa` (flujo `plazas_balance`).

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
