---
id: "notas.asignaturas_pendientes_resumen.gestionar"
tipo: "capacidad"
modulo: "notas"
nombre: "Gestionar Asignaturas Pendientes Resumen"
entidades: ["AsignaturasPendientesResumen"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/asignaturas_pendientes_resumen_data"]
pantallas: ["frontend/notas/controller/asignaturas_pendientes_resumen.php"]
casos_uso: ["src\\notas\\application\\AsignaturasPendientesResumenData"]
tags: ["asignaturas", "asignaturas_pendientes_resumen", "data", "notas", "pendientes", "resumen"]
estado_revision: "generado"
---

# Gestionar Asignaturas Pendientes Resumen

Propuesta generada automaticamente a partir de endpoints con prefijo comun `asignaturas_pendientes_resumen`.

## Objetivo Funcional

Gestiona AsignaturasPendientesResumen. Resumen: número de alumnos con cada asignatura pendiente, desglosado por tramo (nb, nc1, nc2, n total, ab, ac1, ac2, a total). Sucesor de la lógica embebida en frontend/notas/controller/asignaturas_pendientes_resumen.php.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/notas/asignaturas_pendientes_resumen_data`

## Pantallas Relacionadas

- `frontend/notas/controller/asignaturas_pendientes_resumen.php`

## Casos De Uso Detectados

- `src\notas\application\AsignaturasPendientesResumenData`

## Pistas Desde Endpoints

- Resumen: número de alumnos con cada asignatura pendiente, desglosado por tramo (nb, nc1, nc2, n total, ab, ac1, ac2, a total). Sucesor de la lógica embebida en `frontend/notas/controller/asignaturas_pendientes_resumen.php`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
