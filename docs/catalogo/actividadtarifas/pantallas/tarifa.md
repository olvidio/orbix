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
estado_revision: "revisado"
---

# Tarifa

Catálogo maestro de tipos de tarifa (`TipoTarifa`): listado AJAX, alta/edición/eliminación en popup.

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

Al abrir la pantalla se carga el listado (`fnjs_ver` → `tipo_tarifa_lista_data`). Desde el listado
se puede modificar una fila (`fnjs_modificar`) o crear una nueva; el formulario se inyecta en
`#div_modificar`. Guardar/eliminar llaman a `tipo_tarifa_update` / `tipo_tarifa_eliminar` con JSON.
El enlace modificar solo aparece con permiso oficina `adl` y sección coincidente.

## Ruta de menú

- **Legacy:** adl > Tarifas > definir tarifa; dre > Nuevo calendario > Tarifas > definir tarifa;
  exterior > Tarifas > definir tarifa (también como activ -> tarifa en algunos menús).
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Tarifas > Definir tarifa.
