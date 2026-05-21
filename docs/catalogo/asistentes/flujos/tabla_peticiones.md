---
id: "asistentes.tabla_peticiones.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Gestionar Tabla Peticiones"
capacidad: "asistentes.tabla_peticiones.gestionar"
pantallas_principales: []
fragmentos: ["asistentes.pantalla.tabla_peticiones"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/tabla_peticiones_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Tabla Peticiones

Propuesta generada automaticamente desde la capacidad `asistentes.tabla_peticiones.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona TablaPeticiones. JSON para {.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.tabla_peticiones`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.id_activ_old`
- `post.sel`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/asistentes/tabla_peticiones_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
