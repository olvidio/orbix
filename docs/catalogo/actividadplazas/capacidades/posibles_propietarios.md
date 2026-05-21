---
id: "actividadplazas.posibles_propietarios.gestionar"
tipo: "capacidad"
modulo: "actividadplazas"
nombre: "Gestionar Posibles Propietarios"
entidades: ["PosiblesPropietarios"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadplazas/posibles_propietarios_data"]
pantallas: []
casos_uso: ["src\\actividadplazas\\application\\PosiblesPropietariosData"]
tags: ["actividadplazas", "data", "posibles", "posibles_propietarios", "propietarios"]
estado_revision: "generado"
---

# Gestionar Posibles Propietarios

Propuesta generada automaticamente a partir de endpoints con prefijo comun `posibles_propietarios`.

## Objetivo Funcional

Gestiona PosiblesPropietarios. Devuelve el payload JSON estandar de desplegable (id, opciones, selected, blanco, val_blanco) con los posibles propietarios de plaza para la persona+actividad indicadas.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividadplazas/posibles_propietarios_data`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\actividadplazas\application\PosiblesPropietariosData`

## Pistas Desde Endpoints

- Endpoint backend: devuelve el payload JSON estandar de desplegable (`id`, `opciones`, `selected`, `blanco`, `val_blanco`) con los posibles propietarios de plaza para la persona+actividad indicadas.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
