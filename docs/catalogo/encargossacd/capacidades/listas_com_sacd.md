---
id: "encargossacd.listas_com_sacd.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Listas Com Sacd"
entidades: ["ListasComSacd"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/listas_com_sacd_data"]
pantallas: ["frontend/encargossacd/controller/listas_com_sacd.php"]
casos_uso: ["src\\encargossacd\\application\\ListasComSacdData"]
tags: ["com", "data", "encargossacd", "listas", "listas_com_sacd", "sacd"]
estado_revision: "generado"
---

# Gestionar Listas Com Sacd

Propuesta generada automaticamente a partir de endpoints con prefijo comun `listas_com_sacd`.

## Objetivo Funcional

Gestiona ListasComSacd. Datos para la comunicacion a los SACD. Sustituye la logica de frontend/encargossacd/controller/listas_com_sacd.php.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/encargossacd/listas_com_sacd_data`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/listas_com_sacd.php`

## Casos De Uso Detectados

- `src\encargossacd\application\ListasComSacdData`

## Pistas Desde Endpoints

- Datos para la comunicacion a los SACD. Sustituye la logica de `frontend/encargossacd/controller/listas_com_sacd.php`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
