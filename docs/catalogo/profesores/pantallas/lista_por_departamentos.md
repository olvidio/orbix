---
id: "profesores.pantalla.lista_por_departamentos"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "profesores"
nombre: "Claustro por departamentos"
controller: "frontend/profesores/controller/lista_por_departamentos.php"
vistas: ["frontend/profesores/view/lista_por_departamentos.phtml"]
fragmentos_frontend: []
endpoints: ["/src/profesores/lista_por_departamentos"]
capacidades: ["profesores.lista_por_departamentos.gestionar"]
campos: ["form.dl", "post.dl", "post.filtro"]
acciones: ["fnjs_left_side_hide"]
estado_revision: "revisado"
---

# Claustro por departamentos

Listado del claustro STGR agrupado por departamento: subsección **director** y cada **tipo de
profesor**, con nombre y centro (y delegación en RSTGR). En ámbito regional muestra primero un
filtro de delegaciones.

## Tipo

- Subtipo: `pantalla_principal`
- Controller: `frontend/profesores/controller/lista_por_departamentos.php`

## Vistas Relacionadas

- `frontend/profesores/view/lista_por_departamentos.phtml`
- Filtro RSTGR: `frontend/ubis/controller/dl_rstgr_que.html.twig` (vía `modo=filtro`)

## Endpoints Usados

- `/src/profesores/lista_por_departamentos`

## Campos Detectados

- `post.dl` — delegaciones seleccionadas (RSTGR)
- `post.filtro` — `1` tras aplicar filtro

## Acciones Detectadas

- `fnjs_left_side_hide` — ocultar panel lateral al cargar

## Manual De Usuario

1. Abrir **claustro** desde el menú.
2. En RSTGR: marcar delegaciones y pulsar **Aplicar filtro**.
3. Consultar el listado por departamento (solo profesores activos sin cese).

Pantalla de solo consulta.

## Ruta de menú

- **Legacy:** vest > buscar persona > claustro; stgr > personas > claustro
- **Pills2:** ESTUDIOS > Datos e informes > Claustro
