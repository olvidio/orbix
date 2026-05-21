---
id: "actividadescentro.activ_ctr_shell.gestionar"
tipo: "capacidad"
modulo: "actividadescentro"
nombre: "Gestionar Activ Ctr Shell"
entidades: ["ActivCtrShell"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadescentro/activ_ctr_shell_data"]
pantallas: ["frontend/actividadescentro/controller/activ_ctr.php"]
casos_uso: ["src\\actividadescentro\\application\\ActivCtrShellData"]
tags: ["activ", "activ_ctr_shell", "actividadescentro", "ctr", "data", "shell"]
estado_revision: "generado"
---

# Gestionar Activ Ctr Shell

Propuesta generada automaticamente a partir de endpoints con prefijo comun `activ_ctr_shell`.

## Objetivo Funcional

Gestiona ActivCtrShell. Tipo resuelto y especificaciones de URL para la shell de activ_ctr (sin HashFront en src/). La firma linkSinVal se aplica en {.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividadescentro/activ_ctr_shell_data`

## Pantallas Relacionadas

- `frontend/actividadescentro/controller/activ_ctr.php`

## Casos De Uso Detectados

- `src\actividadescentro\application\ActivCtrShellData`

## Pistas Desde Endpoints

- Tipo resuelto y especificaciones de URL para la shell de `activ_ctr` (sin `HashFront` en `src/`). La firma `linkSinVal` se aplica en {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
