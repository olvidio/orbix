---
id: "ubis.calendario_periodos.gestionar"
tipo: "capacidad"
modulo: "ubis"
nombre: "Gestionar Calendario Periodos"
entidades: ["CalendarioPeriodo"]
acciones: ["eliminar", "guardar"]
endpoints: ["/src/ubis/calendario_periodos_eliminar", "/src/ubis/calendario_periodos_guardar"]
pantallas: ["frontend/ubis/controller/calendario_periodos.php"]
casos_uso: ["src\\ubis\\application\\CalendarioPeriodoEliminar", "src\\ubis\\application\\CalendarioPeriodoGuardar"]
tags: ["calendario", "calendario_periodos", "eliminar", "guardar", "periodos", "ubis"]
estado_revision: "generado"
---

# Gestionar Calendario Periodos

Propuesta generada automaticamente a partir de endpoints con prefijo comun `calendario_periodos`.

## Objetivo Funcional

Gestiona CalendarioPeriodo. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `eliminar`
- `guardar`

## Endpoints

- `/src/ubis/calendario_periodos_eliminar`
- `/src/ubis/calendario_periodos_guardar`

## Pantallas Relacionadas

- `frontend/ubis/controller/calendario_periodos.php`

## Casos De Uso Detectados

- `src\ubis\application\CalendarioPeriodoEliminar`
- `src\ubis\application\CalendarioPeriodoGuardar`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

- `hay un error, no se ha eliminado`
- `hay un error, no se ha guardado`
- `no se encuentra el periodo a borrar`
- `no sé cuál he de borar`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
