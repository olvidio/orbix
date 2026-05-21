---
id: "procesos.pantalla.tipo_activ_proceso"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "procesos"
nombre: "Tipo Activ Proceso"
controller: "frontend/procesos/controller/tipo_activ_proceso.php"
vistas: []
fragmentos_frontend: ["frontend/procesos/controller/tipo_activ_proceso_lista.php", "frontend/procesos/controller/tipo_activ_proceso_lst_posibles.php"]
endpoints: ["/src/procesos/tipo_activ_proceso_asignar", "/src/procesos/tipo_activ_proceso_lst_posibles"]
capacidades: ["procesos.tipo_activ_proceso_asignar.gestionar", "procesos.tipo_activ_proceso_lst_posibles.gestionar"]
campos: []
acciones: []
estado_revision: "generado"
---

# Tipo Activ Proceso

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/procesos/controller/tipo_activ_proceso.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/procesos/controller/tipo_activ_proceso_lista.php`
- `frontend/procesos/controller/tipo_activ_proceso_lst_posibles.php`

## Endpoints Usados

- `/src/procesos/tipo_activ_proceso_asignar`
- `/src/procesos/tipo_activ_proceso_lst_posibles`

## Capacidades Relacionadas

- `procesos.tipo_activ_proceso_asignar.gestionar`
- `procesos.tipo_activ_proceso_lst_posibles.gestionar`

## Campos Detectados

No se han detectado campos de formulario.

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
