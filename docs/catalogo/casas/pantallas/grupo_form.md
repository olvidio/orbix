---
id: "casas.pantalla.grupo_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "casas"
nombre: "Grupo Form"
controller: "frontend/casas/controller/grupo_form.php"
vistas: ["frontend/casas/view/grupo_form.phtml"]
fragmentos_frontend: ["frontend/casas/controller/grupo_form.php"]
endpoints: ["/src/casas/grupo_form_data"]
capacidades: ["casas.grupo.gestionar"]
campos: ["html.cancelar", "html.id_item", "html.ok", "post.id_item"]
acciones: ["fnjs_cerrar", "fnjs_guardar"]
estado_revision: "revisado"
---

# Grupo Form

Controlador AJAX HTML: formulario `GrupoCasa` (nuevo/editar).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/casas/controller/grupo_form.php`

## Vistas Relacionadas

- `frontend/casas/view/grupo_form.phtml`

## Fragmentos Frontend Relacionados

- `frontend/casas/controller/grupo_form.php`

## Endpoints Usados

- `/src/casas/grupo_form_data`

## Capacidades Relacionadas

- `casas.grupo.gestionar`

## Campos Detectados

- `html.cancelar`
- `html.id_item`
- `html.ok`
- `post.id_item`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.
## Ruta de menú

- **Legacy:** adl > Gestión casas > grupos
- **Pills2:** CASAS Y CTR > Gestión casas > grupos

