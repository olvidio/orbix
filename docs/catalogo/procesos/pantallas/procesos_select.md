---
id: "procesos.pantalla.procesos_select"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "procesos"
nombre: "Procesos Select"
controller: "frontend/procesos/controller/procesos_select.php"
vistas: []
fragmentos_frontend: ["frontend/procesos/controller/procesos_get.php", "frontend/procesos/controller/procesos_get_listado.php", "frontend/procesos/controller/procesos_ver.php"]
endpoints: ["/src/procesos/procesos_clonar", "/src/procesos/procesos_eliminar", "/src/procesos/procesos_get", "/src/procesos/procesos_regenerar", "/src/procesos/procesos_select_data", "/src/procesos/procesos_update"]
capacidades: ["procesos.procesos.gestionar", "procesos.procesos_clonar.gestionar", "procesos.procesos_regenerar.gestionar", "procesos.procesos_select.gestionar"]
campos: ["post.refresh", "post.stack"]
acciones: []
estado_revision: "generado"
---

# Procesos Select

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/procesos/controller/procesos_select.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/procesos/controller/procesos_get.php`
- `frontend/procesos/controller/procesos_get_listado.php`
- `frontend/procesos/controller/procesos_ver.php`

## Endpoints Usados

- `/src/procesos/procesos_clonar`
- `/src/procesos/procesos_eliminar`
- `/src/procesos/procesos_get`
- `/src/procesos/procesos_regenerar`
- `/src/procesos/procesos_select_data`
- `/src/procesos/procesos_update`

## Capacidades Relacionadas

- `procesos.procesos.gestionar`
- `procesos.procesos_clonar.gestionar`
- `procesos.procesos_regenerar.gestionar`
- `procesos.procesos_select.gestionar`

## Campos Detectados

- `post.refresh`
- `post.stack`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
