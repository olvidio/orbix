---
id: "encargossacd.horario_sacd_ver.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Horario Sacd Ver"
entidades: ["EncargoSacdHorarioVer"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/horario_sacd_ver_data"]
pantallas: ["frontend/encargossacd/controller/horario_sacd_ver.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoSacdHorarioVerData"]
tags: ["data", "encargossacd", "horario", "horario_sacd_ver", "sacd", "ver"]
estado_revision: "generado"
---

# Gestionar Horario Sacd Ver

Propuesta generada automaticamente a partir de endpoints con prefijo comun `horario_sacd_ver`.

## Objetivo Funcional

Gestiona EncargoSacdHorarioVer. Datos del formulario horario sacd (ficha tareas).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/encargossacd/horario_sacd_ver_data`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/horario_sacd_ver.php`

## Casos De Uso Detectados

- `src\encargossacd\application\EncargoSacdHorarioVerData`

## Pistas Desde Endpoints

- Datos del formulario horario sacd (ficha tareas).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
