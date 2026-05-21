---
id: "procesos.tipo_activ_proceso.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Gestionar Tipo Activ Proceso"
capacidad: "procesos.tipo_activ_proceso.gestionar"
pantallas_principales: []
fragmentos: ["procesos.pantalla.tipo_activ_proceso_lista"]
acciones: ["listar"]
endpoints: ["/src/procesos/tipo_activ_proceso_lista"]
estado_revision: "generado"
---

# Flujo - Gestionar Tipo Activ Proceso

Propuesta generada automaticamente desde la capacidad `procesos.tipo_activ_proceso.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona TipoActivProcesoLista. Caso de uso: devuelve el listado estructurado de tipos de actividad con el proceso propio / no-propio asignado. El frontend renderiza la tabla con frontend\shared\web\Lista.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.tipo_activ_proceso_lista`

## Escenarios Inferidos

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Endpoints asociados:
- `/src/procesos/tipo_activ_proceso_lista`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- `fnjs_cambiar_proceso`

## Endpoints Del Flujo

- `/src/procesos/tipo_activ_proceso_lista`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
