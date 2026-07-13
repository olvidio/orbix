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
estado_revision: "revisado"
---

# Migraciones Lista

Listado de migraciones SQL pendientes/aplicadas con ejecución y borrado de registro.

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

1. Acceder desde el menú de administración DB o configuración.
2. Completar el formulario y ejecutar la acción.
3. Revisar avisos/errores en el panel de respuesta.

## Ruta de menú

- **Legacy:** sistema > DB > actualizar DB
- **Pills2:** sistema > DB > actualizar DB
