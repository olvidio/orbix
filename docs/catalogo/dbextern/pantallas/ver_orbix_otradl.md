---
id: "dbextern.pantalla.ver_orbix_otradl"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "dbextern"
nombre: "Trasladar a otra DL"
controller: "frontend/dbextern/controller/ver_orbix_otradl.php"
vistas: ["frontend/dbextern/view/ver_orbix_otradl.phtml"]
fragmentos_frontend: []
endpoints: ["/src/dbextern/sincro_trasladar_a", "/src/dbextern/ver_orbix_otradl_datos"]
capacidades: ["dbextern.sincro_trasladar_a.gestionar", "dbextern.ver_orbix_otradl.gestionar"]
campos: ["form.dl", "form.id_nom_orbix", "form.tipo_persona", "post.ids_traslados_A", "post.tipo_persona"]
acciones: ["fnjs_trasladar"]
estado_revision: "revisado"
---

# Trasladar a otra DL

Subpantalla del punto 7: personas Aquinate activas cuya BDU pertenece a otra DL.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/dbextern/controller/ver_orbix_otradl.php`

## Vistas Relacionadas

- `frontend/dbextern/view/ver_orbix_otradl.phtml`

## Endpoints Usados

- `/src/dbextern/ver_orbix_otradl_datos`
- `/src/dbextern/sincro_trasladar_a` (`fnjs_trasladar`)

## Manual De Usuario

1. Desde `sincro_index`, pulsar **ver** en punto 7.
2. Pulsar **trasladar** por persona hacia la DL indicada.
3. Si la región destino es distinta, el backend rechaza y pide usar el dossier de traslados.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `sincro_index` punto 7)
