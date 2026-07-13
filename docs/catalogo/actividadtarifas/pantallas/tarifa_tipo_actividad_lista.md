---
id: "actividadtarifas.pantalla.tarifa_tipo_actividad_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadtarifas"
nombre: "Tarifa Tipo Actividad Lista"
controller: "frontend/actividadtarifas/controller/tarifa_tipo_actividad_lista.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/actividadtarifas/relacion_tarifa_lista_data"]
capacidades: ["actividadtarifas.relacion_tarifa.gestionar"]
campos: []
acciones: ["fnjs_modificar"]
estado_revision: "revisado"
---

# Tarifa Tipo Actividad Lista

Fragmento AJAX: tabla de relaciones tarifa ↔ tipo de actividad.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadtarifas/controller/tarifa_tipo_actividad_lista.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadtarifas/relacion_tarifa_lista_data`

## Capacidades Relacionadas

- `actividadtarifas.relacion_tarifa.gestionar`

## Campos Detectados

No se han detectado campos de formulario.

## Acciones Detectadas

- `fnjs_modificar`

## Manual De Usuario

Cargado al abrir la pantalla principal. Enlace modificar por fila si permiso `adl` y sección coincide.

## Ruta de menú

Sin entrada propia; fragmento de `tarifa_tipo_actividad.php`.
