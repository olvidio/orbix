---
id: "casas.casa_ingresos.gestionar"
tipo: "capacidad"
modulo: "casas"
nombre: "Gestionar Casa Ingresos"
entidades: ["CasaIngresos"]
acciones: ["listar"]
endpoints: ["/src/casas/casa_ingresos_lista_data"]
pantallas: ["frontend/casas/controller/casa_ingresos_lista.php"]
casos_uso: ["src\\casas\\application\\CasaIngresosListaData"]
tags: ["casa", "casa_ingresos", "casas", "data", "ingresos", "lista"]
estado_revision: "generado"
---

# Gestionar Casa Ingresos

Propuesta generada automaticamente a partir de endpoints con prefijo comun `casa_ingresos`.

## Objetivo Funcional

Gestiona CasaIngresos. Listado económico de actividades por casa (casa_ingresos_lista).

## Acciones Detectadas

- `listar`

## Endpoints

- `/src/casas/casa_ingresos_lista_data`

## Pantallas Relacionadas

- `frontend/casas/controller/casa_ingresos_lista.php`

## Casos De Uso Detectados

- `src\casas\application\CasaIngresosListaData`

## Pistas Desde Endpoints

- Endpoint backend: listado económico de actividades por casa (`casa_ingresos_lista`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
