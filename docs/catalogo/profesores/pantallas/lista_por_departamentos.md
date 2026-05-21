---
id: "profesores.pantalla.lista_por_departamentos"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "profesores"
nombre: "Lista Por Departamentos"
controller: "frontend/profesores/controller/lista_por_departamentos.php"
vistas: ["frontend/profesores/view/lista_por_departamentos.phtml"]
fragmentos_frontend: ["frontend/profesores/controller/lista_por_departamentos.php"]
endpoints: ["/src/profesores/lista_por_departamentos"]
capacidades: ["profesores.lista_por_departamentos.gestionar"]
campos: ["form.dl", "post.dl", "post.filtro"]
acciones: ["fnjs_left_side_hide"]
estado_revision: "generado"
---

# Lista Por Departamentos

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/profesores/controller/lista_por_departamentos.php`

## Vistas Relacionadas

- `frontend/profesores/view/lista_por_departamentos.phtml`

## Fragmentos Frontend Relacionados

- `frontend/profesores/controller/lista_por_departamentos.php`

## Endpoints Usados

- `/src/profesores/lista_por_departamentos`

## Capacidades Relacionadas

- `profesores.lista_por_departamentos.gestionar`

## Campos Detectados

- `form.dl`
- `post.dl`
- `post.filtro`

## Acciones Detectadas

- `fnjs_left_side_hide`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
