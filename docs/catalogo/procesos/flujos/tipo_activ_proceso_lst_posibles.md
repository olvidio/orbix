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
estado_revision: "revisado"
---

# Flujo - Procesos posibles para tipo de actividad

## Objetivo De Usuario

Obtener y mostrar la mini-tabla de procesos que el usuario puede asignar a un tipo de actividad concreto (propio o no propio).

## Punto De Entrada

Menú Legacy: sistema > procesos activ. > tipo activ - proceso. Pills2: ADMIN LOCAL > procesos activ. > tipo activ - proceso.

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

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sistema > procesos activ. > tipo activ - proceso
- **Pills2:** ADMIN LOCAL > procesos activ. > tipo activ - proceso
