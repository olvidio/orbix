---
id: "actividadplazas.plazas_balance.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadplazas"
nombre: "Flujo - Gestionar Plazas Balance"
capacidad: "actividadplazas.plazas_balance.gestionar"
pantallas_principales: []
fragmentos: ["actividadplazas.pantalla.plazas_balance_dl"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadplazas/plazas_balance_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Plazas Balance

Carga el grid comparativo A vs B (mi dl frente a otra delegación) con plazas concedidas y libres por
actividad, insertado en la pantalla de balance.

## Objetivo De Usuario

Comparar, para un tipo de actividad, cuántas plazas concedidas y libres tiene cada actividad en mi
delegación frente a otra delegación elegida en el desplegable.

## Punto De Entrada

Fragmento `plazas_balance_dl`, invocado por AJAX desde `plazas_balance_que` al elegir una delegación
en el desplegable (`fnjs_comparativa`). No tiene menú propio.

## Fragmentos O Pantallas Auxiliares

- `actividadplazas.pantalla.plazas_balance_dl`
- `actividadplazas.pantalla.plazas_balance_que` (contenedor)

## Escenarios Inferidos

### Obtener Datos

1. En **Balance de plazas**, elegir la delegación a comparar en el desplegable.
2. El sistema carga el HTML del grid en `#comparativa` vía `plazas_balance_dl.php`.
3. Ese fragmento obtiene los datos de `plazas_balance_data` (`dlA` = mi dl, `dlB` = la elegida):
   cabecera con concedidas cruzadas (A→B y B→A) y tabla con concedidas/libres por actividad.
4. Las celdas de mi dl son editables (doble clic → `gestion_plazas_update`).

Endpoints asociados:
- `/src/actividadplazas/plazas_balance_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.colName`
- `form.data`
- `post.dl`
- `post.id_tipo_activ`

Acciones JavaScript:
- Ninguna detectada (la edición delega en `TablaEditable` del fragmento).

## Endpoints Del Flujo

- `/src/actividadplazas/plazas_balance_data`

## Errores Conocidos

- `falta parametro dl`
- `no se puede comparar una dl consigo misma`

## Ruta de menú

- Sin entrada de menú en el índice: fragmento invocado desde **Balance de plazas** (`plazas_balance_que`):
  - **Legacy:** vsm > ca > Balance de plazas (y variantes por perfil/tipo: dagd, vsg…)
  - **Pills2:** ACTIVIDADES > Gestión de plazas y peticiones > Balance plazas ca n entre r/dl (y variantes por tipo/colectivo)
