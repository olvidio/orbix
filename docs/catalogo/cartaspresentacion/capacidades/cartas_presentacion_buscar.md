---
id: "cartaspresentacion.cartas_presentacion_buscar.gestionar"
tipo: "capacidad"
modulo: "cartaspresentacion"
nombre: "Gestionar Cartas Presentacion Buscar"
entidades: ["CartasPresentacionBuscarOpciones"]
acciones: ["obtener_datos"]
endpoints: ["/src/cartaspresentacion/cartas_presentacion_buscar_data"]
pantallas: ["frontend/cartaspresentacion/controller/cartas_presentacion_buscar.php"]
casos_uso: ["src\\cartaspresentacion\\application\\CartasPresentacionBuscarOpcionesData"]
tags: ["buscar", "cartas", "cartas_presentacion_buscar", "cartaspresentacion", "data", "presentacion"]
estado_revision: "generado"
---

# Gestionar Cartas Presentacion Buscar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `cartas_presentacion_buscar`.

## Objetivo Funcional

Gestiona CartasPresentacionBuscarOpciones. Opciones del formulario de busqueda de cartas de presentacion (region, pais, delegacion).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/cartaspresentacion/cartas_presentacion_buscar_data`

## Pantallas Relacionadas

- `frontend/cartaspresentacion/controller/cartas_presentacion_buscar.php`

## Casos De Uso Detectados

- `src\cartaspresentacion\application\CartasPresentacionBuscarOpcionesData`

## Pistas Desde Endpoints

- Endpoint backend: opciones del formulario de busqueda de cartas de presentacion (region, pais, delegacion).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
