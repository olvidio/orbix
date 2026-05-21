---
id: "menus.grupmenu.gestionar"
tipo: "capacidad"
modulo: "menus"
nombre: "Gestionar Grupmenu"
entidades: ["GrupMenuListaUseCase"]
acciones: ["eliminar", "guardar", "listar"]
endpoints: ["/src/menus/grupmenu_eliminar", "/src/menus/grupmenu_guardar", "/src/menus/grupmenu_lista"]
pantallas: ["frontend/menus/controller/grupmenu_lista.php", "frontend/menus/controller/menus_get.php", "frontend/menus/controller/menus_que.php", "frontend/menus/view/grupmenu_lista.phtml"]
casos_uso: ["src\\menus\\application\\GrupMenuListaUseCase"]
tags: ["eliminar", "grupmenu", "guardar", "lista", "menus"]
estado_revision: "generado"
---

# Gestionar Grupmenu

Propuesta generada automaticamente a partir de endpoints con prefijo comun `grupmenu`.

## Objetivo Funcional

Gestiona GrupMenuListaUseCase. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `eliminar`
- `guardar`
- `listar`

## Endpoints

- `/src/menus/grupmenu_eliminar`
- `/src/menus/grupmenu_guardar`
- `/src/menus/grupmenu_lista`

## Pantallas Relacionadas

- `frontend/menus/controller/grupmenu_lista.php`
- `frontend/menus/controller/menus_get.php`
- `frontend/menus/controller/menus_que.php`
- `frontend/menus/view/grupmenu_lista.phtml`

## Casos De Uso Detectados

- `src\menus\application\GrupMenuListaUseCase`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
