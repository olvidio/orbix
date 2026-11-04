---
id: "actividadplazas.resumen_plazas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadplazas"
nombre: "Flujo - Ver el resumen de plazas de una actividad"
capacidad: "actividadplazas.resumen_plazas.gestionar"
pantallas_principales: ["actividadplazas.pantalla.resumen_plazas"]
fragmentos: ["actividadplazas.pantalla.resumen_plazas"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadplazas/resumen_plazas_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Resumen Plazas

Consulta el desglose de plazas de una actividad por delegación (calendario, cedidas, conseguidas,
disponibles, ocupadas y libres) y prepara el formulario de cesión.

## Objetivo De Usuario

Ver cuántas plazas tiene cada delegación en una actividad y ceder plazas a otra.

## Punto De Entrada

Lista de resultados de **Buscar actividad**: marcar la actividad y pulsar el botón **plazas**.

## Fragmentos O Pantallas Auxiliares

- `actividadplazas.pantalla.resumen_plazas`

## Escenarios Inferidos

### Ver el resumen de plazas de una actividad

Pasos propuestos:
1. Marcar la actividad y pulsar **plazas**.
2. Si solo se ven las plazas de la propia dl, avisa «Sólo se ven las ocupadas por la propia dl».
3. La tabla tiene una fila por delegación con **calendario**, **cedidas**, **conseguidas**, **total**, **ocupadas** y **libres**; debajo de cada una se indica a quién cedió o de quién consiguió plazas (por ejemplo «1 a dlb», «3 de dlmE») y una fila de **totales**.
4. Debajo hay un bloque «ceder [número] plazas a [delegación] **Guardar**» (ver «Ceder plazas»).

Endpoints asociados:
- `/src/actividadplazas/resumen_plazas_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_activ`
- `form.num_plazas`
- `form.region_dl`
- `html.btn_ok`
- `html.num_plazas`
- `html.refresh`
- `post.id_activ`
- `post.nom_activ`
- `post.sel`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_enviar_formulario`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/actividadplazas/resumen_plazas_data`

## Errores Conocidos

No se han documentado errores en la capacidad (el builder devuelve el payload o falla en el controller).

## Ruta de menú

- Sin entrada de menú en el índice: se abre desde una actividad concreta (menú «Plazas» de la
  actividad), no directamente desde un menú.
