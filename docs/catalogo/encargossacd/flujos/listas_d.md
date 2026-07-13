---
id: "encargossacd.listas_d.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Listas D"
capacidad: "encargossacd.listas_d.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.listas_d"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/listas_d_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Listas D

Propuesta generada automaticamente desde la capacidad `encargossacd.listas_d.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ListasD. Genera el listado "d" de atencion SACD (cr 9/20, 10). Sustituye la logica de frontend/encargossacd/controller/listas_d.php. La vista original devolvia dos tablas HTML sueltas (cabecera + listado); aqui se componen ambas en Html para que el frontend solo tenga que volcarlas al cliente.

## Punto De Entrada

Fragmento AJAX embebido; sin entrada de menú directa.


## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.listas_d`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.sf`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/encargossacd/listas_d_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

