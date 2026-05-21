---
id: "misas.pantalla.ver_encargos_centros"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "misas"
nombre: "Ver Encargos Centros"
controller: "frontend/misas/controller/ver_encargos_centros.php"
vistas: ["frontend/misas/view/ver_encargos_centros.phtml"]
fragmentos_frontend: ["frontend/misas/controller/ver_encargos_centros.php"]
endpoints: ["/src/misas/desplegable_encargos", "/src/misas/eliminar_encargo_centro", "/src/misas/guardar_encargo_centro", "/src/misas/ver_encargos_centros_data"]
capacidades: ["misas.desplegable_encargos.gestionar", "misas.eliminar_encargo_centro.gestionar", "misas.guardar_encargo_centro.gestionar", "misas.ver_encargos_centros.gestionar"]
campos: ["form.id_ctr", "form.id_enc", "form.id_item", "form.id_zona", "html.nuevo", "post.id_zona"]
acciones: ["fnjs_construir_desplegable", "fnjs_nuevo", "fnjs_prepara_select_encargo", "fnjs_refresh_grid"]
estado_revision: "generado"
---

# Ver Encargos Centros

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/misas/controller/ver_encargos_centros.php`

## Vistas Relacionadas

- `frontend/misas/view/ver_encargos_centros.phtml`

## Fragmentos Frontend Relacionados

- `frontend/misas/controller/ver_encargos_centros.php`

## Endpoints Usados

- `/src/misas/desplegable_encargos`
- `/src/misas/eliminar_encargo_centro`
- `/src/misas/guardar_encargo_centro`
- `/src/misas/ver_encargos_centros_data`

## Capacidades Relacionadas

- `misas.desplegable_encargos.gestionar`
- `misas.eliminar_encargo_centro.gestionar`
- `misas.guardar_encargo_centro.gestionar`
- `misas.ver_encargos_centros.gestionar`

## Campos Detectados

- `form.id_ctr`
- `form.id_enc`
- `form.id_item`
- `form.id_zona`
- `html.nuevo`
- `post.id_zona`

## Acciones Detectadas

- `fnjs_construir_desplegable`
- `fnjs_nuevo`
- `fnjs_prepara_select_encargo`
- `fnjs_refresh_grid`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
