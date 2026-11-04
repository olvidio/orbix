---
id: "procesos.tipo_activ_proceso_asignar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Asignar un proceso a un tipo de actividad"
capacidad: "procesos.tipo_activ_proceso_asignar.gestionar"
pantallas_principales: ["procesos.pantalla.tipo_activ_proceso"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/procesos/tipo_activ_proceso_asignar"]
estado_revision: "revisado"
---

# Flujo - Asignar proceso a tipo de actividad

## Objetivo De Usuario

Asignar un tipo de proceso a un tipo de actividad (propio de la dl o no propio).

## Punto De Entrada

Menú **sistema > procesos activ. > tipo activ - proceso** (Pills2: ADMIN LOCAL > procesos activ. > tipo activ - proceso).

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Asignar un proceso a un tipo de actividad

Pasos propuestos:
1. Entrar en **tipo activ - proceso** y elegir el tipo de actividad.
2. Elegir el proceso de la lista de posibles.
3. Guardar. Las actividades nuevas de ese tipo usarán el proceso asignado.

Endpoints asociados:
- `/src/procesos/tipo_activ_proceso_asignar`

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
