---
id: "procesos.procesos_clonar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Clonar un proceso"
capacidad: "procesos.procesos_clonar.gestionar"
pantallas_principales: ["procesos.pantalla.procesos_select"]
fragmentos: ["procesos.pantalla.procesos_select"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/procesos_clonar"]
estado_revision: "revisado"
---

# Flujo - Clonar proceso

## Objetivo De Usuario

Copiar las tareas de un proceso de referencia sobre el proceso elegido, sustituyendo las que tenía.

## Punto De Entrada

Menú **sistema > procesos activ. > procesos** (Pills2: ADMIN LOCAL > procesos activ. > procesos).

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.procesos_select`

## Escenarios Inferidos

### Clonar un proceso

Pasos propuestos:
1. En **procesos**, elegir en **Proceso** el que se quiere rellenar.
2. En **clonar de** elegir el proceso de referencia.
3. Pulsar **clonar**. Atención: las tareas existentes del proceso destino se sustituyen por las del de referencia.

Endpoints asociados:
- `/src/procesos/procesos_clonar`

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

## Ruta de menú

- **Legacy:** sistema > procesos activ. > procesos
- **Pills2:** ADMIN LOCAL > procesos activ. > procesos
