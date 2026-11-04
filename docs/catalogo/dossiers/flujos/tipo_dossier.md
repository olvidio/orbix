---
id: "dossiers.tipo_dossier.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dossiers"
nombre: "Flujo - Guardar o eliminar un tipo de dossier"
capacidad: "dossiers.tipo_dossier.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["eliminar", "guardar"]
endpoints: ["/src/dossiers/tipo_dossier_eliminar", "/src/dossiers/tipo_dossier_guardar"]
estado_revision: "revisado"
---

# Flujo - Tipo Dossier

## Objetivo De Usuario

Guardar o eliminar un tipo de dossier (solo administradores).

## Punto De Entrada

Menú **sistema > perm_dossiers > ubis / personas / actividades** (Pills2: ADMIN LOCAL > perm_dossiers).

## Escenarios Inferidos

### Guardar o eliminar un tipo de dossier

Pasos propuestos:
1. Abrir el tipo de dossier desde **perm_dossiers**.
2. Pulsar **guardar cambios** para aplicar, o **eliminar** y confirmar para quitarlo.
3. Solo los administradores (admin_sv/admin_sf) pueden hacerlo.

Endpoints asociados:
- `/src/dossiers/tipo_dossier_eliminar`
- `/src/dossiers/tipo_dossier_guardar`

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
