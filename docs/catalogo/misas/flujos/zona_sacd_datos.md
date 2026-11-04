---
id: "misas.zona_sacd_datos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Consultar la disponibilidad semanal de un sacerdote"
capacidad: "misas.zona_sacd_datos.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["obtener"]
endpoints: ["/src/misas/zona_sacd_datos_get"]
estado_revision: "revisado"
---

# Flujo - Zona sacd datos

## Objetivo De Usuario

Ver los días de la semana en que un sacerdote está disponible en una zona.

## Punto De Entrada

Ventana de disponibilidad del sacerdote en la zona.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Consultar la disponibilidad semanal de un sacerdote

Pasos propuestos:
1. Abrir la ventana de disponibilidad del sacerdote en la zona.
2. Se muestran marcados los días de la semana disponibles.

Endpoints asociados:
- `/src/misas/zona_sacd_datos_get`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/misas/zona_sacd_datos_get`

## Errores Conocidos

- `No existe`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
