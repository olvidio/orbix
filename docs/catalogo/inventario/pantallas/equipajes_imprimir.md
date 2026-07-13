---
id: "inventario.pantalla.equipajes_imprimir"
tipo: "pantalla_frontend"
subtipo: "fragmento"
modulo: "inventario"
nombre: "Imprimir equipaje"
controller: "frontend/inventario/controller/equipajes_imprimir.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/inventario/cabecera_pie_txt", "/src/inventario/equipajes_doc_casa", "/src/inventario/equipajes_egm", "/src/inventario/equipajes_lista_activ_equipaje"]
capacidades: ["inventario.cabecera_pie_txt.gestionar", "inventario.equipajes_doc_casa.gestionar", "inventario.equipajes_egm.gestionar", "inventario.equipajes_lista_activ_equipaje.gestionar"]
campos: ["post.id_equipaje"]
acciones: ["fnjs_left_side_hide", "fnjs_mod_texto_equipaje"]
estado_revision: "revisado"
---

# Imprimir equipaje

Compone impresión: cabecera, actividades, docs por casa, EGM.


## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/equipajes_imprimir.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/cabecera_pie_txt`
- `/src/inventario/equipajes_doc_casa`
- `/src/inventario/equipajes_egm`
- `/src/inventario/equipajes_lista_activ_equipaje`

## Capacidades Relacionadas

- `inventario.cabecera_pie_txt.gestionar`
- `inventario.equipajes_doc_casa.gestionar`
- `inventario.equipajes_egm.gestionar`
- `inventario.equipajes_lista_activ_equipaje.gestionar`

## Campos Detectados

- `post.id_equipaje`

## Acciones Detectadas

- `fnjs_left_side_hide`
- `fnjs_mod_texto_equipaje`

## Manual De Usuario

Ver [`manual/inventario.md`](../../../manual/inventario.md). Compone impresión: cabecera, actividades, docs por casa, EGM.

## Ruta de menú

- **Legacy:** sin entrada directa (equipajes_ver?imprimir=1)
- **Pills2:** —
