---
id: "procesos.procesos_clonar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Gestionar Procesos Clonar"
capacidad: "procesos.procesos_clonar.gestionar"
pantallas_principales: []
fragmentos: ["procesos.pantalla.procesos_select"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/procesos_clonar"]
estado_revision: "generado"
---

# Flujo - Gestionar Procesos Clonar

Propuesta generada automaticamente desde la capacidad `procesos.procesos_clonar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ProcesosClonar. Caso de uso: clona las tareas de un proceso de referencia al proceso indicado (borrando las existentes previamente). Devuelve '' si ha ido bien o un mensaje de error. El frontend se encarga de recargar la vista del proceso tras el clonado.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.procesos_select`

## Escenarios Inferidos

### Ejecutar

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

- `/src/procesos/procesos_clonar`

## Errores Conocidos

- ``no se ha indicado el proceso a clonar``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
