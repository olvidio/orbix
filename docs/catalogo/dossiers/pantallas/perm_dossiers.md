---
id: "dossiers.pantalla.perm_dossiers"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "dossiers"
nombre: "Perm Dossiers"
controller: "frontend/dossiers/controller/perm_dossiers.php"
vistas: ["frontend/dossiers/view/perm_dossiers.phtml"]
fragmentos_frontend: []
endpoints: ["/src/dossiers/perm_dossiers_data"]
capacidades: ["dossiers.perm_dossiers.gestionar"]
campos: ["post.tipo"]
acciones: ["fnjs_update_div"]
estado_revision: "revisado"
---

# Perm Dossiers

Listado de tipos de dossier para administrar permisos de un ámbito (`tipo` = `p` personas, `u` ubis, `a` actividades). Cada fila enlaza a `perm_dossier_ver` para ver o modificar la definición del tipo.

## Tipo

- Subtipo: `pantalla_principal`
- Controller: `frontend/dossiers/controller/perm_dossiers.php`

## Vistas Relacionadas

- `frontend/dossiers/view/perm_dossiers.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/dossiers/perm_dossiers_data`

## Capacidades Relacionadas

- `dossiers.perm_dossiers.gestionar`

## Campos Detectados

- `post.tipo`

## Acciones Detectadas

- `fnjs_update_div`

## Manual De Usuario

Pantalla revisada contra `frontend/dossiers/` y `src/dossiers/`.

## Ruta de menú

- **Legacy:** sistema > perm_dossiers > ubis · sistema > perm_dossiers > personas · sistema > perm_dossiers > actividades
- **Pills2:** ADMIN LOCAL > perm_dossiers > ubis · ADMIN LOCAL > perm_dossiers > personas · ADMIN LOCAL > perm_dossiers > actividades
