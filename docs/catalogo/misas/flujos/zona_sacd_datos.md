---
id: "misas.zona_sacd_datos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Zona Sacd Datos"
capacidad: "misas.zona_sacd_datos.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["obtener"]
endpoints: ["/src/misas/zona_sacd_datos_get"]
estado_revision: "revisado"
---

# Flujo - Zona sacd datos

## Objetivo De Usuario

Lee datos de disponibilidad semanal (propia, dw1-dw7) de un SACD en una zona para el modal zona_sacd.

## Punto De Entrada

Sin entrada de menú directa; fragmento o modal invocado desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Obtener

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

- `/src/misas/zona_sacd_datos_get`

## Errores Conocidos

- `No existe`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
