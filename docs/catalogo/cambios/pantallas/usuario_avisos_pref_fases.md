---
id: "cambios.pantalla.usuario_avisos_pref_fases"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "cambios"
nombre: "Usuario Avisos Pref Fases"
controller: "frontend/cambios/controller/usuario_avisos_pref_fases.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/cambios/cambio_usuario_objeto_pref_fases_data"]
capacidades: ["cambios.cambio_usuario_objeto_pref_fases.gestionar"]
campos: ["post.dl_propia", "post.id_tipo_activ", "post.objeto"]
acciones: []
estado_revision: "generado"
---

# Usuario Avisos Pref Fases

Controlador AJAX HTML: fragmento con el desplegable de fases para el `id_tipo_activ` y `dl_propia` indicados.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/cambios/controller/usuario_avisos_pref_fases.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/cambios/cambio_usuario_objeto_pref_fases_data`

## Capacidades Relacionadas

- `cambios.cambio_usuario_objeto_pref_fases.gestionar`

## Campos Detectados

- `post.dl_propia`
- `post.id_tipo_activ`
- `post.objeto`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
