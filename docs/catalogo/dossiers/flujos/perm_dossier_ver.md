---
id: "dossiers.perm_dossier_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dossiers"
nombre: "Flujo - Consultar o cambiar los permisos de un dossier"
capacidad: "dossiers.perm_dossier_ver.gestionar"
pantallas_principales: ["dossiers.pantalla.perm_dossier_ver"]
fragmentos: ["dossiers.pantalla.perm_dossier_ver"]
acciones: ["obtener_datos"]
endpoints: ["/src/dossiers/perm_dossier_ver_data"]
estado_revision: "revisado"
---

# Flujo - Perm Dossier Ver

## Objetivo De Usuario

Ver o cambiar la definición y los permisos de un tipo de dossier, y volver a la lista.

## Punto De Entrada

Menú **sistema > perm_dossiers > ubis / personas / actividades** (Pills2: ADMIN LOCAL > perm_dossiers).

## Escenarios Inferidos

### Consultar o cambiar los permisos de un dossier

Pasos propuestos:
1. En la lista de **perm_dossiers**, abrir el tipo de dossier.
2. Revisar su descripción, el código y las máscaras de permiso. «depende_modificar» activo implica que solo modificará la oficina correspondiente (agd para los agd, sm para los n, sg para los s).
3. El formulario también muestra campos técnicos editables (**tabla_from**, **tabla_to**, **clase**, **código**). Es intencionado que un administrador de delegación pueda cambiarlos, pero conviene no tocarlos salvo que se sepa lo que se hace.
3. Pulsar **guardar cambios**; después se vuelve a la lista.

Endpoints asociados:
- `/src/dossiers/perm_dossier_ver_data`

## Fragmentos O Pantallas Auxiliares

- `dossiers.pantalla.perm_dossier_ver`

## Endpoints Del Flujo

- `/src/dossiers/perm_dossier_ver_data`

## Errores Conocidos

- `No se encuentra el dossier: <id>`

## Ruta de menú

- **Legacy:** sistema > perm_dossiers > ubis · sistema > perm_dossiers > personas · sistema > perm_dossiers > actividades
- **Pills2:** ADMIN LOCAL > perm_dossiers > ubis · ADMIN LOCAL > perm_dossiers > personas · ADMIN LOCAL > perm_dossiers > actividades
