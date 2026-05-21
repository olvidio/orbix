---
id: "encargossacd.ctr_get_ficha.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Ctr Get Ficha"
entidades: ["CtrGetFicha"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/ctr_get_ficha_data"]
pantallas: ["frontend/encargossacd/controller/ctr_get_ficha.php"]
casos_uso: ["src\\encargossacd\\application\\CtrGetFichaData"]
tags: ["ctr", "ctr_get_ficha", "data", "encargossacd", "ficha", "get"]
estado_revision: "generado"
---

# Gestionar Ctr Get Ficha

Propuesta generada automaticamente a partir de endpoints con prefijo comun `ctr_get_ficha`.

## Objetivo Funcional

Gestiona CtrGetFicha. Lectura de la ficha de atencion sacerdotal de un centro. Puerto del antiguo frontend/encargossacd/controller/ctr_get_ficha.php. Devuelve arrays planos/estructurados para que el controlador frontend arme frontend\shared\web\Desplegable y la HTML sin instanciar nada de src\.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/encargossacd/ctr_get_ficha_data`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/ctr_get_ficha.php`

## Casos De Uso Detectados

- `src\encargossacd\application\CtrGetFichaData`

## Pistas Desde Endpoints

- Lectura de la ficha de atencion sacerdotal de un centro. Puerto del antiguo `frontend/encargossacd/controller/ctr_get_ficha.php`. Devuelve arrays planos/estructurados para que el controlador frontend arme `frontend\shared\web\Desplegable` y la HTML sin instanciar nada de `src\`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
