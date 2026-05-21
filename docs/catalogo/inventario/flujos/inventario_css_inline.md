---
id: "inventario.inventario_css_inline.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Gestionar Inventario Css Inline"
capacidad: "inventario.inventario_css_inline.gestionar"
pantallas_principales: []
fragmentos: ["inventario.pantalla.doc_imprimir_ctr", "inventario.pantalla.doc_imprimir_dlb"]
acciones: ["obtener_datos"]
endpoints: ["/src/inventario/inventario_css_inline_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Inventario Css Inline

Propuesta generada automaticamente desde la capacidad `inventario.inventario_css_inline.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona InventarioCssInline. CSS embebido para impresión de inventario (inventario.css.php en disco).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.doc_imprimir_ctr`
- `inventario.pantalla.doc_imprimir_dlb`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
