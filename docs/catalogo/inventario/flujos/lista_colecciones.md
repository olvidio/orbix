---
id: "inventario.lista_colecciones.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Agrupar un listado por colección"
capacidad: "inventario.lista_colecciones.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_colecciones"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Colecciones

## Objetivo De Usuario

Cargar las colecciones de documentos para agrupar o filtrar un listado.

## Punto De Entrada

No se abre desde el menú: se usa dentro de los listados de documentos que permiten agrupar por colección.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Agrupar un listado por colección

Pasos propuestos:
1. En un listado de documentos con agrupación, abrir el desplegable de colección.
2. Las colecciones disponibles se cargan solas.
3. Elegir la colección para ver solo sus documentos.

Endpoints asociados:
- `/src/inventario/lista_colecciones`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/inventario/lista_colecciones`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
