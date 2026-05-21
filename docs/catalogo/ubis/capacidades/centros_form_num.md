---
id: "ubis.centros_form_num.gestionar"
tipo: "capacidad"
modulo: "ubis"
nombre: "Gestionar Centros Form Num"
entidades: ["Centros"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/centros_form_num"]
pantallas: ["frontend/ubis/controller/centros_form_num.php"]
casos_uso: ["src\\ubis\\application\\CentrosFormData"]
tags: ["centros", "centros_form_num", "form", "num", "ubis"]
estado_revision: "generado"
---

# Gestionar Centros Form Num

Propuesta generada automaticamente a partir de endpoints con prefijo comun `centros_form_num`.

## Objetivo Funcional

Gestiona Centros. Datos comunes para los formularios de centro dl (labor / num / plazas). Los tres formularios muestran sobre un mismo centro un subconjunto de campos distinto según el modo indicado.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/ubis/centros_form_num`

## Pantallas Relacionadas

- `frontend/ubis/controller/centros_form_num.php`

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
