---
id: "ubis.casas_opciones.gestionar"
tipo: "capacidad"
modulo: "ubis"
nombre: "Gestionar Casas Opciones"
entidades: ["CasasOpciones"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/casas_opciones_data"]
pantallas: ["frontend/casas/controller/calendario_ubi_resumen.php", "frontend/shared/web/CasasQue.php"]
casos_uso: ["src\\ubis\\application\\CasasOpcionesData"]
tags: ["casas", "casas_opciones", "data", "opciones", "ubis"]
estado_revision: "generado"
---

# Gestionar Casas Opciones

Propuesta generada automaticamente a partir de endpoints con prefijo comun `casas_opciones`.

## Objetivo Funcional

Gestiona CasasOpciones. Devuelve el payload (solo datos) para poblar el <select> de casas en frontend\shared\web\CasasQue. La vista/componente frontend es quien construye el HTML del desplegable; aquí solo se exponen las opciones. Sustituye el acceso directo desde CasasQue al repositorio CasaDlRepositoryInterface (separación frontend ↔ backend, ver refactor.md).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/ubis/casas_opciones_data`

## Pantallas Relacionadas

- `frontend/casas/controller/calendario_ubi_resumen.php`
- `frontend/shared/web/CasasQue.php`

## Casos De Uso Detectados

- `src\ubis\application\CasasOpcionesData`

## Pistas Desde Endpoints

- Devuelve el payload (solo datos) para poblar el <select> de casas en `frontend\shared\web\CasasQue`. La vista/componente frontend es quien construye el HTML del desplegable; aquí solo se exponen las opciones. Sustituye el acceso directo desde `CasasQue` al repositorio `CasaDlRepositoryInterface` (separación frontend ↔ backend, ver `refactor.md`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
