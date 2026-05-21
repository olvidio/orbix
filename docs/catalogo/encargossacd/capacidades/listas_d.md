---
id: "encargossacd.listas_d.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Listas D"
entidades: ["ListasD"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/listas_d_data"]
pantallas: ["frontend/encargossacd/controller/listas_d.php"]
casos_uso: ["src\\encargossacd\\application\\ListasDData"]
tags: ["d", "data", "encargossacd", "listas", "listas_d"]
estado_revision: "generado"
---

# Gestionar Listas D

Propuesta generada automaticamente a partir de endpoints con prefijo comun `listas_d`.

## Objetivo Funcional

Gestiona ListasD. Genera el listado "d" de atencion SACD (cr 9/20, 10). Sustituye la logica de frontend/encargossacd/controller/listas_d.php. La vista original devolvia dos tablas HTML sueltas (cabecera + listado); aqui se componen ambas en Html para que el frontend solo tenga que volcarlas al cliente.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/encargossacd/listas_d_data`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/listas_d.php`

## Casos De Uso Detectados

- `src\encargossacd\application\ListasDData`

## Pistas Desde Endpoints

- Genera el listado "d" de atencion SACD (cr 9/20, 10). Sustituye la logica de `frontend/encargossacd/controller/listas_d.php`. La vista original devolvia dos tablas HTML sueltas (cabecera + listado); aqui se componen ambas en `Html` para que el frontend solo tenga que volcarlas al cliente.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
