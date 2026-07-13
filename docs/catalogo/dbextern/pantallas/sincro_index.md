---
id: "dbextern.pantalla.sincro_index"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "dbextern"
nombre: "Sincronizar con los datos de Listas"
controller: "frontend/dbextern/controller/sincro_index.php"
vistas: ["frontend/dbextern/view/sincro_index.phtml"]
fragmentos_frontend: []
endpoints: ["/src/dbextern/refrescar_bdu", "/src/dbextern/sincro_index_datos", "/src/dbextern/sincro_syncro"]
capacidades: ["dbextern.refrescar_bdu.gestionar", "dbextern.sincro_index.gestionar", "dbextern.sincro_syncro.gestionar"]
campos: ["form.dl_listas", "form.que", "form.region", "form.tipo_persona", "post.tipo"]
acciones: ["fnjs_refrescar", "fnjs_sincronizar", "fnjs_update_div"]
estado_revision: "revisado"
---

# Sincronizar con los datos de Listas

Dashboard principal de sincronización BDU↔Aquinate: muestra fecha de actualización de `tmp_bdu`,
contadores de las 9 situaciones y enlaces «ver» / «ejecutar» hacia subpantallas y mutaciones.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/dbextern/controller/sincro_index.php`

## Vistas Relacionadas

- `frontend/dbextern/view/sincro_index.phtml`

## Endpoints Usados

- `/src/dbextern/sincro_index_datos` (bootstrap vía `PostRequest::getDataFromUrl` con `tipo`)
- `/src/dbextern/refrescar_bdu` (`fnjs_refrescar`, recarga pantalla tras éxito)
- `/src/dbextern/sincro_syncro` (`fnjs_sincronizar`, punto 1)

Los enlaces «ver» usan `fnjs_update_div` hacia controladores frontend firmados (`ver_traslados`,
`ver_listas`, etc.).

## Capacidades Relacionadas

- `dbextern.sincro_index.gestionar`
- `dbextern.sincro_syncro.gestionar`
- `dbextern.refrescar_bdu.gestionar`

## Manual De Usuario

1. Abrir desde menú «Actualizar datos desde BDU» (según colectivo: numerarios, agregados, s, sssc).
2. Si la BDU cambió después de `fecha_actualizacion`, pulsar **refrescar** (tarda varios minutos).
3. Revisar contadores: en situación normal solo el punto 1 tiene valor y el resto es 0.
4. Pulsar **ejecutar** en punto 1 para sincronizar fichas ya unidas.
5. Usar **ver** en puntos 2–4 y 7–9 para resolver casos especiales (traslados, uniones, altas, bajas).
6. Tras acciones en subpantallas, volver y **actualizar** (recarga `sincro_index`) hasta dejar todo en punto 1.

## Ruta de menú

La misma pantalla se abre con `tipo` distinto según colectivo:

- **Legacy:** vsm > buscar n > Actualizar datos desde BDU (`tipo=n`); dagd > buscar agd > … (`tipo=a`);
  vsg > buscar > … (`tipo=s`); dre > personas > … (`tipo=sssc`).
- **Pills2:** PERSONAS > Numerarios / Agregados / Supernumerarios > Actualizar datos desde BDU
  (`tipo=n`/`a`/`s`); sin entrada Pills2 explícita para `sssc`.
