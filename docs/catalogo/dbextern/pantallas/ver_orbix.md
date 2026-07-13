---
id: "dbextern.pantalla.ver_orbix"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "dbextern"
nombre: "Personas Aquinate sin BDU"
controller: "frontend/dbextern/controller/ver_orbix.php"
vistas: ["frontend/dbextern/view/ver_orbix.phtml"]
fragmentos_frontend: []
endpoints: ["/src/dbextern/sincro_unir", "/src/dbextern/ver_orbix_datos"]
capacidades: ["dbextern.sincro_unir.gestionar", "dbextern.ver_orbix.gestionar"]
campos: ["form.dl", "form.id", "form.id_nom_listas", "form.id_orbix", "form.region", "form.tipo_persona", "html.mov", "post.dl", "post.id", "post.mov", "post.region", "post.tipo_persona"]
acciones: ["fnjs_submit", "fnjs_unir_bdu"]
estado_revision: "revisado"
---

# Personas Aquinate sin BDU

Subpantalla del punto 9: personas activas en Aquinate sin correspondencia en BDU; permite unir con
candidato BDU si existe.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/dbextern/controller/ver_orbix.php`

## Vistas Relacionadas

- `frontend/dbextern/view/ver_orbix.phtml`

## Endpoints Usados

- `/src/dbextern/ver_orbix_datos` (lista en `$_SESSION['DBOrbix']` + matches por persona)
- `/src/dbextern/sincro_unir` (`fnjs_unir_bdu`)

## Manual De Usuario

1. Desde `sincro_index`, pulsar **ver** en punto 9.
2. Navegar la cola de personas sin unir (anterior/siguiente).
3. Si aparecen candidatos BDU, pulsar **unir** en la fila deseada.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `sincro_index` punto 9)
