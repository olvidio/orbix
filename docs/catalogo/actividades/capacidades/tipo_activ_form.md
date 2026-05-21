---
id: "actividades.tipo_activ_form.gestionar"
tipo: "capacidad"
modulo: "actividades"
nombre: "Gestionar Tipo Activ Form"
entidades: ["TipoActivForm"]
acciones: ["crear"]
endpoints: ["/src/actividades/tipo_activ_form_nuevo"]
pantallas: ["frontend/actividades/controller/tipo_activ.php"]
casos_uso: ["src\\actividades\\application\\TipoActivFormNuevo"]
tags: ["activ", "actividades", "form", "nuevo", "tipo", "tipo_activ_form"]
estado_revision: "generado"
---

# Gestionar Tipo Activ Form

Propuesta generada automaticamente a partir de endpoints con prefijo comun `tipo_activ_form`.

## Objetivo Funcional

Gestiona TipoActivForm. Devuelve el HTML del formulario para crear un nuevo tipo de actividad. Portado del case form_nuevo del dispatcher legacy.

## Acciones Detectadas

- `crear`

## Endpoints

- `/src/actividades/tipo_activ_form_nuevo`

## Pantallas Relacionadas

- `frontend/actividades/controller/tipo_activ.php`

## Casos De Uso Detectados

- `src\actividades\application\TipoActivFormNuevo`

## Pistas Desde Endpoints

- Devuelve el HTML del formulario para crear un nuevo tipo de actividad. Portado del case `form_nuevo` del dispatcher legacy.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
