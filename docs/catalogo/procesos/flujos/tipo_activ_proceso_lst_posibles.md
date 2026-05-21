---
id: "procesos.tipo_activ_proceso_lst_posibles.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Gestionar Tipo Activ Proceso Lst Posibles"
capacidad: "procesos.tipo_activ_proceso_lst_posibles.gestionar"
pantallas_principales: ["procesos.pantalla.tipo_activ_proceso"]
fragmentos: ["procesos.pantalla.tipo_activ_proceso_lst_posibles"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/tipo_activ_proceso_lst_posibles"]
estado_revision: "generado"
---

# Flujo - Gestionar Tipo Activ Proceso Lst Posibles

Propuesta generada automaticamente desde la capacidad `procesos.tipo_activ_proceso_lst_posibles.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona TipoActivProcesoLstPosibles. Caso de uso: devuelve la lista de procesos posibles que el usuario puede asignar a un id_tipo_activ concreto, como estructura. El frontend se encarga de la mini-tabla HTML clickable.

## Punto De Entrada

- `procesos.pantalla.tipo_activ_proceso`

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.tipo_activ_proceso_lst_posibles`

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
- `fnjs_asignar_proceso`

## Endpoints Del Flujo

- `/src/procesos/tipo_activ_proceso_lst_posibles`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
