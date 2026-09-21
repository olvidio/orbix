---
id: "actividadtarifas.pantalla.tarifa_ubi_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadtarifas"
nombre: "Tarifa Ubi Form"
controller: "frontend/actividadtarifas/controller/tarifa_ubi_form.php"
vistas: ["frontend/actividadtarifas/view/tarifa_ubi_form.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadtarifas/tarifa_ubi_form_data", "/src/actividadtarifas/tarifa_ubi_update"]
capacidades: ["actividadtarifas.tarifa_ubi.gestionar"]
campos: ["html.cantidad", "html.ctx_eliminar", "html.ctx_update", "post.ctx_form", "post.id_ubi", "post.year"]
acciones: ["fnjs_cerrar", "fnjs_comprobar_dinero", "fnjs_guardar"]
estado_revision: "revisado"
---

# Tarifa Ubi Form

Fragmento AJAX: formulario popup de `TarifaUbi` con cápsulas HashB `ctx_update` / `ctx_eliminar`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadtarifas/controller/tarifa_ubi_form.php`

## Vistas Relacionadas

- `frontend/actividadtarifas/view/tarifa_ubi_form.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadtarifas/tarifa_ubi_form_data`
- `/src/actividadtarifas/tarifa_ubi_update`

## Capacidades Relacionadas

- `actividadtarifas.tarifa_ubi.gestionar`

## Campos Detectados

- `html.cantidad`
- `html.ctx_eliminar`
- `html.ctx_update`
- `post.ctx_form`
- `post.id_ubi`
- `post.year`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_comprobar_dinero`
- `fnjs_guardar`

## Manual De Usuario

Alta: desplegables tarifa y serie + importe. Edición: solo importe (y eliminar). Los hidden `ctx_*`
autorizan las mutaciones y contienen la identidad firmada; el formulario no transporta IDs planos.

## Ruta de menú

Sin entrada propia; popup de `tarifa_ubi.php`.
