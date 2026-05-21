---
id: "encargossacd.comprobaciones_ctr.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Comprobaciones Ctr"
entidades: ["EncargoComprobacionesCtr"]
acciones: ["ejecutar"]
endpoints: ["/src/encargossacd/comprobaciones_ctr"]
pantallas: ["frontend/encargossacd/controller/comprobaciones.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoComprobacionesCtr"]
tags: ["comprobaciones", "comprobaciones_ctr", "ctr", "encargossacd"]
estado_revision: "generado"
---

# Gestionar Comprobaciones Ctr

Propuesta generada automaticamente a partir de endpoints con prefijo comun `comprobaciones_ctr`.

## Objetivo Funcional

Gestiona EncargoComprobacionesCtr. Elimina encargos ligados a centros inactivos y sacd huérfanos (misma lógica que el antiguo frontend/encargossacd/controller/comprobaciones.php).

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/encargossacd/comprobaciones_ctr`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/comprobaciones.php`

## Casos De Uso Detectados

- `src\encargossacd\application\EncargoComprobacionesCtr`

## Pistas Desde Endpoints

- Elimina encargos ligados a centros inactivos y sacd huérfanos (misma lógica que el antiguo `frontend/encargossacd/controller/comprobaciones.php`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
