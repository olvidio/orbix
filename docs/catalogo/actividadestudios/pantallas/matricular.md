---
id: "actividadestudios.pantalla.matricular"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadestudios"
nombre: "Matricular"
controller: "frontend/actividadestudios/controller/matricular.php"
vistas: ["frontend/actividadestudios/view/matricular.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadestudios/matricula_automatica"]
capacidades: ["actividadestudios.matricula_automatica.gestionar"]
campos: []
acciones: []
estado_revision: "generado"
---

# Matricular

Pantalla de menu "matricular a todos".

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadestudios/controller/matricular.php`

## Vistas Relacionadas

- `frontend/actividadestudios/view/matricular.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadestudios/matricula_automatica`

## Capacidades Relacionadas

- `actividadestudios.matricula_automatica.gestionar`

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
