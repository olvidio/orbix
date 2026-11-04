---
id: "inventario.inventario_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Imprimir el inventario de centros"
capacidad: "inventario.inventario_ctr.gestionar"
pantallas_principales: ["inventario.pantalla.doc_imprimir_ctr"]
fragmentos: ["inventario.pantalla.doc_imprimir_ctr"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/inventario_ctr"]
estado_revision: "revisado"
---

# Flujo - Gestionar Inventario Ctr

## Objetivo De Usuario

Obtener el inventario impreso de los documentos de uno o varios centros, listo para firmar.

## Punto De Entrada

Desde **de centros o dlb > inventario de centros**, tras marcar los centros y pulsar **Imprimir ctr** o **Imprimir dl**.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.doc_imprimir_ctr`

## Escenarios Inferidos

### Imprimir el inventario de centros

Pasos propuestos:
1. Marcar los centros en **inventario de centros**.
2. Pulsar **Imprimir ctr** (o **Imprimir dl** para añadir la columna **observaciones dl**).
3. Se muestra una hoja por centro titulada **Inventario de documentos de** (nombre del centro), con **documento** y **observaciones**.
4. Imprimir desde el navegador. Cada hoja termina con la nota «(*) Se guarda bajo llave», el total de páginas y el espacio **Conforme (firma)**.

Endpoints asociados:
- `/src/inventario/inventario_ctr`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.dl`
- `post.sel`

Acciones JavaScript:
- `fnjs_ver_equipaje`

## Endpoints Del Flujo

- `/src/inventario/inventario_ctr`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** scdl > Inventario > inventarios > de centros o dlb
- **Pills2:** —
