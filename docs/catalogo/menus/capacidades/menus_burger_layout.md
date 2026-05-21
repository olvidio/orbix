---
id: "menus.menus_burger_layout.gestionar"
tipo: "capacidad"
modulo: "menus"
nombre: "Gestionar Menus Burger Layout"
entidades: ["MenusBurgerLayoutDataUseCase"]
acciones: ["obtener_datos"]
endpoints: ["/src/menus/menus_burger_layout_data"]
pantallas: ["frontend/shared/layouts/BurgerLayout.php", "frontend/shared/layouts/Pills2Layout.php", "frontend/shared/layouts/PillsLayout.php"]
casos_uso: ["src\\menus\\application\\MenusBurgerLayoutDataUseCase"]
tags: ["burger", "data", "layout", "menus", "menus_burger_layout"]
estado_revision: "generado"
---

# Gestionar Menus Burger Layout

Propuesta generada automaticamente a partir de endpoints con prefijo comun `menus_burger_layout`.

## Objetivo Funcional

Gestiona MenusBurgerLayoutDataUseCase. Datos para {.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/menus/menus_burger_layout_data`

## Pantallas Relacionadas

- `frontend/shared/layouts/BurgerLayout.php`
- `frontend/shared/layouts/Pills2Layout.php`
- `frontend/shared/layouts/PillsLayout.php`

## Casos De Uso Detectados

- `src\menus\application\MenusBurgerLayoutDataUseCase`

## Pistas Desde Endpoints

- Datos para {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
