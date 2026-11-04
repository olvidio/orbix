---
id: "inventario.lista_docs_en_busqueda.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Consultar los documentos pendientes"
capacidad: "inventario.lista_docs_en_busqueda.gestionar"
pantallas_principales: ["inventario.pantalla.docs_en_busqueda"]
fragmentos: ["inventario.pantalla.docs_en_busqueda"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_docs_en_busqueda"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Docs En Busqueda

## Objetivo De Usuario

Consultar los documentos que están pendientes de localizar (en búsqueda).

## Punto De Entrada

Menú **Inventario > inventarios > lista docs pendientes**.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.docs_en_busqueda`

## Escenarios Inferidos

### Consultar los documentos pendientes

Pasos propuestos:
1. Abrir **lista docs pendientes**.
2. Leer el listado: **centro - lugar**, **documento** y **núm. reg.**.
3. Usar la lista para saber qué hay que localizar en cada centro.

Endpoints asociados:
- `/src/inventario/lista_docs_en_busqueda`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- `fnjs_left_side_hide`

## Endpoints Del Flujo

- `/src/inventario/lista_docs_en_busqueda`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** scdl > Inventario > inventarios > lista docs pendientes
- **Pills2:** sin entrada de menú en el índice
