---
id: "dossiers.perm_dossiers.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dossiers"
nombre: "Flujo - Gestionar Perm Dossiers"
capacidad: "dossiers.perm_dossiers.gestionar"
pantallas_principales: []
fragmentos: ["dossiers.pantalla.perm_dossiers"]
acciones: ["obtener_datos"]
endpoints: ["/src/dossiers/perm_dossiers_data"]
estado_revision: "revisado"
---

# Flujo - Perm Dossiers

## Objetivo De Usuario

Elegir el ámbito de tipos de dossier (personas/ubis/actividades) y abrir la edición de permisos de cada tipo desde el menú de administración.

## Punto De Entrada

Menú Legacy: sistema > perm_dossiers > ubis · sistema > perm_dossiers > personas · sistema > perm_dossiers > actividades. Pills2: ADMIN LOCAL > perm_dossiers > ubis · ADMIN LOCAL > perm_dossiers > personas · ADMIN LOCAL > perm_dossiers > actividades.

## Escenarios

### Elegir ámbito y tipo de dossier

1. Menú `perm_dossiers` con `tipo=p|u|a` (personas, ubis o actividades).
2. `perm_dossiers_data` devuelve `a_filas` con `pagina_link_spec` hacia `perm_dossier_ver`.
3. Pulsar «ver o modificar permisos» en una fila carga el formulario del tipo.

## Fragmentos O Pantallas Auxiliares

- `dossiers.pantalla.perm_dossiers`

## Endpoints Del Flujo

- `/src/dossiers/perm_dossiers_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sistema > perm_dossiers > ubis · sistema > perm_dossiers > personas · sistema > perm_dossiers > actividades
- **Pills2:** ADMIN LOCAL > perm_dossiers > ubis · ADMIN LOCAL > perm_dossiers > personas · ADMIN LOCAL > perm_dossiers > actividades
