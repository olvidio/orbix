---
id: "profesores.pantalla.ficha_profesor_stgr"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "profesores"
nombre: "Ficha profesor STGR"
controller: "frontend/profesores/controller/ficha_profesor_stgr.php"
vistas: ["frontend/profesores/view/ficha_profesor_stgr.phtml", "frontend/profesores/view/ficha_profesor_stgr.print.phtml"]
fragmentos_frontend: []
endpoints: ["/src/profesores/ficha_profesor_stgr"]
capacidades: ["profesores.ficha_profesor_stgr.gestionar"]
campos: ["post.depende", "post.id_nom", "post.id_pau", "post.id_tabla", "post.obj_pau", "post.permiso", "post.print", "post.sel", "post.stack"]
acciones: ["fnjs_update_div"]
estado_revision: "revisado"
---

# Ficha profesor STGR

Dossier académico del profesor: cabecera (nombre, centro, departamento, flags n/agd/sacd), bloques
de curriculum, nombramientos, ampliaciones, congresos, docencia, director, juramento y
publicaciones. Enlaces **[modificar]** según `aPerm` hacia submantenimientos `tablaDB_lista_ver`.
Vista imprimible con `[imprimir]`.

## Tipo

- Subtipo: `fragmento_ajax` (desde búsqueda de personas)
- Controller: `frontend/profesores/controller/ficha_profesor_stgr.php`

## Vistas Relacionadas

- `frontend/profesores/view/ficha_profesor_stgr.phtml`
- `frontend/profesores/view/ficha_profesor_stgr.print.phtml`

## Endpoints Usados

- `/src/profesores/ficha_profesor_stgr`

## Campos Detectados

- `post.sel` — token `id_nom#id_tabla` desde listado personas
- `post.id_nom` / `post.id_pau` — identificador alternativo
- `post.id_tabla`, `post.obj_pau`, `post.permiso`, `post.depende` — contexto dossier
- `post.print` — vista impresión

## Acciones Detectadas

- `fnjs_update_div` — navegar a impresión o submantenimientos

## Manual De Usuario

1. Buscar persona en el menú de personas.
2. En el resultado, pulsar **ficha profesor stgr** (`fnjs_ficha_profe`).
3. Consultar bloques; con permiso de escritura, **[modificar]** abre el mantenimiento del bloque.
4. Opcional: **[imprimir]** para versión reducida.

## Ruta de menú

sin entrada de menú en el índice (botón en `personas_select` tras búsqueda de personas)
