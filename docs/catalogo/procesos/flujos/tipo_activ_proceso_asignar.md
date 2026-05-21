---
id: "procesos.tipo_activ_proceso_asignar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Gestionar Tipo Activ Proceso Asignar"
capacidad: "procesos.tipo_activ_proceso_asignar.gestionar"
pantallas_principales: ["procesos.pantalla.tipo_activ_proceso"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/procesos/tipo_activ_proceso_asignar"]
estado_revision: "generado"
---

# Flujo - Gestionar Tipo Activ Proceso Asignar

Propuesta generada automaticamente desde la capacidad `procesos.tipo_activ_proceso_asignar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona TipoActivProcesoAsignar. Caso de uso: asigna un id_tipo_proceso al tipo de actividad indicado, distinguiendo entre proceso propio (dl) o no-propio segun propio.

## Punto De Entrada

- `procesos.pantalla.tipo_activ_proceso`

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Ejecutar

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

- `/src/procesos/tipo_activ_proceso_asignar`

## Errores Conocidos

- ``hay un error, no se ha guardado el proceso``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
