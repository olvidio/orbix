---
id: "ubis.centros_opciones.gestionar"
tipo: "capacidad"
modulo: "ubis"
nombre: "Gestionar Centros Opciones"
entidades: ["CentrosOpciones"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/centros_opciones_data"]
pantallas: ["frontend/shared/web/CentrosQue.php"]
casos_uso: ["src\\ubis\\application\\CentrosOpcionesData"]
tags: ["centros", "centros_opciones", "data", "opciones", "ubis"]
estado_revision: "generado"
---

# Gestionar Centros Opciones

Propuesta generada automaticamente a partir de endpoints con prefijo comun `centros_opciones`.

## Objetivo Funcional

Gestiona CentrosOpciones. Devuelve el payload (solo datos) para poblar el <select> de centros en frontend\shared\web\CentrosQue. Sustituye el acceso directo desde CentrosQue al repositorio CentroDlRepositoryInterface (separación frontend ↔ backend, ver refactor.md).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/ubis/centros_opciones_data`

## Pantallas Relacionadas

- `frontend/shared/web/CentrosQue.php`

## Casos De Uso Detectados

- `src\ubis\application\CentrosOpcionesData`

## Pistas Desde Endpoints

- Devuelve el payload (solo datos) para poblar el <select> de centros en `frontend\shared\web\CentrosQue`. Sustituye el acceso directo desde `CentrosQue` al repositorio `CentroDlRepositoryInterface` (separación frontend ↔ backend, ver `refactor.md`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
