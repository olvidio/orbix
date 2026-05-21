---
id: "misas.pantalla.cambiar_status"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "misas"
nombre: "Cambiar Status"
controller: "frontend/misas/controller/cambiar_status.php"
vistas: ["frontend/misas/view/cambiar_status.phtml"]
fragmentos_frontend: ["frontend/misas/controller/cambiar_status.php", "frontend/misas/controller/ver_cuadricula_zona.php"]
endpoints: ["/src/misas/cambiar_status_data", "/src/misas/nuevo_status"]
capacidades: ["misas.cambiar_status.gestionar", "misas.nuevo_status.gestionar"]
campos: ["form.empiezamax", "form.empiezamin", "form.estado", "form.id_zona", "form.orden", "form.periodo", "form.tipo_plantilla", "html.cambiar"]
acciones: ["button:cambiar", "fnjs_nuevo_estado", "fnjs_ver_cuadricula_zona"]
estado_revision: "generado"
---

# Cambiar Status

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/misas/controller/cambiar_status.php`

## Vistas Relacionadas

- `frontend/misas/view/cambiar_status.phtml`

## Fragmentos Frontend Relacionados

- `frontend/misas/controller/cambiar_status.php`
- `frontend/misas/controller/ver_cuadricula_zona.php`

## Endpoints Usados

- `/src/misas/cambiar_status_data`
- `/src/misas/nuevo_status`

## Capacidades Relacionadas

- `misas.cambiar_status.gestionar`
- `misas.nuevo_status.gestionar`

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.estado`
- `form.id_zona`
- `form.orden`
- `form.periodo`
- `form.tipo_plantilla`
- `html.cambiar`

## Acciones Detectadas

- `button:cambiar`
- `fnjs_nuevo_estado`
- `fnjs_ver_cuadricula_zona`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
