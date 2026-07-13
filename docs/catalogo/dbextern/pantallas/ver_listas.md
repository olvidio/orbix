---
id: "dbextern.pantalla.ver_listas"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "dbextern"
nombre: "Personas BDU no unidas"
controller: "frontend/dbextern/controller/ver_listas.php"
vistas: ["frontend/dbextern/view/ver_listas.phtml"]
fragmentos_frontend: []
endpoints: ["/src/dbextern/sincro_crear", "/src/dbextern/sincro_crear_todos", "/src/dbextern/sincro_unir", "/src/dbextern/ver_listas_datos"]
capacidades: ["dbextern.sincro.gestionar", "dbextern.sincro_crear_todos.gestionar", "dbextern.sincro_unir.gestionar", "dbextern.ver_listas.gestionar"]
campos: ["form.dl", "form.id", "form.id_nom_listas", "form.id_orbix", "form.region", "form.tipo_persona", "html.mov", "post.dl", "post.id", "post.mov", "post.region", "post.tipo_persona"]
acciones: ["fnjs_crear", "fnjs_crear_todos", "fnjs_submit", "fnjs_unir"]
estado_revision: "revisado"
---

# Personas BDU no unidas

Subpantalla del punto 4: recorre personas de la BDU sin `id_match`, muestra candidatos Orbix para
unir o permite crear ficha nueva / crear todas.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/dbextern/controller/ver_listas.php`

## Vistas Relacionadas

- `frontend/dbextern/view/ver_listas.phtml`

## Endpoints Usados

- `/src/dbextern/ver_listas_datos` (carga lista en sesión + búsqueda de coincidencias)
- `/src/dbextern/sincro_unir` (`fnjs_unir`)
- `/src/dbextern/sincro_crear` (`fnjs_crear`)
- `/src/dbextern/sincro_crear_todos` (`fnjs_crear_todos`)

## Manual De Usuario

1. Desde `sincro_index`, pulsar **ver** en punto 4.
2. En primera carga se intentan uniones automáticas (`cont_sync`).
3. Navegar con anterior/siguiente por la cola en `$_SESSION['DBListas']`.
4. Si hay coincidencias en la misma u otra DL, pulsar **unir**; si no, **crear nuevo** o **crear todos**.
5. Tras cada acción exitosa avanza al siguiente registro.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `sincro_index` punto 4)
