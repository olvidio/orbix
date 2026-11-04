---
id: "inventario.inventario_css_inline.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Qué hace al imprimir"
capacidad: "inventario.inventario_css_inline.gestionar"
pantallas_principales: ["inventario.pantalla.doc_imprimir_ctr", "inventario.pantalla.doc_imprimir_dlb"]
fragmentos: ["inventario.pantalla.doc_imprimir_ctr", "inventario.pantalla.doc_imprimir_dlb"]
acciones: ["obtener_datos"]
endpoints: ["/src/inventario/inventario_css_inline_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Inventario Css Inline

## Objetivo De Usuario

Dar formato de impresión (márgenes, cabecera y pie de página) a las hojas de inventario.

## Punto De Entrada

No se abre desde el menú: se aplica automáticamente al generar el inventario de centros o de dlb/casa.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.doc_imprimir_ctr`
- `inventario.pantalla.doc_imprimir_dlb`

## Escenarios Inferidos

### Qué hace al imprimir

Pasos propuestos:
1. Generar un inventario con **Imprimir ctr**, **Imprimir** o **Imprimir dl**.
2. El formato se aplica solo; no hay nada que configurar.
3. Si la hoja sale mal maquetada, avisar a mantenimiento.

Endpoints asociados:
- `/src/inventario/inventario_css_inline_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.dl`
- `post.sel`

Acciones JavaScript:
- `fnjs_ver_equipaje`

## Endpoints Del Flujo

- `/src/inventario/inventario_css_inline_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
