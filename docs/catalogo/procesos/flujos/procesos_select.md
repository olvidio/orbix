---
id: "procesos.procesos_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Gestionar Procesos Select"
capacidad: "procesos.procesos_select.gestionar"
pantallas_principales: []
fragmentos: ["procesos.pantalla.procesos_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/procesos/procesos_select_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Procesos Select

Propuesta generada automaticamente desde la capacidad `procesos.procesos_select.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ProcesosSelect. Caso de uso: datos para la pantalla procesos_select. Devuelve las opciones del desplegable de tipo de proceso para que la vista frontend monte el frontend\shared\web\Desplegable y los web\Hash correspondientes.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.procesos_select`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.refresh`
- `post.stack`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/procesos/procesos_select_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
