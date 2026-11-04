---
id: "procesos.procesos_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Elegir el proceso que se quiere administrar"
capacidad: "procesos.procesos_select.gestionar"
pantallas_principales: ["procesos.pantalla.procesos_select"]
fragmentos: ["procesos.pantalla.procesos_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/procesos/procesos_select_data"]
estado_revision: "revisado"
---

# Flujo - Procesos select

## Objetivo De Usuario

Elegir el tipo de proceso de actividades que se quiere revisar o modificar, y clonar sus tareas desde otro.

## Punto De Entrada

Menú **sistema > procesos activ. > procesos** (Pills2: ADMIN LOCAL > procesos activ. > procesos).

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.procesos_select`

## Escenarios Inferidos

### Elegir el proceso que se quiere administrar

Pasos propuestos:
1. Entrar en **procesos**.
2. En **Proceso** elegir el tipo (actividad n, agd, sacd, sg, sr, estudios, general…; hay variantes «(no dl)»).
3. Pulsar **ver listado** para abrir sus fases y tareas.

Endpoints asociados:
- `/src/procesos/procesos_select_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.refresh`
- `post.stack`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/procesos/procesos_select_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sistema > procesos activ. > procesos
- **Pills2:** ADMIN LOCAL > procesos activ. > procesos
