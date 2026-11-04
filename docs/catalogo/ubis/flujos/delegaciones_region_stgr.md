---
id: "ubis.delegaciones_region_stgr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Elegir región y delegación"
capacidad: "ubis.delegaciones_region_stgr.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/delegaciones_region_stgr_data"]
estado_revision: "revisado"
---

# Flujo - Delegaciones Region Stgr

## Objetivo De Usuario

Rellenar el desplegable de delegaciones cuando se elige una región del Studium.

## Punto De Entrada

Sin entrada de menú propia: se abre desde la ficha del centro o casa.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Elegir región y delegación

Pasos propuestos:
1. Elegir la región.
2. El desplegable de delegaciones se actualiza con las delegaciones de esa región; elegir una.

Endpoints asociados:
- `/src/ubis/delegaciones_region_stgr_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/ubis/delegaciones_region_stgr_data`

## Errores Conocidos

- `Se requiere region_stgr`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
