---
id: "encargossacd.encargo_ver_editar.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Encargo Ver Editar"
entidades: ["EncargoVerEditar"]
acciones: ["ejecutar"]
endpoints: ["/src/encargossacd/encargo_ver_editar"]
pantallas: ["frontend/encargossacd/controller/encargo_ver.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoVerEditar"]
tags: ["editar", "encargo", "encargo_ver_editar", "encargossacd", "ver"]
estado_revision: "generado"
---

# Gestionar Encargo Ver Editar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `encargo_ver_editar`.

## Objetivo Funcional

Gestiona EncargoVerEditar. Actualización de encargo desde encargo_ver (antes encargo_ajax.php que=editar).

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/encargossacd/encargo_ver_editar`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/encargo_ver.php`

## Casos De Uso Detectados

- `src\encargossacd\application\EncargoVerEditar`

## Pistas Desde Endpoints

- Actualización de encargo desde `encargo_ver` (antes `encargo_ajax.php` que=editar).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
