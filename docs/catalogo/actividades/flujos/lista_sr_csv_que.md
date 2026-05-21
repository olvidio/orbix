---
id: "actividades.lista_sr_csv_que.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Gestionar Lista Sr Csv Que"
capacidad: "actividades.lista_sr_csv_que.gestionar"
pantallas_principales: []
fragmentos: ["actividades.pantalla.lista_sr_csv_que"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/lista_sr_csv_que_datos"]
estado_revision: "generado"
---

# Flujo - Gestionar Lista Sr Csv Que

Propuesta generada automaticamente desde la capacidad `actividades.lista_sr_csv_que.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ListaSrCsvQueDatos. Endpoint backend para lista_sr_csv_que.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividades.pantalla.lista_sr_csv_que`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.c_activ`
- `form.empiezamax`
- `form.empiezamin`
- `form.id_cdc_mas`
- `form.id_cdc_num`
- `form.periodo`
- `form.status`
- `form.year`
- `post.empiezamax`
- `post.empiezamin`
- `post.periodo`
- `post.year`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividades/lista_sr_csv_que_datos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
