---
id: "actividadtarifas.pantalla.tarifa_ubi"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "actividadtarifas"
nombre: "Tarifa Ubi"
controller: "frontend/actividadtarifas/controller/tarifa_ubi.php"
vistas: ["frontend/actividadtarifas/view/tarifa_ubi.phtml"]
fragmentos_frontend: ["frontend/actividadtarifas/controller/tarifa_ubi_form.php", "frontend/actividadtarifas/controller/tarifa_ubi_lista.php"]
endpoints: ["/src/actividadtarifas/tarifa_ubi_copiar", "/src/actividadtarifas/tarifa_ubi_eliminar", "/src/actividadtarifas/tarifa_ubi_update"]
capacidades: ["actividadtarifas.tarifa_ubi.gestionar"]
campos: ["form.cantidad", "form.id_item", "form.id_serie", "form.id_tarifa", "form.id_ubi", "form.letra", "form.year", "html.buscar"]
acciones: ["fnjs_cerrar", "fnjs_copiar_tarifas", "fnjs_guardar", "fnjs_modificar", "fnjs_update_div", "fnjs_ver"]
estado_revision: "generado"
---

# Tarifa Ubi

Pantalla principal del modulo `actividadtarifas` - tarifas por casa y año (`TarifaUbi`).

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/actividadtarifas/controller/tarifa_ubi.php`

## Vistas Relacionadas

- `frontend/actividadtarifas/view/tarifa_ubi.phtml`

## Fragmentos Frontend Relacionados

- `frontend/actividadtarifas/controller/tarifa_ubi_form.php`
- `frontend/actividadtarifas/controller/tarifa_ubi_lista.php`

## Endpoints Usados

- `/src/actividadtarifas/tarifa_ubi_copiar`
- `/src/actividadtarifas/tarifa_ubi_eliminar`
- `/src/actividadtarifas/tarifa_ubi_update`

## Capacidades Relacionadas

- `actividadtarifas.tarifa_ubi.gestionar`

## Campos Detectados

- `form.cantidad`
- `form.id_item`
- `form.id_serie`
- `form.id_tarifa`
- `form.id_ubi`
- `form.letra`
- `form.year`
- `html.buscar`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_copiar_tarifas`
- `fnjs_guardar`
- `fnjs_modificar`
- `fnjs_update_div`
- `fnjs_ver`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
