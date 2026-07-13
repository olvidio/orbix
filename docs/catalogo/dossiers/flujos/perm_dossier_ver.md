---
id: "dossiers.perm_dossier_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dossiers"
nombre: "Flujo - Gestionar Perm Dossier Ver"
capacidad: "dossiers.perm_dossier_ver.gestionar"
pantallas_principales: []
fragmentos: ["dossiers.pantalla.perm_dossier_ver"]
acciones: ["obtener_datos"]
endpoints: ["/src/dossiers/perm_dossier_ver_data"]
estado_revision: "revisado"
---

# Flujo - Perm Dossier Ver

## Objetivo De Usuario

Consultar o modificar la definición y máscaras de permiso de un `TipoDossier` concreto; volver al listado tras guardar o eliminar.

## Punto De Entrada

Menú Legacy: sistema > perm_dossiers > ubis · sistema > perm_dossiers > personas · sistema > perm_dossiers > actividades. Pills2: ADMIN LOCAL > perm_dossiers > ubis · ADMIN LOCAL > perm_dossiers > personas · ADMIN LOCAL > perm_dossiers > actividades.

## Escenarios

### Consultar permisos de un tipo

1. Llega `tipo` + `id_tipo_dossier` desde el listado.
2. `perm_dossier_ver_data` devuelve metadatos, máscaras y `permiso_dossier_bit_map`.
3. Si el usuario no es `admin_sv`/`admin_sf`, el formulario es solo lectura (`botones=0`).

### Guardar o eliminar (admin)

1. Con `perm_admin`, aparecen botones guardar/eliminar.
2. Guardar: POST `tipo_dossier_guardar` con campos del formulario y arrays `Permiso_lectura[]`/`Permiso_escritura[]`.
3. Eliminar: confirmación con `txt_eliminar`, POST `tipo_dossier_eliminar`, vuelta al listado con `go_to`.

## Fragmentos O Pantallas Auxiliares

- `dossiers.pantalla.perm_dossier_ver`

## Endpoints Del Flujo

- `/src/dossiers/perm_dossier_ver_data`

## Errores Conocidos

- `No se encuentra el dossier: <id>`

## Ruta de menú

- **Legacy:** sistema > perm_dossiers > ubis · sistema > perm_dossiers > personas · sistema > perm_dossiers > actividades
- **Pills2:** ADMIN LOCAL > perm_dossiers > ubis · ADMIN LOCAL > perm_dossiers > personas · ADMIN LOCAL > perm_dossiers > actividades
