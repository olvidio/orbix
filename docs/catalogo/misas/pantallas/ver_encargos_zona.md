---
id: "misas.pantalla.ver_encargos_zona"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "misas"
nombre: "Ver Encargos Zona"
controller: "frontend/misas/controller/ver_encargos_zona.php"
vistas: ["frontend/misas/view/ver_encargos_zona.phtml"]
fragmentos_frontend: ["frontend/misas/controller/ver_encargos_zona.php"]
endpoints: ["/src/misas/eliminar_encargo_zona", "/src/misas/guardar_encargo_zona", "/src/misas/ver_encargos_zona_data"]
capacidades: ["misas.eliminar_encargo_zona.gestionar", "misas.guardar_encargo_zona.gestionar", "misas.ver_encargos_zona.gestionar"]
campos: ["form.descripcion_lugar", "form.encargo", "form.id_enc", "form.id_tipo_enc", "form.id_ubi", "form.id_zona", "form.idioma_enc", "form.observ", "form.orden", "form.prioridad", "html.nuevo", "post.id_zona", "post.orden"]
acciones: ["fnjs_generarNomEnc", "fnjs_nuevo", "fnjs_refresh_grid"]
estado_revision: "generado"
---

# Ver Encargos Zona

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/misas/controller/ver_encargos_zona.php`

## Vistas Relacionadas

- `frontend/misas/view/ver_encargos_zona.phtml`

## Fragmentos Frontend Relacionados

- `frontend/misas/controller/ver_encargos_zona.php`

## Endpoints Usados

- `/src/misas/eliminar_encargo_zona`
- `/src/misas/guardar_encargo_zona`
- `/src/misas/ver_encargos_zona_data`

## Capacidades Relacionadas

- `misas.eliminar_encargo_zona.gestionar`
- `misas.guardar_encargo_zona.gestionar`
- `misas.ver_encargos_zona.gestionar`

## Campos Detectados

- `form.descripcion_lugar`
- `form.encargo`
- `form.id_enc`
- `form.id_tipo_enc`
- `form.id_ubi`
- `form.id_zona`
- `form.idioma_enc`
- `form.observ`
- `form.orden`
- `form.prioridad`
- `html.nuevo`
- `post.id_zona`
- `post.orden`

## Acciones Detectadas

- `fnjs_generarNomEnc`
- `fnjs_nuevo`
- `fnjs_refresh_grid`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
