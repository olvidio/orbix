---
id: "inventario.pantalla.equipajes_form_nuevo"
tipo: "pantalla_frontend"
subtipo: "fragmento"
modulo: "inventario"
nombre: "Fragmento selección actividades"
controller: "frontend/inventario/controller/equipajes_form_nuevo.php"
vistas: ["frontend/inventario/view/equipajes_form_nuevo.phtml"]
fragmentos_frontend: []
endpoints: ["/src/inventario/equipajes_lista_activ_sel"]
capacidades: ["inventario.equipajes_lista_activ_sel.gestionar"]
campos: ["form.nom_equipaje", "html.nom_equipaje", "post.id_cdc", "post.nom_equip", "post.sel"]
acciones: ["fnjs_cerrar", "fnjs_guardar"]
estado_revision: "revisado"
---

# Fragmento selección actividades

AJAX selección actividades al crear equipaje.


## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/equipajes_form_nuevo.php`

## Vistas Relacionadas

- `frontend/inventario/view/equipajes_form_nuevo.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/equipajes_lista_activ_sel`

## Capacidades Relacionadas

- `inventario.equipajes_lista_activ_sel.gestionar`

## Campos Detectados

- `form.nom_equipaje`
- `html.nom_equipaje`
- `post.id_cdc`
- `post.nom_equip`
- `post.sel`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`

## Manual De Usuario

Ver [`manual/inventario.md`](../../../manual/inventario.md). AJAX selección actividades al crear equipaje.

## Ruta de menú

- **Legacy:** sin entrada de menú
- **Pills2:** —
