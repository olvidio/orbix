---
id: "procesos.pantalla.tipo_activ_proceso_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "procesos"
nombre: "Tipo Activ Proceso Lista"
controller: "frontend/procesos/controller/tipo_activ_proceso_lista.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/procesos/tipo_activ_proceso_lista"]
capacidades: ["procesos.tipo_activ_proceso.gestionar"]
campos: []
acciones: ["fnjs_cambiar_proceso"]
estado_revision: "revisado"
---

# Tipo Activ Proceso Lista

Fragmento AJAX que renderiza la tabla de tipos de actividad con el proceso asignado (propio y no propio) mediante `tipo_activ_proceso_lista`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/procesos/controller/tipo_activ_proceso_lista.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/procesos/tipo_activ_proceso_lista`

## Capacidades Relacionadas

- `procesos.tipo_activ_proceso.gestionar`

## Campos Detectados

No se han detectado campos de formulario.

## Acciones Detectadas

- `fnjs_cambiar_proceso`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
