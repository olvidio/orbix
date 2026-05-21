---
id: "casas.pantalla.grupo_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "casas"
nombre: "Grupo Lista"
controller: "frontend/casas/controller/grupo_lista.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/casas/grupo_lista_data"]
capacidades: ["casas.grupo.gestionar"]
campos: []
acciones: ["fnjs_modificar"]
estado_revision: "generado"
---

# Grupo Lista

Controlador AJAX HTML: listado de `GrupoCasa`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/casas/controller/grupo_lista.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/casas/grupo_lista_data`

## Capacidades Relacionadas

- `casas.grupo.gestionar`

## Campos Detectados

No se han detectado campos de formulario.

## Acciones Detectadas

- `fnjs_modificar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
