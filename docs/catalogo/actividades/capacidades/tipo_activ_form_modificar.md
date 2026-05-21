---
id: "actividades.tipo_activ_form_modificar.gestionar"
tipo: "capacidad"
modulo: "actividades"
nombre: "Gestionar Tipo Activ Form Modificar"
entidades: ["TipoActivFormModificar"]
acciones: ["ejecutar"]
endpoints: ["/src/actividades/tipo_activ_form_modificar"]
pantallas: ["frontend/actividades/controller/tipo_activ.php"]
casos_uso: ["src\\actividades\\application\\TipoActivFormModificar"]
tags: ["activ", "actividades", "form", "modificar", "tipo", "tipo_activ_form_modificar"]
estado_revision: "generado"
---

# Gestionar Tipo Activ Form Modificar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `tipo_activ_form_modificar`.

## Objetivo Funcional

Gestiona TipoActivFormModificar. Devuelve el HTML del formulario para modificar/eliminar un tipo de actividad existente. Portado del case form_modificar del dispatcher legacy.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/actividades/tipo_activ_form_modificar`

## Pantallas Relacionadas

- `frontend/actividades/controller/tipo_activ.php`

## Casos De Uso Detectados

- `src\actividades\application\TipoActivFormModificar`

## Pistas Desde Endpoints

- Devuelve el HTML del formulario para modificar/eliminar un tipo de actividad existente. Portado del case `form_modificar` del dispatcher legacy.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
