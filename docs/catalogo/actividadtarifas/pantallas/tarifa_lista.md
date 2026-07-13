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
estado_revision: "revisado"
---

# Tarifa Lista

Fragmento AJAX: renderiza la tabla HTML del catálogo `TipoTarifa` a partir de `tipo_tarifa_lista_data`.

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

Cargado automáticamente al abrir `tarifa.phtml`. Muestra columnas id, sección, letra, modo y
observaciones; la columna acción invoca `fnjs_modificar(id_tarifa)` si el backend lo permite.

## Ruta de menú

Sin entrada propia; accesible como fragmento de `tarifa.php` (definir tarifa).
