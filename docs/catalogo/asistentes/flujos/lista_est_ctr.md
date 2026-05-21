---
id: "asistentes.lista_est_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Gestionar Lista Est Ctr"
capacidad: "asistentes.lista_est_ctr.gestionar"
pantallas_principales: []
fragmentos: ["asistentes.pantalla.lista_est_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/lista_est_ctr_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Lista Est Ctr

Propuesta generada automaticamente desde la capacidad `asistentes.lista_est_ctr.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ListaEstCtr. Listado estudios por centro (lista_est_ctr.php).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.lista_est_ctr`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.empiezamax`
- `post.empiezamin`
- `post.id_ubi`
- `post.n_agd`
- `post.periodo`
- `post.year`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/asistentes/lista_est_ctr_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
