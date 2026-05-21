---
id: "configuracion.modulos_select.gestionar"
tipo: "capacidad"
modulo: "configuracion"
nombre: "Gestionar Modulos Select"
entidades: ["ModulosSelect"]
acciones: ["obtener_datos"]
endpoints: ["/src/configuracion/modulos_select_data"]
pantallas: ["frontend/configuracion/controller/modulos_select.php"]
casos_uso: ["src\\configuracion\\application\\ModulosSelectData"]
tags: ["configuracion", "data", "modulos", "modulos_select", "select"]
estado_revision: "generado"
---

# Gestionar Modulos Select

Propuesta generada automaticamente a partir de endpoints con prefijo comun `modulos_select`.

## Objetivo Funcional

Gestiona ModulosSelect. JSON para {.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/configuracion/modulos_select_data`

## Pantallas Relacionadas

- `frontend/configuracion/controller/modulos_select.php`

## Casos De Uso Detectados

- `src\configuracion\application\ModulosSelectData`

## Pistas Desde Endpoints

- JSON para {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
