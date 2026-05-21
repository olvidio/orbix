---
id: "misas.pantalla.modificar_plantilla"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "misas"
nombre: "Modificar Plantilla"
controller: "frontend/misas/controller/modificar_plantilla.php"
vistas: ["frontend/misas/view/modificar_plantilla.phtml"]
fragmentos_frontend: ["frontend/misas/controller/importar_plantilla.php", "frontend/misas/controller/modificar_cuadricula_zona.php", "frontend/misas/controller/modificar_plantilla.php"]
endpoints: ["/src/misas/modificar_plantilla_data"]
capacidades: ["misas.modificar_plantilla.gestionar"]
campos: ["form.id_zona", "form.importar_de_plantilla", "form.orden", "form.tipo_plantilla", "form.tipo_plantilla_destino", "form.tipo_plantilla_origen", "html.importar"]
acciones: ["button:importar", "fnjs_importar_de_plantilla_zona", "fnjs_ver_plantilla_zona"]
estado_revision: "generado"
---

# Modificar Plantilla

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/misas/controller/modificar_plantilla.php`

## Vistas Relacionadas

- `frontend/misas/view/modificar_plantilla.phtml`

## Fragmentos Frontend Relacionados

- `frontend/misas/controller/importar_plantilla.php`
- `frontend/misas/controller/modificar_cuadricula_zona.php`
- `frontend/misas/controller/modificar_plantilla.php`

## Endpoints Usados

- `/src/misas/modificar_plantilla_data`

## Capacidades Relacionadas

- `misas.modificar_plantilla.gestionar`

## Campos Detectados

- `form.id_zona`
- `form.importar_de_plantilla`
- `form.orden`
- `form.tipo_plantilla`
- `form.tipo_plantilla_destino`
- `form.tipo_plantilla_origen`
- `html.importar`

## Acciones Detectadas

- `button:importar`
- `fnjs_importar_de_plantilla_zona`
- `fnjs_ver_plantilla_zona`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
