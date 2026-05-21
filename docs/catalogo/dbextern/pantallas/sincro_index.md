---
id: "dbextern.pantalla.sincro_index"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "dbextern"
nombre: "Sincro Index"
controller: "frontend/dbextern/controller/sincro_index.php"
vistas: ["frontend/dbextern/view/sincro_index.phtml"]
fragmentos_frontend: []
endpoints: ["/src/dbextern/refrescar_bdu", "/src/dbextern/sincro_index_datos", "/src/dbextern/sincro_syncro"]
capacidades: ["dbextern.refrescar_bdu.gestionar", "dbextern.sincro_index.gestionar", "dbextern.sincro_syncro.gestionar"]
campos: ["form.dl_listas", "form.que", "form.region", "form.tipo_persona", "post.tipo"]
acciones: ["fnjs_refrescar", "fnjs_sincronizar", "fnjs_update_div"]
estado_revision: "generado"
---

# Sincro Index

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/dbextern/controller/sincro_index.php`

## Vistas Relacionadas

- `frontend/dbextern/view/sincro_index.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/dbextern/refrescar_bdu`
- `/src/dbextern/sincro_index_datos`
- `/src/dbextern/sincro_syncro`

## Capacidades Relacionadas

- `dbextern.refrescar_bdu.gestionar`
- `dbextern.sincro_index.gestionar`
- `dbextern.sincro_syncro.gestionar`

## Campos Detectados

- `form.dl_listas`
- `form.que`
- `form.region`
- `form.tipo_persona`
- `post.tipo`

## Acciones Detectadas

- `fnjs_refrescar`
- `fnjs_sincronizar`
- `fnjs_update_div`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
