---
id: "dbextern.pantalla.ver_desaparecidos_de_listas"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "dbextern"
nombre: "Aquinate con BDU desaparecida"
controller: "frontend/dbextern/controller/ver_desaparecidos_de_listas.php"
vistas: ["frontend/dbextern/view/ver_desaparecidos_de_listas.phtml"]
fragmentos_frontend: []
endpoints: ["/src/dbextern/sincro_baja", "/src/dbextern/ver_desaparecidos_de_listas_datos"]
capacidades: ["dbextern.sincro_baja.gestionar", "dbextern.ver_desaparecidos_de_listas.gestionar"]
campos: ["form.id_nom_orbix", "form.tipo_persona", "post.ids_desaparecidos_de_listas", "post.tipo_persona"]
acciones: ["fnjs_baja"]
estado_revision: "revisado"
---

# Aquinate con BDU desaparecida

Subpantalla del punto 8: personas Aquinate con `id_match` pero ficha BDU vacía o inexistente.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/dbextern/controller/ver_desaparecidos_de_listas.php`

## Vistas Relacionadas

- `frontend/dbextern/view/ver_desaparecidos_de_listas.phtml`

## Endpoints Usados

- `/src/dbextern/ver_desaparecidos_de_listas_datos`
- `/src/dbextern/sincro_baja` (situación `B`)

## Manual De Usuario

1. Desde `sincro_index`, pulsar **ver** en punto 8.
2. Pulsar **baja** para dar de baja la ficha Aquinate (no elimina el vínculo automáticamente).

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `sincro_index` punto 8)
