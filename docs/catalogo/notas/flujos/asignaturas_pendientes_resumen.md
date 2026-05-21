---
id: "notas.asignaturas_pendientes_resumen.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Gestionar Asignaturas Pendientes Resumen"
capacidad: "notas.asignaturas_pendientes_resumen.gestionar"
pantallas_principales: []
fragmentos: ["notas.pantalla.asignaturas_pendientes_resumen"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/asignaturas_pendientes_resumen_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Asignaturas Pendientes Resumen

Propuesta generada automaticamente desde la capacidad `notas.asignaturas_pendientes_resumen.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona AsignaturasPendientesResumen. Resumen: número de alumnos con cada asignatura pendiente, desglosado por tramo (nb, nc1, nc2, n total, ab, ac1, ac2, a total). Sucesor de la lógica embebida en frontend/notas/controller/asignaturas_pendientes_resumen.php.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.asignaturas_pendientes_resumen`

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
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/notas/asignaturas_pendientes_resumen_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
