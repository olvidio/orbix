---
id: "inventario.lista_docs_con_observaciones.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Consultar los documentos con observaciones"
capacidad: "inventario.lista_docs_con_observaciones.gestionar"
pantallas_principales: ["inventario.pantalla.docs_con_observaciones"]
fragmentos: ["inventario.pantalla.docs_con_observaciones"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_docs_con_observaciones"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Docs Con Observaciones

## Objetivo De Usuario

Consultar los documentos que tienen alguna observación anotada.

## Punto De Entrada

Menú **Inventario > inventarios > lista docs con observ.**.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.docs_con_observaciones`

## Escenarios Inferidos

### Consultar los documentos con observaciones

Pasos propuestos:
1. Abrir **lista docs con observ.**.
2. Leer el listado: **centro - lugar**, **documento**, **número**, **observaciones** y **observaciones ctr**.

Endpoints asociados:
- `/src/inventario/lista_docs_con_observaciones`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/inventario/lista_docs_con_observaciones`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** scdl > Inventario > inventarios > lista docs con observ.
- **Pills2:** sin entrada de menú en el índice
