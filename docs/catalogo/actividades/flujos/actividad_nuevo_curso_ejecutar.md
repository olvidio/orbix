---
id: "actividades.actividad_nuevo_curso_ejecutar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Generar las actividades de un nuevo curso"
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

Confirmar los años de origen y destino y lanzar la generación de las actividades del nuevo curso. Puede tardar varios minutos.

## Punto De Entrada

Formulario de **Generar nuevo curso** (menú Nuevo calendario > nuevo curso).

## Escenarios Inferidos

### Generar las actividades de un nuevo curso

Pasos propuestos:
1. Leer los avisos sobre lo que se borra y lo que se crea.
2. Elegir el **año de destino** y el **año de origen**.
3. Enviar el formulario y esperar a que termine.
4. Revisar el resumen o, si se ha pedido, el listado de actividades creadas.

Endpoints asociados:
- `/src/actividades/actividad_nuevo_curso_ejecutar`

## Endpoints Del Flujo

- `/src/actividades/actividad_nuevo_curso_ejecutar`

## Ruta de menú

- **Legacy:** dre > Nuevo calendario > nuevo curso.
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Generar nuevo curso.
