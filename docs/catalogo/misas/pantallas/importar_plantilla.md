---
id: "misas.pantalla.importar_plantilla"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "misas"
nombre: "Importar Plantilla"
controller: "frontend/misas/controller/importar_plantilla.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/misas/importar_plantilla_data"]
capacidades: ["misas.importar_plantilla.gestionar"]
campos: ["post.id_zona", "post.tipo_plantilla_destino", "post.tipo_plantilla_origen"]
acciones: []
estado_revision: "generado"
---

# Importar Plantilla

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/misas/controller/importar_plantilla.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/misas/importar_plantilla_data`

## Capacidades Relacionadas

- `misas.importar_plantilla.gestionar`

## Campos Detectados

- `post.id_zona`
- `post.tipo_plantilla_destino`
- `post.tipo_plantilla_origen`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
