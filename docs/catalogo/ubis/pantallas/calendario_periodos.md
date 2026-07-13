---
id: "ubis.pantalla.calendario_periodos"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "ubis"
nombre: "Calendario Periodos"
controller: "frontend/ubis/controller/calendario_periodos.php"
vistas: ["frontend/ubis/view/calendario_periodos.phtml"]
fragmentos_frontend: ["frontend/ubis/controller/calendario_periodos_form_periodo.php", "frontend/ubis/controller/calendario_periodos_get2.php", "frontend/ubis/controller/calendario_periodos_nuevo.php"]
endpoints: ["/src/ubis/calendario_periodos_eliminar", "/src/ubis/calendario_periodos_guardar"]
capacidades: ["ubis.calendario_periodos.gestionar"]
campos: ["form.id_item", "form.id_ubi", "form.year", "html.buscar"]
acciones: ["fnjs_cerrar", "fnjs_guardar", "fnjs_modificar", "fnjs_update_div", "fnjs_ver"]
estado_revision: "revisado"
---

# Calendario Periodos

Pantalla principal de gestión de periodos de calendario de casas CDC por año.

## Tipo

- Subtipo: `pantalla_principal`


- Controller: `frontend/ubis/controller/calendario_periodos.php`

## Vistas Relacionadas

- `frontend/ubis/view/calendario_periodos.phtml`

## Fragmentos Frontend Relacionados

- `frontend/ubis/controller/calendario_periodos_form_periodo.php`
- `frontend/ubis/controller/calendario_periodos_get2.php`
- `frontend/ubis/controller/calendario_periodos_nuevo.php`

## Endpoints Usados

- `/src/ubis/calendario_periodos_eliminar`
- `/src/ubis/calendario_periodos_guardar`

## Capacidades Relacionadas

- `ubis.calendario_periodos.gestionar`

## Campos Detectados

- `form.id_item`
- `form.id_ubi`
- `form.year`
- `html.buscar`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`
- `fnjs_modificar`
- `fnjs_update_div`
- `fnjs_ver`

## Ruta de menú

- **Legacy:** adl > Nuevo Calendario > Definir periodos
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Definir periodos
