---
id: "encargossacd.sacd_ausencias.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Guardar las ausencias de un sacerdote"
capacidad: "encargossacd.sacd_ausencias.gestionar"
pantallas_principales: ["encargossacd.pantalla.sacd_ausencias"]
fragmentos: ["encargossacd.pantalla.sacd_ausencias_update"]
acciones: ["crear_actualizar"]
endpoints: ["/src/encargossacd/sacd_ausencias_update"]
estado_revision: "revisado"
---

# Flujo - Gestionar Sacd Ausencias

Propuesta generada automaticamente desde la capacidad `encargossacd.sacd_ausencias.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Guardar o modificar las ausencias y otras tareas de un sacerdote.

## Punto De Entrada

Menú **dre > ausencias > sacd**.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.sacd_ausencias_update`

## Escenarios Inferidos

### Guardar las ausencias de un sacerdote

Pasos propuestos:
1. Elegir el tipo y el sacd.
2. Rellenar fechas de inicio y fin y el tipo de ausencia; para borrar una, dejar las dos fechas en blanco.
3. Pulsar **guardar**.

Endpoints asociados:
- `/src/encargossacd/sacd_ausencias_update`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/encargossacd/sacd_ausencias_update`

## Errores Conocidos

- ``no se ha encontrado el encargo del sacd``

## Ruta de menú

- **Legacy:** dre > ausencias > sacd
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** dre > ausencias > sacd
- **Pills2:** sin entrada de menú en el índice

