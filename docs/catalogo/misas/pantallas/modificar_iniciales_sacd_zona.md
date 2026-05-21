---
id: "misas.pantalla.modificar_iniciales_sacd_zona"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "misas"
nombre: "Modificar Iniciales Sacd Zona"
controller: "frontend/misas/controller/modificar_iniciales_sacd_zona.php"
vistas: ["frontend/misas/view/modificar_iniciales_sacd_zona.phtml"]
fragmentos_frontend: ["frontend/misas/controller/ver_iniciales_zona.php"]
endpoints: ["/src/misas/modificar_iniciales_sacd_zona_data"]
capacidades: ["misas.modificar_iniciales_sacd_zona.gestionar"]
campos: ["form.id_zona"]
acciones: ["fnjs_ver_iniciales_sacd_zona"]
estado_revision: "generado"
---

# Modificar Iniciales Sacd Zona

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/misas/controller/modificar_iniciales_sacd_zona.php`

## Vistas Relacionadas

- `frontend/misas/view/modificar_iniciales_sacd_zona.phtml`

## Fragmentos Frontend Relacionados

- `frontend/misas/controller/ver_iniciales_zona.php`

## Endpoints Usados

- `/src/misas/modificar_iniciales_sacd_zona_data`

## Capacidades Relacionadas

- `misas.modificar_iniciales_sacd_zona.gestionar`

## Campos Detectados

- `form.id_zona`

## Acciones Detectadas

- `fnjs_ver_iniciales_sacd_zona`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
