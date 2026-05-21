---
id: "inventario.inventario_css_inline.gestionar"
tipo: "capacidad"
modulo: "inventario"
nombre: "Gestionar Inventario Css Inline"
entidades: ["InventarioCssInline"]
acciones: ["obtener_datos"]
endpoints: ["/src/inventario/inventario_css_inline_data"]
pantallas: ["frontend/inventario/controller/doc_imprimir_ctr.php", "frontend/inventario/controller/doc_imprimir_dlb.php"]
casos_uso: ["src\\inventario\\application\\InventarioCssInlineData"]
tags: ["css", "data", "inline", "inventario", "inventario_css_inline"]
estado_revision: "generado"
---

# Gestionar Inventario Css Inline

Propuesta generada automaticamente a partir de endpoints con prefijo comun `inventario_css_inline`.

## Objetivo Funcional

Gestiona InventarioCssInline. CSS embebido para impresión de inventario (inventario.css.php en disco).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/inventario/inventario_css_inline_data`

## Pantallas Relacionadas

- `frontend/inventario/controller/doc_imprimir_ctr.php`
- `frontend/inventario/controller/doc_imprimir_dlb.php`

## Casos De Uso Detectados

- `src\inventario\application\InventarioCssInlineData`

## Pistas Desde Endpoints

- CSS embebido para impresión de inventario (`inventario.css.php` en disco).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
