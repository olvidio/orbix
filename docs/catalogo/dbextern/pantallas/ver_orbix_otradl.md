---
id: "dbextern.pantalla.ver_orbix_otradl"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "dbextern"
nombre: "Ver Orbix Otradl"
controller: "frontend/dbextern/controller/ver_orbix_otradl.php"
vistas: ["frontend/dbextern/view/ver_orbix_otradl.phtml"]
fragmentos_frontend: []
endpoints: ["/src/dbextern/sincro_trasladar_a", "/src/dbextern/ver_orbix_otradl_datos"]
capacidades: ["dbextern.sincro_trasladar_a.gestionar", "dbextern.ver_orbix_otradl.gestionar"]
campos: ["form.dl", "form.id_nom_orbix", "form.tipo_persona", "post.ids_traslados_A", "post.tipo_persona"]
acciones: ["fnjs_trasladar"]
estado_revision: "generado"
---

# Ver Orbix Otradl

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/dbextern/controller/ver_orbix_otradl.php`

## Vistas Relacionadas

- `frontend/dbextern/view/ver_orbix_otradl.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/dbextern/sincro_trasladar_a`
- `/src/dbextern/ver_orbix_otradl_datos`

## Capacidades Relacionadas

- `dbextern.sincro_trasladar_a.gestionar`
- `dbextern.ver_orbix_otradl.gestionar`

## Campos Detectados

- `form.dl`
- `form.id_nom_orbix`
- `form.tipo_persona`
- `post.ids_traslados_A`
- `post.tipo_persona`

## Acciones Detectadas

- `fnjs_trasladar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
