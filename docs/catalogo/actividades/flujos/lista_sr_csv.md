---
id: "actividades.lista_sr_csv.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Gestionar Lista Sr Csv"
capacidad: "actividades.lista_sr_csv.gestionar"
pantallas_principales: []
fragmentos: ["actividades.pantalla.lista_sr_csv"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/lista_sr_csv_datos"]
estado_revision: "generado"
---

# Flujo - Gestionar Lista Sr Csv

Propuesta generada automaticamente desde la capacidad `actividades.lista_sr_csv.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ListaSrCsvListado. Endpoint backend para lista_sr_csv (listado SR + exportacion).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividades.pantalla.lista_sr_csv`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.c_activ`
- `post.dl_org`
- `post.empiezamax`
- `post.empiezamin`
- `post.id_cdc`
- `post.periodo`
- `post.que`
- `post.status`
- `post.year`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividades/lista_sr_csv_datos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
