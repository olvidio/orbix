---
id: "inventario.pantalla.equipajes_posibles_maletas"
tipo: "pantalla_frontend"
subtipo: "fragmento"
modulo: "inventario"
nombre: "Elegir maleta/grupo"
controller: "frontend/inventario/controller/equipajes_posibles_maletas.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/inventario/lista_equipajes_posibles_maletas"]
capacidades: ["inventario.lista_equipajes_posibles_maletas.gestionar"]
campos: ["post.id_equipaje"]
acciones: ["fnjs_ver_docs"]
estado_revision: "revisado"
---

# Elegir maleta/grupo

Selector maleta; puede crear grupo vía `equipajes_update_grupo`.


## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/equipajes_posibles_maletas.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/lista_equipajes_posibles_maletas`

## Capacidades Relacionadas

- `inventario.lista_equipajes_posibles_maletas.gestionar`

## Campos Detectados

- `post.id_equipaje`

## Acciones Detectadas

- `fnjs_ver_docs`

## Manual De Usuario

Ver [`manual/inventario.md`](../../../manual/inventario.md). Selector maleta; puede crear grupo vía `equipajes_update_grupo`.

## Ruta de menú

- **Legacy:** sin entrada de menú
- **Pills2:** —
