---
id: "dossiers.perm_dossiers.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dossiers"
nombre: "Flujo - Elegir los dossiers a configurar"
capacidad: "dossiers.perm_dossiers.gestionar"
pantallas_principales: ["dossiers.pantalla.perm_dossiers"]
fragmentos: ["dossiers.pantalla.perm_dossiers"]
acciones: ["obtener_datos"]
endpoints: ["/src/dossiers/perm_dossiers_data"]
estado_revision: "revisado"
---

# Flujo - Perm Dossiers

## Objetivo De Usuario

Elegir si se quieren configurar los dossiers de personas, centros/casas o actividades, y abrir los permisos de cada tipo.

## Punto De Entrada

Menú **sistema > perm_dossiers > ubis / personas / actividades** (Pills2: ADMIN LOCAL > perm_dossiers).

## Escenarios Inferidos

### Elegir los dossiers a configurar

Pasos propuestos:
1. Entrar en **perm_dossiers** y elegir **personas**, **ubis** o **actividades**.
2. Aparece la lista de **dossiers seleccionados**.
3. Pulsar **ver o modificar permisos** del tipo que interese.

Endpoints asociados:
- `/src/dossiers/perm_dossiers_data`

## Fragmentos O Pantallas Auxiliares

- `dossiers.pantalla.perm_dossiers`

## Endpoints Del Flujo

- `/src/dossiers/perm_dossiers_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sistema > perm_dossiers > ubis · sistema > perm_dossiers > personas · sistema > perm_dossiers > actividades
- **Pills2:** ADMIN LOCAL > perm_dossiers > ubis · ADMIN LOCAL > perm_dossiers > personas · ADMIN LOCAL > perm_dossiers > actividades
