---
id: "ubis.pantalla.delegacion_que"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "ubis"
nombre: "Delegacion Que"
controller: "frontend/ubis/controller/delegacion_que.php"
vistas: ["frontend/ubis/view/delegaciones.phtml"]
fragmentos_frontend: []
endpoints: ["/src/ubis/delegacion_que_data"]
capacidades: ["ubis.delegacion_que.gestionar"]
campos: []
acciones: ["fnjs_cerrar", "fnjs_cmb_id_dl", "fnjs_trasladar"]
estado_revision: "generado"
---

# Delegacion Que

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/ubis/controller/delegacion_que.php`

## Vistas Relacionadas

- `frontend/ubis/view/delegaciones.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/ubis/delegacion_que_data`

## Capacidades Relacionadas

- `ubis.delegacion_que.gestionar`

## Campos Detectados

No se han detectado campos de formulario.

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_cmb_id_dl`
- `fnjs_trasladar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
