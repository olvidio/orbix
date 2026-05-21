---
id: "casas.pantalla.grupo"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "casas"
nombre: "Grupo"
controller: "frontend/casas/controller/grupo.php"
vistas: ["frontend/casas/view/grupo.phtml"]
fragmentos_frontend: ["frontend/casas/controller/grupo_form.php", "frontend/casas/controller/grupo_lista.php"]
endpoints: ["/src/casas/grupo_eliminar", "/src/casas/grupo_update"]
capacidades: ["casas.grupo.gestionar"]
campos: ["form.id_item", "form.id_ubi_hijo", "form.id_ubi_padre"]
acciones: ["fnjs_cerrar", "fnjs_eliminar", "fnjs_guardar", "fnjs_modificar", "fnjs_update_div", "fnjs_ver"]
estado_revision: "generado"
---

# Grupo

Pantalla principal del módulo `casas` - grupos de casas (padre ↔ hijo).

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/casas/controller/grupo.php`

## Vistas Relacionadas

- `frontend/casas/view/grupo.phtml`

## Fragmentos Frontend Relacionados

- `frontend/casas/controller/grupo_form.php`
- `frontend/casas/controller/grupo_lista.php`

## Endpoints Usados

- `/src/casas/grupo_eliminar`
- `/src/casas/grupo_update`

## Capacidades Relacionadas

- `casas.grupo.gestionar`

## Campos Detectados

- `form.id_item`
- `form.id_ubi_hijo`
- `form.id_ubi_padre`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_eliminar`
- `fnjs_guardar`
- `fnjs_modificar`
- `fnjs_update_div`
- `fnjs_ver`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
