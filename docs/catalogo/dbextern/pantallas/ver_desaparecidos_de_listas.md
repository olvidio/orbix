---
id: "dbextern.pantalla.ver_desaparecidos_de_listas"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "dbextern"
nombre: "Ver Desaparecidos De Listas"
controller: "frontend/dbextern/controller/ver_desaparecidos_de_listas.php"
vistas: ["frontend/dbextern/view/ver_desaparecidos_de_listas.phtml"]
fragmentos_frontend: []
endpoints: ["/src/dbextern/sincro_baja", "/src/dbextern/ver_desaparecidos_de_listas_datos"]
capacidades: ["dbextern.sincro_baja.gestionar", "dbextern.ver_desaparecidos_de_listas.gestionar"]
campos: ["form.id_nom_orbix", "form.tipo_persona", "post.ids_desaparecidos_de_listas", "post.tipo_persona"]
acciones: ["fnjs_baja", "fnjs_traslado"]
estado_revision: "generado"
---

# Ver Desaparecidos De Listas

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/dbextern/controller/ver_desaparecidos_de_listas.php`

## Vistas Relacionadas

- `frontend/dbextern/view/ver_desaparecidos_de_listas.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/dbextern/sincro_baja`
- `/src/dbextern/ver_desaparecidos_de_listas_datos`

## Capacidades Relacionadas

- `dbextern.sincro_baja.gestionar`
- `dbextern.ver_desaparecidos_de_listas.gestionar`

## Campos Detectados

- `form.id_nom_orbix`
- `form.tipo_persona`
- `post.ids_desaparecidos_de_listas`
- `post.tipo_persona`

## Acciones Detectadas

- `fnjs_baja`
- `fnjs_traslado`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
