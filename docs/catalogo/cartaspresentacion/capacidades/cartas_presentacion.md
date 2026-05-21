---
id: "cartaspresentacion.cartas_presentacion.gestionar"
tipo: "capacidad"
modulo: "cartaspresentacion"
nombre: "Gestionar Cartas Presentacion"
entidades: ["CartasPresentacion"]
acciones: ["listar"]
endpoints: ["/src/cartaspresentacion/cartas_presentacion_lista_data"]
pantallas: ["frontend/cartaspresentacion/controller/cartas_presentacion_lista.php"]
casos_uso: ["src\\cartaspresentacion\\application\\CartasPresentacionListaData"]
tags: ["cartas", "cartas_presentacion", "cartaspresentacion", "data", "lista", "presentacion"]
estado_revision: "generado"
---

# Gestionar Cartas Presentacion

Propuesta generada automaticamente a partir de endpoints con prefijo comun `cartas_presentacion`.

## Objetivo Funcional

Gestiona CartasPresentacion. Listado agrupado de cartas de presentacion (modo lista_dl, lista_todo o get con filtros).

## Acciones Detectadas

- `listar`

## Endpoints

- `/src/cartaspresentacion/cartas_presentacion_lista_data`

## Pantallas Relacionadas

- `frontend/cartaspresentacion/controller/cartas_presentacion_lista.php`

## Casos De Uso Detectados

- `src\cartaspresentacion\application\CartasPresentacionListaData`

## Pistas Desde Endpoints

- Endpoint backend: listado agrupado de cartas de presentacion (modo `lista_dl`, `lista_todo` o `get` con filtros).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
