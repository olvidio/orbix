---
id: "dbextern.pantalla.ver_traslados"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "dbextern"
nombre: "Ver Traslados"
controller: "frontend/dbextern/controller/ver_traslados.php"
vistas: ["frontend/dbextern/view/ver_traslados.phtml"]
fragmentos_frontend: []
endpoints: ["/src/dbextern/sincro_trasladar", "/src/dbextern/ver_traslados_datos"]
capacidades: ["dbextern.sincro_trasladar.gestionar", "dbextern.ver_traslados.gestionar"]
campos: ["form.dl", "form.id_nom_orbix", "form.tipo_persona", "post.ids_traslados", "post.tipo_persona"]
acciones: ["fnjs_trasladar"]
estado_revision: "generado"
---

# Ver Traslados

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/dbextern/controller/ver_traslados.php`

## Vistas Relacionadas

- `frontend/dbextern/view/ver_traslados.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/dbextern/sincro_trasladar`
- `/src/dbextern/ver_traslados_datos`

## Capacidades Relacionadas

- `dbextern.sincro_trasladar.gestionar`
- `dbextern.ver_traslados.gestionar`

## Campos Detectados

- `form.dl`
- `form.id_nom_orbix`
- `form.tipo_persona`
- `post.ids_traslados`
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
