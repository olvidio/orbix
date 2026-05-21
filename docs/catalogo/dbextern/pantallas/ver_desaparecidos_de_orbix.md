---
id: "dbextern.pantalla.ver_desaparecidos_de_orbix"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "dbextern"
nombre: "Ver Desaparecidos De Orbix"
controller: "frontend/dbextern/controller/ver_desaparecidos_de_orbix.php"
vistas: ["frontend/dbextern/view/ver_desaparecidos_de_orbix.phtml"]
fragmentos_frontend: []
endpoints: ["/src/dbextern/sincro_desunir", "/src/dbextern/ver_desaparecidos_de_orbix_datos"]
capacidades: ["dbextern.sincro_desunir.gestionar", "dbextern.ver_desaparecidos_de_orbix.gestionar"]
campos: ["form.id_nom_listas", "form.tipo_persona", "post.ids_desaparecidos_de_orbix", "post.tipo_persona"]
acciones: ["fnjs_desunir"]
estado_revision: "generado"
---

# Ver Desaparecidos De Orbix

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/dbextern/controller/ver_desaparecidos_de_orbix.php`

## Vistas Relacionadas

- `frontend/dbextern/view/ver_desaparecidos_de_orbix.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/dbextern/sincro_desunir`
- `/src/dbextern/ver_desaparecidos_de_orbix_datos`

## Capacidades Relacionadas

- `dbextern.sincro_desunir.gestionar`
- `dbextern.ver_desaparecidos_de_orbix.gestionar`

## Campos Detectados

- `form.id_nom_listas`
- `form.tipo_persona`
- `post.ids_desaparecidos_de_orbix`
- `post.tipo_persona`

## Acciones Detectadas

- `fnjs_desunir`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
