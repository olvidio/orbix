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
estado_revision: "revisado"
---

# Flujo - Tipo activ proceso

## Objetivo De Usuario

Listado de tipos de actividad con el proceso asignado (propio y no propio) para su gestión desde la pantalla de asignación.

## Punto De Entrada

Menú Legacy: sistema > procesos activ. > tipo activ - proceso. Pills2: ADMIN LOCAL > procesos activ. > tipo activ - proceso.

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

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sistema > procesos activ. > tipo activ - proceso
- **Pills2:** ADMIN LOCAL > procesos activ. > tipo activ - proceso
