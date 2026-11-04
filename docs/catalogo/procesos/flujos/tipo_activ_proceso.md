---
id: "procesos.tipo_activ_proceso.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Consultar los procesos de cada tipo de actividad"
capacidad: "procesos.tipo_activ_proceso.gestionar"
pantallas_principales: ["procesos.pantalla.tipo_activ_proceso"]
fragmentos: ["procesos.pantalla.tipo_activ_proceso_lista"]
acciones: ["listar"]
endpoints: ["/src/procesos/tipo_activ_proceso_lista"]
estado_revision: "revisado"
---

# Flujo - Tipo activ proceso

## Objetivo De Usuario

Ver los tipos de actividad con el proceso que tienen asignado.

## Punto De Entrada

Menú **sistema > procesos activ. > tipo activ - proceso**.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.tipo_activ_proceso_lista`

## Escenarios Inferidos

### Consultar los procesos de cada tipo de actividad

Pasos propuestos:
1. Entrar en **tipo activ - proceso**.
2. Se lista cada tipo de actividad con su proceso (propio y no propio); pulsar uno para cambiarlo.

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
