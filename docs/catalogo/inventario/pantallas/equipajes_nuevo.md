---
id: "inventario.pantalla.equipajes_nuevo"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "inventario"
nombre: "Nuevo equipaje"
controller: "frontend/inventario/controller/equipajes_nuevo.php"
vistas: ["frontend/inventario/view/equipajes_nuevo.phtml"]
fragmentos_frontend: ["frontend/inventario/controller/equipajes_casas_posibles.php", "frontend/inventario/controller/equipajes_form_nuevo.php", "frontend/inventario/controller/equipajes_lista_activ_periodo.php", "frontend/inventario/controller/equipajes_ver.php"]
endpoints: []
capacidades: []
campos: ["post.empiezamax", "post.empiezamin", "post.periodo", "post.year"]
acciones: ["fnjs_cerrar", "fnjs_guardar", "fnjs_nombrar_equipaje", "fnjs_update_div", "fnjs_ver_actividades_casa", "fnjs_ver_casas"]
estado_revision: "revisado"
---

# Nuevo equipaje

Wizard alta equipaje: periodo, actividades, nombre.


## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/inventario/controller/equipajes_nuevo.php`

## Vistas Relacionadas

- `frontend/inventario/view/equipajes_nuevo.phtml`

## Fragmentos Frontend Relacionados

- `frontend/inventario/controller/equipajes_casas_posibles.php`
- `frontend/inventario/controller/equipajes_form_nuevo.php`
- `frontend/inventario/controller/equipajes_lista_activ_periodo.php`
- `frontend/inventario/controller/equipajes_ver.php`

## Endpoints Usados

No se han detectado endpoints `/src/...`.

## Capacidades Relacionadas

No se han detectado capacidades relacionadas.

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.periodo`
- `post.year`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`
- `fnjs_nombrar_equipaje`
- `fnjs_update_div`
- `fnjs_ver_actividades_casa`
- `fnjs_ver_casas`

## Manual De Usuario

Ver [`manual/inventario.md`](../../../manual/inventario.md). Wizard alta equipaje: periodo, actividades, nombre.

## Ruta de menú

- **Legacy:** scdl > Inventario > equipajes > nuevo equipaje
- **Pills2:** —
