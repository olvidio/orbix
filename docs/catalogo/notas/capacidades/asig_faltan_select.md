---
id: "notas.asig_faltan_select.gestionar"
tipo: "capacidad"
modulo: "notas"
nombre: "Gestionar Asig Faltan Select"
entidades: ["AsigFaltanSelectTabla"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/asig_faltan_select_data"]
pantallas: ["frontend/notas/controller/asig_faltan_select.php"]
casos_uso: ["src\\notas\\application\\AsigFaltanSelectTablaData"]
tags: ["asig", "asig_faltan_select", "data", "faltan", "notas", "select"]
estado_revision: "generado"
---

# Gestionar Asig Faltan Select

Propuesta generada automaticamente a partir de endpoints con prefijo comun `asig_faltan_select`.

## Objetivo Funcional

Gestiona AsigFaltanSelectTabla. Tabla de asig_faltan_select (asignaturas pendientes por persona).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/notas/asig_faltan_select_data`

## Pantallas Relacionadas

- `frontend/notas/controller/asig_faltan_select.php`

## Casos De Uso Detectados

- `src\notas\application\AsigFaltanSelectTablaData`

## Pistas Desde Endpoints

- Tabla de `asig_faltan_select` (asignaturas pendientes por persona).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
