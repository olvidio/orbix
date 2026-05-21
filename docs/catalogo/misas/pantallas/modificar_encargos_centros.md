---
id: "misas.pantalla.modificar_encargos_centros"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "misas"
nombre: "Modificar Encargos Centros"
controller: "frontend/misas/controller/modificar_encargos_centros.php"
vistas: ["frontend/misas/view/modificar_encargos_centros.phtml"]
fragmentos_frontend: ["frontend/misas/controller/ver_encargos_centros.php"]
endpoints: ["/src/misas/modificar_encargos_centros_data"]
capacidades: ["misas.modificar_encargos_centros.gestionar"]
campos: ["form.id_zona"]
acciones: ["fnjs_ver_encargos_centros"]
estado_revision: "generado"
---

# Modificar Encargos Centros

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/misas/controller/modificar_encargos_centros.php`

## Vistas Relacionadas

- `frontend/misas/view/modificar_encargos_centros.phtml`

## Fragmentos Frontend Relacionados

- `frontend/misas/controller/ver_encargos_centros.php`

## Endpoints Usados

- `/src/misas/modificar_encargos_centros_data`

## Capacidades Relacionadas

- `misas.modificar_encargos_centros.gestionar`

## Campos Detectados

- `form.id_zona`

## Acciones Detectadas

- `fnjs_ver_encargos_centros`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
