---
id: "dossiers.dossiers_lista_fichas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dossiers"
nombre: "Flujo - Gestionar Dossiers Lista Fichas"
capacidad: "dossiers.dossiers_lista_fichas.gestionar"
pantallas_principales: []
fragmentos: ["dossiers.pantalla.lista_dossiers"]
acciones: ["obtener_datos"]
endpoints: ["/src/dossiers/dossiers_lista_fichas_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Dossiers Lista Fichas

Propuesta generada automaticamente desde la capacidad `dossiers.dossiers_lista_fichas.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona DossiersListaFichas. Filas de la tabla de relación de dossiers (modo lista en dossiers_ver). href_ver / href_abrir se firman en el borde HTTP (ver dossiers_lista_fichas_data.php).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `dossiers.pantalla.lista_dossiers`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/dossiers/dossiers_lista_fichas_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
