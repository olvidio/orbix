---
id: "procesos.procesos_get_listado.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Gestionar Procesos Get Listado"
capacidad: "procesos.procesos_get_listado.gestionar"
pantallas_principales: []
fragmentos: ["procesos.pantalla.procesos_get_listado"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/procesos_get_listado"]
estado_revision: "generado"
---

# Flujo - Gestionar Procesos Get Listado

Propuesta generada automaticamente desde la capacidad `procesos.procesos_get_listado.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ProcesosGetListado. Caso de uso: devuelve el listado (estructurado) de fases/tareas del proceso filtrando por sfsv/role. El render HTML se hace en el frontend.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.procesos_get_listado`

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
- `fnjs_eliminar`
- `fnjs_modificar`

## Endpoints Del Flujo

- `/src/procesos/procesos_get_listado`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
