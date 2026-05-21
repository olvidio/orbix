---
id: "notas.actividades_buscar.gestionar"
tipo: "capacidad"
modulo: "notas"
nombre: "Gestionar Actividades Buscar"
entidades: ["ActividadesBuscar"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/actividades_buscar_data"]
pantallas: ["frontend/notas/controller/actividad_buscar_form.php"]
casos_uso: ["src\\notas\\application\\ActividadesBuscarData"]
tags: ["actividades", "actividades_buscar", "buscar", "data", "notas"]
estado_revision: "generado"
---

# Gestionar Actividades Buscar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `actividades_buscar`.

## Objetivo Funcional

Gestiona ActividadesBuscar. Datos (delegaciones + actividades) para el dialogo "buscar actividad" que abre frontend/notas/controller/actividad_buscar_form.php desde form_notas_de_una_persona.phtml al modificar una nota asociada a una actividad.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/notas/actividades_buscar_data`

## Pantallas Relacionadas

- `frontend/notas/controller/actividad_buscar_form.php`

## Casos De Uso Detectados

- `src\notas\application\ActividadesBuscarData`

## Pistas Desde Endpoints

- Datos (delegaciones + actividades) para el dialogo "buscar actividad" que abre `frontend/notas/controller/actividad_buscar_form.php` desde `form_notas_de_una_persona.phtml` al modificar una nota asociada a una actividad.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
