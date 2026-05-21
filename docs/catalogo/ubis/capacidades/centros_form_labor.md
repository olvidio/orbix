---
id: "ubis.centros_form_labor.gestionar"
tipo: "capacidad"
modulo: "ubis"
nombre: "Gestionar Centros Form Labor"
entidades: ["Centros"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/centros_form_labor"]
pantallas: ["frontend/ubis/controller/centros_form_labor.php"]
casos_uso: ["src\\ubis\\application\\CentrosFormData"]
tags: ["centros", "centros_form_labor", "form", "labor", "ubis"]
estado_revision: "generado"
---

# Gestionar Centros Form Labor

Propuesta generada automaticamente a partir de endpoints con prefijo comun `centros_form_labor`.

## Objetivo Funcional

Gestiona Centros. Datos comunes para los formularios de centro dl (labor / num / plazas). Los tres formularios muestran sobre un mismo centro un subconjunto de campos distinto según el modo indicado.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/ubis/centros_form_labor`

## Pantallas Relacionadas

- `frontend/ubis/controller/centros_form_labor.php`

## Casos De Uso Detectados

- `src\ubis\application\CentrosFormData`

## Pistas Desde Endpoints

- Datos comunes para los formularios de centro dl (labor / num / plazas). Los tres formularios muestran sobre un mismo centro un subconjunto de campos distinto según el modo indicado.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
