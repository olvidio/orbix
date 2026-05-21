---
id: "notas.asignaturas_pendientes.gestionar"
tipo: "capacidad"
modulo: "notas"
nombre: "Gestionar Asignaturas Pendientes"
entidades: ["AsignaturasPendientes"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/asignaturas_pendientes_data"]
pantallas: ["frontend/notas/controller/asignaturas_pendientes.php"]
casos_uso: ["src\\notas\\application\\AsignaturasPendientesData"]
tags: ["asignaturas", "asignaturas_pendientes", "data", "notas", "pendientes"]
estado_revision: "generado"
---

# Gestionar Asignaturas Pendientes

Propuesta generada automaticamente a partir de endpoints con prefijo comun `asignaturas_pendientes`.

## Objetivo Funcional

Gestiona AsignaturasPendientes. Datos para la pantalla asignaturas_pendientes (matriz alumnos × asignaturas). La UI (Lista, desplegable rstgr) se monta en el controlador frontend.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/notas/asignaturas_pendientes_data`

## Pantallas Relacionadas

- `frontend/notas/controller/asignaturas_pendientes.php`

## Casos De Uso Detectados

- `src\notas\application\AsignaturasPendientesData`

## Pistas Desde Endpoints

- Datos para la pantalla `asignaturas_pendientes` (matriz alumnos × asignaturas). La UI (`Lista`, desplegable rstgr) se monta en el controlador frontend.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
