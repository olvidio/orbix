---
id: "menus.grupmenu_coleccion.gestionar"
tipo: "capacidad"
modulo: "menus"
nombre: "Gestionar Grupmenu Coleccion"
entidades: ["GrupMenuColeccionUseCase", "MenusVisiblesPorGrupoMenuUseCase"]
acciones: ["ejecutar"]
endpoints: ["/src/menus/grupmenu_coleccion"]
pantallas: []
casos_uso: ["src\\menus\\application\\GrupMenuColeccionUseCase", "src\\menus\\application\\MenusVisiblesPorGrupoMenuUseCase"]
tags: ["coleccion", "grupmenu", "grupmenu_coleccion", "menus"]
estado_revision: "generado"
---

# Gestionar Grupmenu Coleccion

Propuesta generada automaticamente a partir de endpoints con prefijo comun `grupmenu_coleccion`.

## Objetivo Funcional

Gestiona GrupMenuColeccionUseCase, MenusVisiblesPorGrupoMenuUseCase. Grupmenus visibles para el usuario actual, mismo criterio que el menú lateral en {.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/menus/grupmenu_coleccion`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\menus\application\GrupMenuColeccionUseCase`
- `src\menus\application\MenusVisiblesPorGrupoMenuUseCase`

## Pistas Desde Endpoints

- Grupmenus visibles para el usuario actual, mismo criterio que el menú lateral en {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
