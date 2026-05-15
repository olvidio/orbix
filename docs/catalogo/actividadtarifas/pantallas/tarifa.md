---
id: "actividadtarifas.pantalla.tarifa"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "actividadtarifas"
nombre: "Tarifa"
controller: "frontend/actividadtarifas/controller/tarifa.php"
vistas: ["frontend/actividadtarifas/view/tarifa.phtml"]
fragmentos_frontend: ["frontend/actividadtarifas/controller/tarifa_form.php", "frontend/actividadtarifas/controller/tarifa_lista.php"]
endpoints: ["/src/actividadtarifas/tipo_tarifa_eliminar", "/src/actividadtarifas/tipo_tarifa_lista_data", "/src/actividadtarifas/tipo_tarifa_update"]
capacidades: ["actividadtarifas.tipo_tarifa.gestionar"]
campos: ["form.id_tarifa", "form.letra", "form.modo", "form.observ"]
acciones: ["fnjs_cerrar", "fnjs_guardar", "fnjs_modificar", "fnjs_update_div", "fnjs_ver"]
estado_revision: "generado"
---

# Tarifa

Pantalla principal del modulo `actividadtarifas` - catalogo de `TipoTarifa`.

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/actividadtarifas/controller/tarifa.php`

## Vistas Relacionadas

- `frontend/actividadtarifas/view/tarifa.phtml`

## Fragmentos Frontend Relacionados

- `frontend/actividadtarifas/controller/tarifa_form.php`
- `frontend/actividadtarifas/controller/tarifa_lista.php`

## Endpoints Usados

- `/src/actividadtarifas/tipo_tarifa_eliminar`
- `/src/actividadtarifas/tipo_tarifa_lista_data`
- `/src/actividadtarifas/tipo_tarifa_update`

## Capacidades Relacionadas

- `actividadtarifas.tipo_tarifa.gestionar`

## Campos Detectados

- `form.id_tarifa`
- `form.letra`
- `form.modo`
- `form.observ`

## Acciones Detectadas

- `fnjs_cerrar`
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
