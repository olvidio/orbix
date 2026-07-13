---
id: "inventario.pantalla.equipajes_doc_casa"
tipo: "pantalla_frontend"
subtipo: "fragmento"
modulo: "inventario"
nombre: "Docs por casa (equipaje)"
controller: "frontend/inventario/controller/equipajes_doc_casa.php"
vistas: ["frontend/inventario/view/equipajes_doc_casa.phtml", "frontend/inventario/view/equipajes_doc_maleta.phtml"]
fragmentos_frontend: []
endpoints: ["/src/inventario/equipajes_doc_casa", "/src/inventario/equipajes_egm"]
capacidades: ["inventario.equipajes_doc_casa.gestionar", "inventario.equipajes_egm.gestionar"]
campos: ["post.id_equipaje"]
acciones: ["fnjs_eliminar_grupo", "fnjs_modificar_form_add", "fnjs_modificar_form_del", "fnjs_nuevo_grupo", "fnjs_ver_equipaje"]
estado_revision: "revisado"
---

# Docs por casa (equipaje)

Fragmento impresión documentos por casa.


## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/equipajes_doc_casa.php`

## Vistas Relacionadas

- `frontend/inventario/view/equipajes_doc_casa.phtml`
- `frontend/inventario/view/equipajes_doc_maleta.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/equipajes_doc_casa`
- `/src/inventario/equipajes_egm`

## Capacidades Relacionadas

- `inventario.equipajes_doc_casa.gestionar`
- `inventario.equipajes_egm.gestionar`

## Campos Detectados

- `post.id_equipaje`

## Acciones Detectadas

- `fnjs_eliminar_grupo`
- `fnjs_modificar_form_add`
- `fnjs_modificar_form_del`
- `fnjs_nuevo_grupo`
- `fnjs_ver_equipaje`

## Manual De Usuario

Ver [`manual/inventario.md`](../../../manual/inventario.md). Fragmento impresión documentos por casa.

## Ruta de menú

- **Legacy:** sin entrada de menú
- **Pills2:** —
