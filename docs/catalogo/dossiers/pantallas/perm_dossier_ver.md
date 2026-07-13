---
id: "dossiers.pantalla.perm_dossier_ver"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "dossiers"
nombre: "Perm Dossier Ver"
controller: "frontend/dossiers/controller/perm_dossier_ver.php"
vistas: ["frontend/dossiers/view/perm_dossier_pres.phtml"]
fragmentos_frontend: []
endpoints: ["/src/dossiers/perm_dossier_ver_data"]
capacidades: ["dossiers.perm_dossier_ver.gestionar"]
campos: ["html.app", "html.campo_to", "html.class", "html.codigo", "html.depende_modificar", "html.descripcion", "html.id_tipo_dossier", "html.id_tipo_dossier_rel", "html.que", "html.tabla_from", "html.tabla_to", "post.id_tipo_dossier", "post.tipo"]
acciones: ["fnjs_eliminar", "fnjs_guardar", "fnjs_update_div"]
estado_revision: "revisado"
---

# Perm Dossier Ver

Formulario de permisos de acceso a un tipo de dossier: metadatos (descripción, tablas, app/class/código), checkbox `depende_modificar` y máscaras de lectura/escritura por oficina. Guardar/eliminar solo con `admin_sv`/`admin_sf` (`perm_admin`).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/dossiers/controller/perm_dossier_ver.php`

## Vistas Relacionadas

- `frontend/dossiers/view/perm_dossier_pres.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/dossiers/perm_dossier_ver_data`

## Capacidades Relacionadas

- `dossiers.perm_dossier_ver.gestionar`

## Campos Detectados

- `html.app`
- `html.campo_to`
- `html.class`
- `html.codigo`
- `html.depende_modificar`
- `html.descripcion`
- `html.id_tipo_dossier`
- `html.id_tipo_dossier_rel`
- `html.que`
- `html.tabla_from`
- `html.tabla_to`
- `post.id_tipo_dossier`
- `post.tipo`

## Acciones Detectadas

- `fnjs_eliminar`
- `fnjs_guardar`
- `fnjs_update_div`

## Manual De Usuario

Pantalla revisada contra `frontend/dossiers/` y `src/dossiers/`.

## Ruta de menú

- **Legacy:** sistema > perm_dossiers > ubis · sistema > perm_dossiers > personas · sistema > perm_dossiers > actividades
- **Pills2:** ADMIN LOCAL > perm_dossiers > ubis · ADMIN LOCAL > perm_dossiers > personas · ADMIN LOCAL > perm_dossiers > actividades
