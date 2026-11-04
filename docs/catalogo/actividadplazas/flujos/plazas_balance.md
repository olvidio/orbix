---
id: "actividadplazas.plazas_balance.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadplazas"
nombre: "Flujo - Comparar con otra delegación"
capacidad: "actividadplazas.plazas_balance.gestionar"
pantallas_principales: ["actividadplazas.pantalla.plazas_balance_dl"]
fragmentos: ["actividadplazas.pantalla.plazas_balance_dl"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadplazas/plazas_balance_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Plazas Balance

Carga el grid comparativo A vs B (mi dl frente a otra delegación) con plazas concedidas y libres por
actividad, insertado en la pantalla de balance.

## Objetivo De Usuario

Comparar, para un tipo de actividad, las plazas concedidas y libres de cada actividad en la propia delegación frente a otra delegación elegida.

## Punto De Entrada

Se muestra dentro de **Balance de plazas** al elegir una delegación. No tiene entrada propia en el menú.

## Fragmentos O Pantallas Auxiliares

- `actividadplazas.pantalla.plazas_balance_dl`
- `actividadplazas.pantalla.plazas_balance_que` (contenedor)

## Escenarios Inferidos

### Comparar con otra delegación

Pasos propuestos:
1. En **Balance de plazas**, elegir en el desplegable la delegación con la que comparar.
2. Debajo aparece la tabla: arriba, las plazas que se conceden mutuamente las dos delegaciones, y por actividad las concedidas y las libres.
3. Las celdas de la propia delegación se pueden cambiar con doble clic; se guarda al momento.

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
