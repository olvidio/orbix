---
id: "dossiers.dossiers_lista_fichas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dossiers"
nombre: "Flujo - Ver la relación de dossiers"
capacidad: "dossiers.dossiers_lista_fichas.gestionar"
pantallas_principales: ["dossiers.pantalla.lista_dossiers"]
fragmentos: ["dossiers.pantalla.lista_dossiers"]
acciones: ["obtener_datos"]
endpoints: ["/src/dossiers/dossiers_lista_fichas_data"]
estado_revision: "revisado"
---

# Flujo - Dossiers Lista Fichas

## Objetivo De Usuario

Ver las carpetas (dossiers) disponibles de una persona, actividad o centro/casa y qué puede hacer el usuario en cada una.

## Punto De Entrada

Icono o enlace de dossiers en la ficha de la persona, de la actividad o del centro/casa.

## Escenarios Inferidos

### Ver la relación de dossiers

Pasos propuestos:
1. Abrir la ficha de la persona, actividad o centro/casa y pulsar dossiers.
2. Aparece la **relación de dossiers** con un símbolo por carpeta: **dossier modificable**, **dossier de sólo lectura** o **dossier sin permisos**.
3. Pulsar una carpeta con permiso para abrirla.

Endpoints asociados:
- `/src/dossiers/dossiers_lista_fichas_data`

## Fragmentos O Pantallas Auxiliares

- `dossiers.pantalla.lista_dossiers`

## Endpoints Del Flujo

- `/src/dossiers/dossiers_lista_fichas_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
