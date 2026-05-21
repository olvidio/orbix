---
id: "casas.prevision_asistentes.gestionar"
tipo: "capacidad"
modulo: "casas"
nombre: "Gestionar Prevision Asistentes"
entidades: ["PrevisionAsistentes"]
acciones: ["obtener_datos"]
endpoints: ["/src/casas/prevision_asistentes_data"]
pantallas: ["frontend/casas/controller/prevision_asistentes.php"]
casos_uso: ["src\\casas\\application\\PrevisionAsistentesData"]
tags: ["asistentes", "casas", "data", "prevision", "prevision_asistentes"]
estado_revision: "generado"
---

# Gestionar Prevision Asistentes

Propuesta generada automaticamente a partir de endpoints con prefijo comun `prevision_asistentes`.

## Objetivo Funcional

Gestiona PrevisionAsistentes. Datos de la pantalla prevision_asistentes.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/casas/prevision_asistentes_data`

## Pantallas Relacionadas

- `frontend/casas/controller/prevision_asistentes.php`

## Casos De Uso Detectados

- `src\casas\application\PrevisionAsistentesData`

## Pistas Desde Endpoints

- Endpoint backend: datos de la pantalla `prevision_asistentes`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
