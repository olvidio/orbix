---
id: "encargossacd.encargo_horario_select.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Encargo Horario Select"
entidades: ["EncargoHorarioSelect"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/encargo_horario_select_data"]
pantallas: ["frontend/encargossacd/controller/encargo_horario_select.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoHorarioSelectData"]
tags: ["data", "encargo", "encargo_horario_select", "encargossacd", "horario", "select"]
estado_revision: "generado"
---

# Gestionar Encargo Horario Select

Propuesta generada automaticamente a partir de endpoints con prefijo comun `encargo_horario_select`.

## Objetivo Funcional

Gestiona EncargoHorarioSelect. Datos para la lista de horarios de un encargo (encargo_horario_select). Se devuelven ya precalculados el texto descriptivo del horario y las fechas formateadas para que el frontend solo arme frontend\shared\web\Lista.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/encargossacd/encargo_horario_select_data`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/encargo_horario_select.php`

## Casos De Uso Detectados

- `src\encargossacd\application\EncargoHorarioSelectData`

## Pistas Desde Endpoints

- Datos para la lista de horarios de un encargo (`encargo_horario_select`). Se devuelven ya precalculados el texto descriptivo del horario y las fechas formateadas para que el frontend solo arme `frontend\shared\web\Lista`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
