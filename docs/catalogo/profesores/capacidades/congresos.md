---
id: "profesores.congresos.gestionar"
tipo: "capacidad"
modulo: "profesores"
nombre: "Gestionar Congresos"
entidades: ["CongresosLista"]
acciones: ["ejecutar"]
endpoints: ["/src/profesores/congresos"]
pantallas: ["frontend/profesores/controller/congresos.php"]
casos_uso: ["src\\profesores\\application\\CongresosLista"]
tags: ["congresos", "profesores"]
estado_revision: "generado"
---

# Gestionar Congresos

Propuesta generada automaticamente a partir de endpoints con prefijo comun `congresos`.

## Objetivo Funcional

Gestiona CongresosLista. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/profesores/congresos`

## Pantallas Relacionadas

- `frontend/profesores/controller/congresos.php`

## Casos De Uso Detectados

- `src\profesores\application\CongresosLista`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
