---
id: "misas.eliminar_encargo_centro.gestionar"
tipo: "capacidad"
modulo: "misas"
nombre: "Gestionar Eliminar Encargo Centro"
entidades: ["EliminarEncargoCentro"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/eliminar_encargo_centro"]
pantallas: ["frontend/misas/controller/ver_encargos_centros.php"]
casos_uso: ["src\\misas\\application\\EliminarEncargoCentro"]
tags: ["centro", "eliminar", "eliminar_encargo_centro", "encargo", "misas"]
estado_revision: "generado"
---

# Gestionar Eliminar Encargo Centro

Propuesta generada automaticamente a partir de endpoints con prefijo comun `eliminar_encargo_centro`.

## Objetivo Funcional

Gestiona EliminarEncargoCentro. Elimina un EncargoCtr por su uuid. Devuelve texto vacio si todo fue bien, o el mensaje de error del repositorio en caso contrario.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/misas/eliminar_encargo_centro`

## Pantallas Relacionadas

- `frontend/misas/controller/ver_encargos_centros.php`

## Casos De Uso Detectados

- `src\misas\application\EliminarEncargoCentro`

## Pistas Desde Endpoints

- Elimina un `EncargoCtr` por su uuid. Devuelve texto vacio si todo fue bien, o el mensaje de error del repositorio en caso contrario.

## Errores Conocidos

- `Falta el identificador del encargo-centro a eliminar`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
