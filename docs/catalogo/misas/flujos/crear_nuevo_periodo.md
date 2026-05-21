---
id: "misas.crear_nuevo_periodo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Crear Nuevo Periodo"
capacidad: "misas.crear_nuevo_periodo.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.crear_nuevo_periodo"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/crear_nuevo_periodo_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Crear Nuevo Periodo

Propuesta generada automaticamente desde la capacidad `misas.crear_nuevo_periodo.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CrearNuevoPeriodo. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.crear_nuevo_periodo`

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
- `post.id_zona`
- `post.orden`
- `post.periodo`
- `post.seleccion`
- `post.tipoplantilla`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/misas/crear_nuevo_periodo_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
