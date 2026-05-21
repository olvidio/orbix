---
id: "cartaspresentacion.poblaciones.gestionar"
tipo: "capacidad"
modulo: "cartaspresentacion"
nombre: "Gestionar Poblaciones"
entidades: ["CartasPresentacionPoblaciones"]
acciones: ["obtener_datos"]
endpoints: ["/src/cartaspresentacion/poblaciones_data"]
pantallas: []
casos_uso: ["src\\cartaspresentacion\\application\\CartasPresentacionPoblacionesData"]
tags: ["cartaspresentacion", "data", "poblaciones"]
estado_revision: "generado"
---

# Gestionar Poblaciones

Propuesta generada automaticamente a partir de endpoints con prefijo comun `poblaciones`.

## Objetivo Funcional

Gestiona CartasPresentacionPoblaciones. Opciones del desplegable de poblaciones segun el filtro elegido (get_H, get_r, get_dl).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/cartaspresentacion/poblaciones_data`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\cartaspresentacion\application\CartasPresentacionPoblacionesData`

## Pistas Desde Endpoints

- Endpoint backend: opciones del desplegable de poblaciones segun el filtro elegido (`get_H`, `get_r`, `get_dl`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
