---
id: "encargossacd.listas_cl.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Listas Cl"
entidades: ["ListasCl"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/listas_cl_data"]
pantallas: ["frontend/encargossacd/controller/listas_cl.php"]
casos_uso: ["src\\encargossacd\\application\\ListasClData"]
tags: ["cl", "data", "encargossacd", "listas", "listas_cl"]
estado_revision: "generado"
---

# Gestionar Listas Cl

Propuesta generada automaticamente a partir de endpoints con prefijo comun `listas_cl`.

## Objetivo Funcional

Gestiona ListasCl. Listado de cl para cr, restringido a los centros de la sss+. Sustituye la logica de frontend/encargossacd/controller/listas_cl.php (era una plantilla con SQL crudo). Devuelve el HTML completo listo para volcarlo al cliente; el frontend se limita a pasar sf y a echo del resultado.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/encargossacd/listas_cl_data`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/listas_cl.php`

## Casos De Uso Detectados

- `src\encargossacd\application\ListasClData`

## Pistas Desde Endpoints

- Listado de cl para cr, restringido a los centros de la sss+. Sustituye la logica de `frontend/encargossacd/controller/listas_cl.php` (era una plantilla con SQL crudo). Devuelve el HTML completo listo para volcarlo al cliente; el frontend se limita a pasar `sf` y a echo del resultado.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
