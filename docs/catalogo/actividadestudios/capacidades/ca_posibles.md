---
id: "actividadestudios.ca_posibles.gestionar"
tipo: "capacidad"
modulo: "actividadestudios"
nombre: "Gestionar Ca Posibles"
entidades: ["CaPosibles"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/ca_posibles_data"]
pantallas: ["frontend/actividadestudios/controller/ca_posibles.php"]
casos_uso: ["src\\actividadestudios\\application\\CaPosiblesData"]
tags: ["actividadestudios", "ca", "ca_posibles", "data", "posibles"]
estado_revision: "generado"
---

# Gestionar Ca Posibles

Propuesta generada automaticamente a partir de endpoints con prefijo comun `ca_posibles`.

## Objetivo Funcional

Gestiona CaPosibles. Misma lógica que frontend/.../ca_posibles.php; respuesta serializable. En modo lista, pagina_link_spec lo firma el front ({.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividadestudios/ca_posibles_data`

## Pantallas Relacionadas

- `frontend/actividadestudios/controller/ca_posibles.php`

## Casos De Uso Detectados

- `src\actividadestudios\application\CaPosiblesData`

## Pistas Desde Endpoints

- Misma lógica que `frontend/.../ca_posibles.php`; respuesta serializable. En modo `lista`, `pagina_link_spec` lo firma el front ({

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
