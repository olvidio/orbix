---
id: "misas.pantalla.modificar_encargos"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "misas"
nombre: "Modificar Encargos"
controller: "frontend/misas/controller/modificar_encargos.php"
vistas: ["frontend/misas/view/modificar_encargos.phtml"]
fragmentos_frontend: ["frontend/misas/controller/ver_encargos_zona.php"]
endpoints: ["/src/misas/modificar_encargos_data"]
capacidades: ["misas.modificar_encargos.gestionar"]
campos: ["form.id_zona", "form.orden"]
acciones: ["fnjs_ver_encargos_zona"]
estado_revision: "generado"
---

# Modificar Encargos

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/misas/controller/modificar_encargos.php`

## Vistas Relacionadas

- `frontend/misas/view/modificar_encargos.phtml`

## Fragmentos Frontend Relacionados

- `frontend/misas/controller/ver_encargos_zona.php`

## Endpoints Usados

- `/src/misas/modificar_encargos_data`

## Capacidades Relacionadas

- `misas.modificar_encargos.gestionar`

## Campos Detectados

- `form.id_zona`
- `form.orden`

## Acciones Detectadas

- `fnjs_ver_encargos_zona`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
