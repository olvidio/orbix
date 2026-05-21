---
id: "misas.eliminar_encargo_zona.gestionar"
tipo: "capacidad"
modulo: "misas"
nombre: "Gestionar Eliminar Encargo Zona"
entidades: ["EliminarEncargoZona"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/eliminar_encargo_zona"]
pantallas: ["frontend/misas/controller/ver_encargos_zona.php"]
casos_uso: ["src\\misas\\application\\EliminarEncargoZona"]
tags: ["eliminar", "eliminar_encargo_zona", "encargo", "misas", "zona"]
estado_revision: "generado"
---

# Gestionar Eliminar Encargo Zona

Propuesta generada automaticamente a partir de endpoints con prefijo comun `eliminar_encargo_zona`.

## Objetivo Funcional

Gestiona EliminarEncargoZona. Elimina un Encargo por id. Devuelve texto vacio si todo fue bien, o el mensaje de error del repositorio en caso contrario.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/misas/eliminar_encargo_zona`

## Pantallas Relacionadas

- `frontend/misas/controller/ver_encargos_zona.php`

## Casos De Uso Detectados

- `src\misas\application\EliminarEncargoZona`

## Pistas Desde Endpoints

- Elimina un `Encargo` por id. Devuelve texto vacio si todo fue bien, o el mensaje de error del repositorio en caso contrario.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
