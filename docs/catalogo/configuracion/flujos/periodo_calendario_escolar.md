---
id: "configuracion.periodo_calendario_escolar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "configuracion"
nombre: "Flujo - Gestionar Periodo Calendario Escolar"
capacidad: "configuracion.periodo_calendario_escolar.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["obtener_datos"]
endpoints: ["/src/configuracion/periodo_calendario_escolar_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Periodo Calendario Escolar

Propuesta generada automaticamente desde la capacidad `configuracion.periodo_calendario_escolar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona PeriodoCalendarioEscolar. Fechas y metadatos del curso (STGR / CRT) que antes solo estaban en $_SESSION['oConfig'], para inyectar en Periodo del frontend.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

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

- `/src/configuracion/periodo_calendario_escolar_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
