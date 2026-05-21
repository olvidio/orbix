---
id: "personas.traslado.gestionar"
tipo: "capacidad"
modulo: "personas"
nombre: "Gestionar Traslado"
entidades: ["Traslado"]
acciones: ["crear_actualizar", "ver_formulario"]
endpoints: ["/src/personas/traslado_form_data", "/src/personas/traslado_update"]
pantallas: ["frontend/personas/controller/traslado_form.php", "frontend/personas/view/traslado_form.phtml"]
casos_uso: ["src\\personas\\application\\TrasladoFormData", "src\\personas\\application\\TrasladoUpdate"]
tags: ["data", "form", "personas", "traslado", "update"]
estado_revision: "generado"
---

# Gestionar Traslado

Propuesta generada automaticamente a partir de endpoints con prefijo comun `traslado`.

## Objetivo Funcional

Gestiona Traslado. Endpoint JSON: aplica un traslado de centro y/o delegacion. Endpoint JSON: datos para el formulario traslado_form.phtml.

## Acciones Detectadas

- `crear_actualizar`
- `ver_formulario`

## Endpoints

- `/src/personas/traslado_form_data`
- `/src/personas/traslado_update`

## Pantallas Relacionadas

- `frontend/personas/controller/traslado_form.php`
- `frontend/personas/view/traslado_form.phtml`

## Casos De Uso Detectados

- `src\personas\application\TrasladoFormData`
- `src\personas\application\TrasladoUpdate`

## Pistas Desde Endpoints

- Endpoint JSON: aplica un traslado de centro y/o delegacion.
- Endpoint JSON: datos para el formulario `traslado_form.phtml`.

## Errores Conocidos

- `Faltan id_pau u obj_pau`
- `No existe la clase de la persona`
- `No se encuentra la persona`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
