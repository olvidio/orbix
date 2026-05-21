---
id: "menus.menu.gestionar"
tipo: "capacidad"
modulo: "menus"
nombre: "Gestionar Menu"
entidades: ["Menu"]
acciones: ["copiar", "eliminar", "guardar"]
endpoints: ["/src/menus/menu_copiar", "/src/menus/menu_eliminar", "/src/menus/menu_guardar"]
pantallas: []
casos_uso: ["src\\menus\\application\\MenuCopiar", "src\\menus\\application\\MenuEliminar", "src\\menus\\application\\MenuGuardar"]
tags: ["copiar", "eliminar", "guardar", "menu", "menus"]
estado_revision: "generado"
---

# Gestionar Menu

Propuesta generada automaticamente a partir de endpoints con prefijo comun `menu`.

## Objetivo Funcional

Gestiona Menu. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `copiar`
- `eliminar`
- `guardar`

## Endpoints

- `/src/menus/menu_copiar`
- `/src/menus/menu_eliminar`
- `/src/menus/menu_guardar`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\menus\application\MenuCopiar`
- `src\menus\application\MenuEliminar`
- `src\menus\application\MenuGuardar`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
