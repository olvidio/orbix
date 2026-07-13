---
id: "actividades.actividad_nuevo_curso_ejecutar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Ejecutar generación nuevo curso"
capacidad: "actividades.actividad_nuevo_curso_ejecutar.gestionar"
pantallas_principales: ["actividades.pantalla.actividad_nuevo_curso"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/actividades/actividad_nuevo_curso_ejecutar"]
estado_revision: "revisado"
---

# Flujo - Ejecutar generación nuevo curso

Proceso batch: borra actividades en proyecto del curso destino, copia desde año referencia,
opcionalmente centros encargados y fases.

## Objetivo De Usuario

Confirmar años en `actividad_nuevo_curso` y lanzar la generación (puede tardar varios minutos).

## Punto De Entrada

Formulario `actividad_nuevo_curso.phtml` → POST directo al endpoint (no pasa por controller
frontend intermedio).

## Escenarios

### Ejecutar

1. Leer avisos de borrado/creación.
2. Elegir `year` (destino) y `year_ref` (origen).
3. Enviar formulario; esperar respuesta.
4. Revisar resumen o listado si `ver_lista`.

## Endpoints Del Flujo

- `/src/actividades/actividad_nuevo_curso_ejecutar`

## Ruta de menú

- **Legacy:** dre > Nuevo calendario > nuevo curso.
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Generar nuevo curso.
