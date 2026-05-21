---
id: "encargossacd.sacd_ficha.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Sacd Ficha"
entidades: ["SacdFicha"]
acciones: ["crear_actualizar", "obtener_datos"]
endpoints: ["/src/encargossacd/sacd_ficha_data", "/src/encargossacd/sacd_ficha_update"]
pantallas: ["frontend/encargossacd/controller/sacd_ficha_ajax.php"]
casos_uso: ["src\\encargossacd\\application\\SacdFichaData", "src\\encargossacd\\application\\SacdFichaUpdate"]
tags: ["data", "encargossacd", "ficha", "sacd", "sacd_ficha", "update"]
estado_revision: "generado"
---

# Gestionar Sacd Ficha

Propuesta generada automaticamente a partir de endpoints con prefijo comun `sacd_ficha`.

## Objetivo Funcional

Gestiona SacdFicha. Datos para la ficha de encargos de un SACD (sacd_ficha_ajax?que=ficha). Porta la lectura del antiguo controlador frontend y devuelve un payload estructurado con los encargos y sus dedicaciones (horario del centro y del SACD ya calculadas como texto cuando mod_horario=3). Mutacion de la ficha de encargos de un SACD (sacd_ficha_ajax?que=update). Porta la logica del antiguo controlador frontend, haciendo la misma actualizacion de dedicaciones por modulo y de observaciones.

## Acciones Detectadas

- `crear_actualizar`
- `obtener_datos`

## Endpoints

- `/src/encargossacd/sacd_ficha_data`
- `/src/encargossacd/sacd_ficha_update`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/sacd_ficha_ajax.php`

## Casos De Uso Detectados

- `src\encargossacd\application\SacdFichaData`
- `src\encargossacd\application\SacdFichaUpdate`

## Pistas Desde Endpoints

- Datos para la ficha de encargos de un SACD (`sacd_ficha_ajax?que=ficha`). Porta la lectura del antiguo controlador frontend y devuelve un payload estructurado con los encargos y sus dedicaciones (horario del centro y del SACD ya calculadas como texto cuando `mod_horario=3`).
- Mutacion de la ficha de encargos de un SACD (`sacd_ficha_ajax?que=update`). Porta la logica del antiguo controlador frontend, haciendo la misma actualizacion de dedicaciones por modulo y de observaciones.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
