---
id: "cartaspresentacion.ubis.gestionar"
tipo: "capacidad"
modulo: "cartaspresentacion"
nombre: "Gestionar Ubis"
entidades: ["CartasPresentacionUbis"]
acciones: ["listar"]
endpoints: ["/src/cartaspresentacion/ubis_lista_data"]
pantallas: ["frontend/cartaspresentacion/controller/cartas_presentacion_ubis_lista.php"]
casos_uso: ["src\\cartaspresentacion\\application\\CartasPresentacionUbisListaData"]
tags: ["cartaspresentacion", "data", "lista", "ubis"]
estado_revision: "generado"
---

# Gestionar Ubis

Propuesta generada automaticamente a partir de endpoints con prefijo comun `ubis`.

## Objetivo Funcional

Gestiona CartasPresentacionUbis. Listado de centros con el estado de su carta de presentacion, en dos variantes (delegacion del usuario o centros extranjeros).

## Acciones Detectadas

- `listar`

## Endpoints

- `/src/cartaspresentacion/ubis_lista_data`

## Pantallas Relacionadas

- `frontend/cartaspresentacion/controller/cartas_presentacion_ubis_lista.php`

## Casos De Uso Detectados

- `src\cartaspresentacion\application\CartasPresentacionUbisListaData`

## Pistas Desde Endpoints

- Endpoint backend: listado de centros con el estado de su carta de presentacion, en dos variantes (delegacion del usuario o centros extranjeros).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
