---
id: "dbextern.pantalla.ver_desaparecidos_de_orbix"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "dbextern"
nombre: "BDU desaparecidas en Aquinate"
controller: "frontend/dbextern/controller/ver_desaparecidos_de_orbix.php"
vistas: ["frontend/dbextern/view/ver_desaparecidos_de_orbix.phtml"]
fragmentos_frontend: []
endpoints: ["/src/dbextern/sincro_desunir", "/src/dbextern/ver_desaparecidos_de_orbix_datos"]
capacidades: ["dbextern.sincro_desunir.gestionar", "dbextern.ver_desaparecidos_de_orbix.gestionar"]
campos: ["form.id_nom_listas", "form.tipo_persona", "post.ids_desaparecidos_de_orbix", "post.tipo_persona"]
acciones: ["fnjs_desunir"]
estado_revision: "revisado"
---

# BDU desaparecidas en Aquinate

Subpantalla del punto 3: personas en BDU con vínculo pero sin ficha activa en esta DL.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/dbextern/controller/ver_desaparecidos_de_orbix.php`

## Vistas Relacionadas

- `frontend/dbextern/view/ver_desaparecidos_de_orbix.phtml`

## Endpoints Usados

- `/src/dbextern/ver_desaparecidos_de_orbix_datos`
- `/src/dbextern/sincro_desunir`

## Manual De Usuario

1. Desde `sincro_index`, pulsar **ver** en punto 3.
2. Pulsar **desunir** para eliminar el `id_match` y permitir re-vincular o crear después.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `sincro_index` punto 3)
