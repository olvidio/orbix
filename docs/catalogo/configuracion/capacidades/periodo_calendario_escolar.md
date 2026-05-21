---
id: "configuracion.periodo_calendario_escolar.gestionar"
tipo: "capacidad"
modulo: "configuracion"
nombre: "Gestionar Periodo Calendario Escolar"
entidades: ["PeriodoCalendarioEscolar"]
acciones: ["obtener_datos"]
endpoints: ["/src/configuracion/periodo_calendario_escolar_data"]
pantallas: ["frontend/shared/web/Periodo.php"]
casos_uso: ["src\\configuracion\\application\\PeriodoCalendarioEscolarData"]
tags: ["calendario", "configuracion", "data", "escolar", "periodo", "periodo_calendario_escolar"]
estado_revision: "generado"
---

# Gestionar Periodo Calendario Escolar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `periodo_calendario_escolar`.

## Objetivo Funcional

Gestiona PeriodoCalendarioEscolar. Fechas y metadatos del curso (STGR / CRT) que antes solo estaban en $_SESSION['oConfig'], para inyectar en Periodo del frontend.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/configuracion/periodo_calendario_escolar_data`

## Pantallas Relacionadas

- `frontend/shared/web/Periodo.php`

## Casos De Uso Detectados

- `src\configuracion\application\PeriodoCalendarioEscolarData`

## Pistas Desde Endpoints

- Fechas y metadatos del curso (STGR / CRT) que antes solo estaban en `$_SESSION['oConfig']`, para inyectar en `Periodo` del frontend.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
