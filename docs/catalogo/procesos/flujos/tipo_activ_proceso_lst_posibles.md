---
id: "procesos.tipo_activ_proceso_lst_posibles.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Ver los procesos asignables a un tipo de actividad"
capacidad: "procesos.tipo_activ_proceso_lst_posibles.gestionar"
pantallas_principales: ["procesos.pantalla.tipo_activ_proceso"]
fragmentos: ["procesos.pantalla.tipo_activ_proceso_lst_posibles"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/tipo_activ_proceso_lst_posibles"]
estado_revision: "revisado"
---

# Flujo - Procesos posibles para tipo de actividad

## Objetivo De Usuario

Mostrar los procesos que se pueden asignar a un tipo de actividad.

## Punto De Entrada

Menú **sistema > procesos activ. > tipo activ - proceso** (Pills2: ADMIN LOCAL > procesos activ. > tipo activ - proceso).

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.tipo_activ_proceso_lst_posibles`

## Escenarios Inferidos

### Ver los procesos asignables a un tipo de actividad

Pasos propuestos:
1. Entrar en **tipo activ - proceso**.
2. Elegir el tipo de actividad (propio de la dl o no propio).
3. Se muestra la mini-tabla de procesos posibles.

Endpoints asociados:
- `/src/procesos/tipo_activ_proceso_lst_posibles`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- `fnjs_asignar_proceso`

## Endpoints Del Flujo

- `/src/procesos/tipo_activ_proceso_lst_posibles`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sistema > procesos activ. > tipo activ - proceso
- **Pills2:** ADMIN LOCAL > procesos activ. > tipo activ - proceso
