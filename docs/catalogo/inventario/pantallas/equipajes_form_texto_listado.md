---
id: "inventario.pantalla.equipajes_form_texto_listado"
tipo: "pantalla_frontend"
subtipo: "fragmento"
modulo: "inventario"
nombre: "Editar texto listado"
controller: "frontend/inventario/controller/equipajes_form_texto_listado.php"
vistas: ["frontend/inventario/view/equipajes_form_texto_listado.phtml"]
fragmentos_frontend: []
endpoints: ["/src/inventario/texto_de_egm"]
capacidades: ["inventario.texto_de_egm.gestionar"]
campos: ["form.texto", "html.texto", "post.id_equipaje", "post.loc", "post.texto"]
acciones: ["fnjs_cerrar", "fnjs_guardar_listado"]
estado_revision: "revisado"
---

# Editar texto listado

Editor texto grupo EGM.


## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/equipajes_form_texto_listado.php`

## Vistas Relacionadas

- `frontend/inventario/view/equipajes_form_texto_listado.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/texto_de_egm`

## Capacidades Relacionadas

- `inventario.texto_de_egm.gestionar`

## Campos Detectados

- `form.texto`
- `html.texto`
- `post.id_equipaje`
- `post.loc`
- `post.texto`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar_listado`

## Manual De Usuario

Ver [`manual/inventario.md`](../../../manual/inventario.md). Editor texto grupo EGM.

## Ruta de menú

- **Legacy:** sin entrada de menú
- **Pills2:** —
