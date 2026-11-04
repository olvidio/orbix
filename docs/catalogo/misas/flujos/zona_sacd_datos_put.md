---
id: "misas.zona_sacd_datos_put.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Cambiar la disponibilidad semanal de un sacerdote"
capacidad: "misas.zona_sacd_datos_put.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/misas/zona_sacd_datos_put"]
estado_revision: "revisado"
---

# Flujo - Zona sacd datos put

## Objetivo De Usuario

Guardar los días de la semana en que un sacerdote está disponible en una zona.

## Punto De Entrada

Ventana de disponibilidad del sacerdote en la zona.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Cambiar la disponibilidad semanal de un sacerdote

Pasos propuestos:
1. Marcar o desmarcar los días de la semana.
2. Guardar.

Endpoints asociados:
- `/src/misas/zona_sacd_datos_put`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/misas/zona_sacd_datos_put`

## Errores Conocidos

- `No existe`
- `<repositorio getErrorTxt()>`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
