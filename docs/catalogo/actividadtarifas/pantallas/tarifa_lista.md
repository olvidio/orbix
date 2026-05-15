---
id: "actividadtarifas.pantalla.tarifa_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadtarifas"
nombre: "Tarifa Lista"
controller: "frontend/actividadtarifas/controller/tarifa_lista.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/actividadtarifas/tipo_tarifa_lista_data"]
capacidades: ["actividadtarifas.tipo_tarifa.gestionar"]
campos: []
acciones: ["fnjs_modificar"]
estado_revision: "generado"
---

# Tarifa Lista

Controlador AJAX HTML: listado del catalogo `TipoTarifa`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadtarifas/controller/tarifa_lista.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadtarifas/tipo_tarifa_lista_data`

## Capacidades Relacionadas

- `actividadtarifas.tipo_tarifa.gestionar`

## Campos Detectados

No se han detectado campos de formulario.

## Acciones Detectadas

- `fnjs_modificar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
