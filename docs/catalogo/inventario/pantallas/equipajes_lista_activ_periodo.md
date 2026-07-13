---
id: "inventario.pantalla.equipajes_lista_activ_periodo"
tipo: "pantalla_frontend"
subtipo: "fragmento"
modulo: "inventario"
nombre: "Actividades por periodo"
controller: "frontend/inventario/controller/equipajes_lista_activ_periodo.php"
vistas: ["frontend/inventario/view/equipajes_lista_activ_periodo.phtml"]
fragmentos_frontend: []
endpoints: ["/src/inventario/equipajes_lista_activ_periodo"]
capacidades: ["inventario.equipajes_lista_activ_periodo.gestionar"]
campos: ["form.sel", "post.empiezamax", "post.empiezamin", "post.fin", "post.id_cdc", "post.inicio", "post.periodo", "post.year"]
acciones: ["fnjs_nombrar_equipaje"]
estado_revision: "revisado"
---

# Actividades por periodo

Tabla actividades filtradas por CDC y periodo.


## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/equipajes_lista_activ_periodo.php`

## Vistas Relacionadas

- `frontend/inventario/view/equipajes_lista_activ_periodo.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/equipajes_lista_activ_periodo`

## Capacidades Relacionadas

- `inventario.equipajes_lista_activ_periodo.gestionar`

## Campos Detectados

- `form.sel`
- `post.empiezamax`
- `post.empiezamin`
- `post.fin`
- `post.id_cdc`
- `post.inicio`
- `post.periodo`
- `post.year`

## Acciones Detectadas

- `fnjs_nombrar_equipaje`

## Manual De Usuario

Ver [`manual/inventario.md`](../../../manual/inventario.md). Tabla actividades filtradas por CDC y periodo.

## Ruta de menú

- **Legacy:** sin entrada de menú
- **Pills2:** —
