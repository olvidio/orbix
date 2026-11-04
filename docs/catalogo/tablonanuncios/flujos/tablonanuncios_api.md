---
id: "tablonanuncios.api_tablon.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "tablonanuncios"
nombre: "Flujo - Anuncios del tablón"
pantallas_principales: ["tablonanuncios.pantalla.api_tablon"]
endpoints: ["/src/tablonanuncios/tablon_anuncios_data"]
estado_revision: "revisado"
---

# Flujo - Tablon anuncios

## Objetivo De Usuario

Publicación y consulta de anuncios desde integraciones externas; no tiene pantalla propia.

## Endpoints

Ver `docs/catalogo/tablonanuncios/api/`.

## Punto De Entrada

Automático: lo usan otras aplicaciones.

## Escenarios Inferidos

### Anuncios del tablón

Pasos propuestos:
1. Los anuncios publicados desde otras aplicaciones aparecen en el tablón; no hay que hacer nada en Orbix.

Endpoints asociados:
- `/src/tablonanuncios/tablon_anuncios_data`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (API / portada)
- **Pills2:** sin entrada de menú en el índice (API / portada)
