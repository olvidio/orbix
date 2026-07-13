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
estado_revision: "revisado"
---

# Tarifa Ubi

Tarifas económicas por casa y año (`TarifaUbi`): filtros casa/año, listado AJAX, popup con HashB.

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

1. Elegir casa y año y pulsar **buscar** (`fnjs_ver`).
2. En el listado, modificar una tarifa o añadir nueva; copiar desde año anterior usa `ctx_copiar`
   (cápsula HashB del listado).
3. El formulario lleva `ctx_update` / `ctx_eliminar`; guardar/eliminar POST a endpoints firmados.

Mutaciones `update`/`eliminar`/`copiar` requieren cápsula `HashB` válida.

## Ruta de menú

- **Legacy:** adl > Tarifas > tarifas por casa y año; Calendario/dre/exterior > Nuevo calendario >
  Tarifas > tarifas por casa y año.
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Tarifas > Tarifas por casa y año.
