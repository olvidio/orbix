---
id: "menus.menus_get_page.gestionar"
tipo: "capacidad"
modulo: "menus"
nombre: "Gestionar Menus Get Page"
entidades: ["MenusGetPage"]
acciones: ["obtener_datos"]
endpoints: ["/src/menus/menus_get_page_data"]
pantallas: ["frontend/menus/controller/menus_get.php"]
casos_uso: ["src\\menus\\application\\MenusGetPageData"]
tags: ["data", "get", "menus", "menus_get_page", "page"]
estado_revision: "generado"
---

# Gestionar Menus Get Page

Propuesta generada automaticamente a partir de endpoints con prefijo comun `menus_get_page`.

## Objetivo Funcional

Gestiona MenusGetPage. Datos para frontend/menus/controller/menus_get.php (formulario o listado).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/menus/menus_get_page_data`

## Pantallas Relacionadas

- `frontend/menus/controller/menus_get.php`

## Casos De Uso Detectados

- `src\menus\application\MenusGetPageData`

## Pistas Desde Endpoints

- Datos para `frontend/menus/controller/menus_get.php` (formulario o listado).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
