---
id: "casas.calendario_ubi_resumen.gestionar"
tipo: "capacidad"
modulo: "casas"
nombre: "Gestionar Calendario Ubi Resumen"
entidades: ["CalendarioUbiResumen"]
acciones: ["obtener_datos"]
endpoints: ["/src/casas/calendario_ubi_resumen_data"]
pantallas: ["frontend/casas/controller/calendario_ubi_resumen.php", "frontend/casas/controller/calendario_ubi_resumen_body.php"]
casos_uso: ["src\\casas\\application\\CalendarioUbiResumenData"]
tags: ["calendario", "calendario_ubi_resumen", "casas", "data", "resumen", "ubi"]
estado_revision: "generado"
---

# Gestionar Calendario Ubi Resumen

Propuesta generada automaticamente a partir de endpoints con prefijo comun `calendario_ubi_resumen`.

## Objetivo Funcional

Gestiona CalendarioUbiResumen. Datos del estudio económico de una casa (calendario_ubi_resumen).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/casas/calendario_ubi_resumen_data`

## Pantallas Relacionadas

- `frontend/casas/controller/calendario_ubi_resumen.php`
- `frontend/casas/controller/calendario_ubi_resumen_body.php`

## Casos De Uso Detectados

- `src\casas\application\CalendarioUbiResumenData`

## Pistas Desde Endpoints

- Endpoint backend: datos del estudio económico de una casa (`calendario_ubi_resumen`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
