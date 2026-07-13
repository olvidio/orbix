---
id: "dbextern.pantalla.ver_traslados"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "dbextern"
nombre: "Traslados desde otra DL"
controller: "frontend/dbextern/controller/ver_traslados.php"
vistas: ["frontend/dbextern/view/ver_traslados.phtml"]
fragmentos_frontend: []
endpoints: ["/src/dbextern/sincro_trasladar", "/src/dbextern/ver_traslados_datos"]
capacidades: ["dbextern.sincro_trasladar.gestionar", "dbextern.ver_traslados.gestionar"]
campos: ["form.dl", "form.id_nom_orbix", "form.tipo_persona", "post.ids_traslados", "post.tipo_persona"]
acciones: ["fnjs_trasladar"]
estado_revision: "revisado"
---

# Traslados desde otra DL

Subpantalla del punto 2: personas unidas a BDU pero con ficha activa en otra DL Orbix.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/dbextern/controller/ver_traslados.php`

## Vistas Relacionadas

- `frontend/dbextern/view/ver_traslados.phtml`

## Endpoints Usados

- `/src/dbextern/ver_traslados_datos` (tabla de personas)
- `/src/dbextern/sincro_trasladar` (`fnjs_trasladar`, con aviso de fecha de traslado = hoy)

## Manual De Usuario

1. Desde `sincro_index`, pulsar **ver** en punto 2.
2. Por cada fila, pulsar **trasladar** (confirma alerta sobre fecha).
3. La fila se marca tachada tras éxito.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `sincro_index` punto 2)
