---
id: "misas.guardar_encargo_centro.gestionar"
tipo: "capacidad"
modulo: "misas"
nombre: "Gestionar Guardar Encargo Centro"
entidades: ["GuardarEncargoCentro"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/guardar_encargo_centro"]
pantallas: ["frontend/misas/controller/ver_encargos_centros.php"]
casos_uso: ["src\\misas\\application\\GuardarEncargoCentro"]
tags: ["centro", "encargo", "guardar", "guardar_encargo_centro", "misas"]
estado_revision: "generado"
---

# Gestionar Guardar Encargo Centro

Propuesta generada automaticamente a partir de endpoints con prefijo comun `guardar_encargo_centro`.

## Objetivo Funcional

Gestiona GuardarEncargoCentro. Inserta o actualiza un EncargoCtr (relacion encargo ↔ centro). - Si id_item esta vacio se crea un nuevo EncargoCtr con uuid v4. - Si id_item es un uuid valido se carga el existente y se modifica. Devuelve texto vacio si todo fue bien, o el mensaje de error del repositorio en caso contrario.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/misas/guardar_encargo_centro`

## Pantallas Relacionadas

- `frontend/misas/controller/ver_encargos_centros.php`

## Casos De Uso Detectados

- `src\misas\application\GuardarEncargoCentro`

## Pistas Desde Endpoints

- Inserta o actualiza un `EncargoCtr` (relacion encargo ↔ centro). - Si `id_item` esta vacio se crea un nuevo `EncargoCtr` con uuid v4. - Si `id_item` es un uuid valido se carga el existente y se modifica. Devuelve texto vacio si todo fue bien, o el mensaje de error del repositorio en caso contrario.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
