---
id: "procesos.pantalla.procesos_get_listado"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "procesos"
nombre: "Procesos Get Listado"
controller: "frontend/procesos/controller/procesos_get_listado.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/procesos/procesos_get_listado"]
capacidades: ["procesos.procesos_get_listado.gestionar"]
campos: []
acciones: ["fnjs_eliminar", "fnjs_modificar"]
estado_revision: "generado"
---

# Procesos Get Listado

Renderer frontend de la tabla de fases del proceso.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/procesos/controller/procesos_get_listado.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/procesos/procesos_get_listado`

## Capacidades Relacionadas

- `procesos.procesos_get_listado.gestionar`

## Campos Detectados

No se han detectado campos de formulario.

## Acciones Detectadas

- `fnjs_eliminar`
- `fnjs_modificar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
