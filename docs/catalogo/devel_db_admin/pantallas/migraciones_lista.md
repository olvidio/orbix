---
id: "devel_db_admin.pantalla.migraciones_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "devel_db_admin"
nombre: "Migraciones Lista"
controller: "frontend/devel_db_admin/controller/migraciones_lista.php"
vistas: ["frontend/devel_db_admin/view/migraciones_lista.phtml"]
fragmentos_frontend: []
endpoints: ["/src/devel_db_admin/migraciones_ejecutar", "/src/devel_db_admin/migraciones_lista_data", "/src/devel_db_admin/migraciones_quitar_registro"]
capacidades: ["devel_db_admin.migraciones.gestionar", "devel_db_admin.migraciones_ejecutar.gestionar", "devel_db_admin.migraciones_quitar_registro.gestionar"]
campos: ["form.sel"]
acciones: ["fnjs_migraciones_checked", "fnjs_migraciones_ejecutar_hasta", "fnjs_migraciones_ejecutar_seleccion", "fnjs_migraciones_enviar", "fnjs_migraciones_quitar_registro"]
estado_revision: "generado"
---

# Migraciones Lista

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/devel_db_admin/controller/migraciones_lista.php`

## Vistas Relacionadas

- `frontend/devel_db_admin/view/migraciones_lista.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/devel_db_admin/migraciones_ejecutar`
- `/src/devel_db_admin/migraciones_lista_data`
- `/src/devel_db_admin/migraciones_quitar_registro`

## Capacidades Relacionadas

- `devel_db_admin.migraciones.gestionar`
- `devel_db_admin.migraciones_ejecutar.gestionar`
- `devel_db_admin.migraciones_quitar_registro.gestionar`

## Campos Detectados

- `form.sel`

## Acciones Detectadas

- `fnjs_migraciones_checked`
- `fnjs_migraciones_ejecutar_hasta`
- `fnjs_migraciones_ejecutar_seleccion`
- `fnjs_migraciones_enviar`
- `fnjs_migraciones_quitar_registro`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
