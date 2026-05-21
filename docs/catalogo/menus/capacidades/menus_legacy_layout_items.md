---
id: "menus.menus_legacy_layout_items.gestionar"
tipo: "capacidad"
modulo: "menus"
nombre: "Gestionar Menus Legacy Layout Items"
entidades: ["MenusLegacyLayoutItemsUseCase"]
acciones: ["obtener_datos"]
endpoints: ["/src/menus/menus_legacy_layout_items_data"]
pantallas: ["frontend/shared/layouts/LegacyLayout.php"]
casos_uso: ["src\\menus\\application\\MenusLegacyLayoutItemsUseCase"]
tags: ["data", "items", "layout", "legacy", "menus", "menus_legacy_layout_items"]
estado_revision: "generado"
---

# Gestionar Menus Legacy Layout Items

Propuesta generada automaticamente a partir de endpoints con prefijo comun `menus_legacy_layout_items`.

## Objetivo Funcional

Gestiona MenusLegacyLayoutItemsUseCase. Entradas de menú para el layout legacy (grupos 1 y el seleccionado, mismo filtro que el antiguo {.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/menus/menus_legacy_layout_items_data`

## Pantallas Relacionadas

- `frontend/shared/layouts/LegacyLayout.php`

## Casos De Uso Detectados

- `src\menus\application\MenusLegacyLayoutItemsUseCase`

## Pistas Desde Endpoints

- Entradas de menú para el layout legacy (grupos 1 y el seleccionado, mismo filtro que el antiguo {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
