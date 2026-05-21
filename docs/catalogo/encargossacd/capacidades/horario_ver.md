---
id: "encargossacd.horario_ver.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Horario Ver"
entidades: ["EncargoHorarioVer"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/horario_ver_data"]
pantallas: ["frontend/encargossacd/controller/horario_ver.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoHorarioVerData"]
tags: ["data", "encargossacd", "horario", "horario_ver", "ver"]
estado_revision: "generado"
---

# Gestionar Horario Ver

Propuesta generada automaticamente a partir de endpoints con prefijo comun `horario_ver`.

## Objetivo Funcional

Gestiona EncargoHorarioVer. Datos del formulario de horario de encargo (no sacd).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/encargossacd/horario_ver_data`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/horario_ver.php`

## Casos De Uso Detectados

- `src\encargossacd\application\EncargoHorarioVerData`

## Pistas Desde Endpoints

- Datos del formulario de horario de encargo (no sacd).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
