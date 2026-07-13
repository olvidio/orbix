---
id: "dossiers.tipo_dossier.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dossiers"
nombre: "Flujo - Gestionar Tipo Dossier"
capacidad: "dossiers.tipo_dossier.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["eliminar", "guardar"]
endpoints: ["/src/dossiers/tipo_dossier_eliminar", "/src/dossiers/tipo_dossier_guardar"]
estado_revision: "revisado"
---

# Flujo - Tipo Dossier

## Objetivo De Usuario

Persistir cambios (`tipo_dossier_guardar`) o eliminar (`tipo_dossier_eliminar`) un tipo de dossier desde el formulario `perm_dossier_ver` (solo administradores `admin_sv`/`admin_sf`).

## Punto De Entrada

Menú Legacy: sistema > perm_dossiers > ubis · sistema > perm_dossiers > personas · sistema > perm_dossiers > actividades. Pills2: ADMIN LOCAL > perm_dossiers > ubis · ADMIN LOCAL > perm_dossiers > personas · ADMIN LOCAL > perm_dossiers > actividades.

## Escenarios

### Guardar cambios

1. Admin pulsa «guardar cambios» → `fnjs_guardar` serializa `#frm2` y POST a `tipo_dossier_guardar`.
2. Éxito: `success: true`, `data: "ok"`. Error: alert con `mensaje`.

### Eliminar tipo

1. Admin pulsa «eliminar» → confirmación → POST `tipo_dossier_eliminar`.
2. Éxito: recarga listado `perm_dossiers` vía `fnjs_update_div` y `go_to`.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Endpoints Del Flujo

- `/src/dossiers/tipo_dossier_eliminar`
- `/src/dossiers/tipo_dossier_guardar`

## Errores Conocidos

- `falta id_tipo_dossier`
- `No se encuentra el dossier: <id>`
- `Hay un error, no se ha guardado.`
- `Hay un error, no se ha eliminado.`

## Ruta de menú

- **Legacy:** sistema > perm_dossiers > ubis · sistema > perm_dossiers > personas · sistema > perm_dossiers > actividades
- **Pills2:** ADMIN LOCAL > perm_dossiers > ubis · ADMIN LOCAL > perm_dossiers > personas · ADMIN LOCAL > perm_dossiers > actividades
