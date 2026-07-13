---
id: "actividadplazas.pantalla.resumen_plazas"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadplazas"
nombre: "Resumen Plazas"
controller: "frontend/actividadplazas/controller/resumen_plazas.php"
vistas: ["frontend/actividadplazas/view/resumen_plazas.phtml"]
fragmentos_frontend: ["frontend/actividadplazas/controller/resumen_plazas.php"]
endpoints: ["/src/actividadplazas/plazas_ceder", "/src/actividadplazas/resumen_plazas_data"]
capacidades: ["actividadplazas.plazas_ceder.gestionar", "actividadplazas.resumen_plazas.gestionar"]
campos: ["form.id_activ", "form.num_plazas", "form.region_dl", "html.btn_ok", "html.num_plazas", "html.refresh", "post.id_activ", "post.nom_activ", "post.sel"]
acciones: ["fnjs_actualizar", "fnjs_enviar_formulario", "fnjs_guardar"]
estado_revision: "revisado"
---

# Resumen Plazas

Pantalla resumen de plazas de una actividad: desglose por delegación (calendario, cedidas,
conseguidas, disponibles, ocupadas y libres) con totales, y formulario para ceder plazas a otra dl.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadplazas/controller/resumen_plazas.php`

## Vistas Relacionadas

- `frontend/actividadplazas/view/resumen_plazas.phtml`

## Fragmentos Frontend Relacionados

- `frontend/actividadplazas/controller/resumen_plazas.php`

## Endpoints Usados

- `/src/actividadplazas/plazas_ceder`
- `/src/actividadplazas/resumen_plazas_data`

## Capacidades Relacionadas

- `actividadplazas.plazas_ceder.gestionar`
- `actividadplazas.resumen_plazas.gestionar`

## Campos Detectados

- `form.id_activ`
- `form.num_plazas`
- `form.region_dl`
- `html.btn_ok`
- `html.num_plazas`
- `html.refresh`
- `post.id_activ`
- `post.nom_activ`
- `post.sel`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_enviar_formulario`
- `fnjs_guardar`

## Manual De Usuario

Presenta, por cada delegación implicada, las plazas de calendario, cedidas, conseguidas, disponibles,
ocupadas y libres, con una fila de totales. Avisa si la actividad no está publicada o si solo se ven
las ocupadas por la propia dl. En la parte inferior, el formulario **Ceder** permite indicar un número
de plazas y una delegación destino; al **Guardar** (`fnjs_guardar`) se envía a `plazas_ceder` y, si
tiene éxito, la pantalla se recarga (`fnjs_actualizar`).

## Ruta de menú

- Sin entrada de menú en el índice: se abre desde una actividad concreta (menú «Plazas» de la
  actividad), no directamente desde un menú.
