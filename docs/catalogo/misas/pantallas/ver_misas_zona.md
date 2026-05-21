---
id: "misas.pantalla.ver_misas_zona"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "misas"
nombre: "Ver Misas Zona"
controller: "frontend/misas/controller/ver_misas_zona.php"
vistas: []
fragmentos_frontend: ["frontend/misas/controller/ver_misas_zona.php"]
endpoints: ["/src/misas/ver_misas_zona_data"]
capacidades: ["misas.ver_misas_zona.gestionar"]
campos: ["post.empiezamax", "post.empiezamin", "post.id_zona", "post.seleccion"]
acciones: []
estado_revision: "generado"
---

# Ver Misas Zona

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/misas/controller/ver_misas_zona.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/misas/controller/ver_misas_zona.php`

## Endpoints Usados

- `/src/misas/ver_misas_zona_data`

## Capacidades Relacionadas

- `misas.ver_misas_zona.gestionar`

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.id_zona`
- `post.seleccion`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
