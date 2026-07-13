---
id: "actividadtarifas.pantalla.tarifa_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadtarifas"
nombre: "Tarifa Form"
controller: "frontend/actividadtarifas/controller/tarifa_form.php"
vistas: ["frontend/actividadtarifas/view/tarifa_form.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadtarifas/tipo_tarifa_form_data", "/src/actividadtarifas/tipo_tarifa_update"]
capacidades: ["actividadtarifas.tipo_tarifa.gestionar"]
campos: ["html.id_tarifa", "html.letra", "html.modo", "html.observ", "post.id_tarifa"]
acciones: ["fnjs_cerrar", "fnjs_guardar"]
estado_revision: "revisado"
---

# Tarifa Form

Fragmento AJAX: formulario popup modificar/nuevo de `TipoTarifa` (`tarifa_form.phtml`).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadtarifas/controller/tarifa_form.php`

## Vistas Relacionadas

- `frontend/actividadtarifas/view/tarifa_form.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadtarifas/tipo_tarifa_form_data`
- `/src/actividadtarifas/tipo_tarifa_update`

## Capacidades Relacionadas

- `actividadtarifas.tipo_tarifa.gestionar`

## Campos Detectados

- `html.id_tarifa`
- `html.letra`
- `html.modo`
- `html.observ`
- `post.id_tarifa`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`

## Manual De Usuario

Campos: letra, modo (desplegable), observaciones. Guardar/eliminar delegan en el JS de `tarifa.phtml`.
En edición muestra botón eliminar con confirmación.

## Ruta de menú

Sin entrada propia; popup de `tarifa.php`.
