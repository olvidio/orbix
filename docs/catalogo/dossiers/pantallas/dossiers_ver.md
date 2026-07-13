---
id: "dossiers.pantalla.dossiers_ver"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "dossiers"
nombre: "Dossiers Ver"
controller: "frontend/dossiers/controller/dossiers_ver.php"
vistas: ["frontend/dossiers/view/dossiers_ver_top.phtml", "frontend/dossiers/view/lista_dossiers.phtml"]
fragmentos_frontend: []
endpoints: ["/src/dossiers/dossiers_ver_pantalla_data"]
capacidades: ["dossiers.dossiers_ver_pantalla.gestionar"]
campos: []
acciones: ["fnjs_update_div"]
estado_revision: "revisado"
---

# Dossiers Ver

Visor de dossiers de una entidad (persona/actividad/ubi): cabecera con enlaces «dossiers» y «home», modo lista de carpetas o modo ficha con segmentos `select_*` y tablas `datos_tabla`. Gestiona navegación con `ListNavSupport` y firma `link_spec` en el frontend (`HashFront`).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/dossiers/controller/dossiers_ver.php`

## Vistas Relacionadas

- `frontend/dossiers/view/dossiers_ver_top.phtml`
- `frontend/dossiers/view/lista_dossiers.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/dossiers/dossiers_ver_pantalla_data`

## Capacidades Relacionadas

- `dossiers.dossiers_ver_pantalla.gestionar`

## Campos Detectados

No se han detectado campos de formulario.

## Acciones Detectadas

- `fnjs_update_div`

## Manual De Usuario

Pantalla revisada contra `frontend/dossiers/` y `src/dossiers/`.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
