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
estado_revision: "revisado"
---

# Flujo - Asignar proceso a tipo de actividad

## Objetivo De Usuario

Asignar un tipo de proceso a un tipo de actividad, distinguiendo entre proceso propio (DL) o no propio.

## Punto De Entrada

Menú Legacy: sistema > procesos activ. > tipo activ - proceso. Pills2: ADMIN LOCAL > procesos activ. > tipo activ - proceso.

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

## Ruta de menú

- **Legacy:** sistema > procesos activ. > tipo activ - proceso
- **Pills2:** ADMIN LOCAL > procesos activ. > tipo activ - proceso
