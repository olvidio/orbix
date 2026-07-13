---
id: "actividadplazas.resumen_plazas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadplazas"
nombre: "Flujo - Gestionar Resumen Plazas"
capacidad: "actividadplazas.resumen_plazas.gestionar"
pantallas_principales: []
fragmentos: ["actividadplazas.pantalla.resumen_plazas"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadplazas/resumen_plazas_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Resumen Plazas

Consulta el desglose de plazas de una actividad por delegación (calendario, cedidas, conseguidas,
disponibles, ocupadas y libres) y prepara el formulario de cesión.

## Objetivo De Usuario

Ver el estado completo de plazas de una actividad (por dl y totales), comprobar avisos de publicación
o visibilidad, y acceder al formulario para ceder plazas a otra delegación.

## Punto De Entrada

Menú «Plazas» de una actividad concreta: abre `resumen_plazas` con `id_activ` (y contexto `sel`).

## Fragmentos O Pantallas Auxiliares

- `actividadplazas.pantalla.resumen_plazas`

## Escenarios Inferidos

### Obtener Datos

1. Desde una actividad, abrir la opción de plazas/resumen.
2. El sistema carga `resumen_plazas_data` con el desglose por delegación y totales.
3. Muestra avisos si la actividad no está publicada o si solo se ven las ocupadas por la propia dl
   (`otra_dl`).
4. Pinta la tabla (calendario, cedidas, conseguidas, disponibles, ocupadas, libres) y el desplegable
   de delegaciones para el bloque **Ceder** (flujo `plazas_ceder`).

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
