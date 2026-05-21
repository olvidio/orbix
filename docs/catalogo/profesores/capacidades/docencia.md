---
id: "profesores.docencia.gestionar"
tipo: "capacidad"
modulo: "profesores"
nombre: "Gestionar Docencia"
entidades: ["DocenciaLista"]
acciones: ["ejecutar"]
endpoints: ["/src/profesores/docencia"]
pantallas: ["frontend/profesores/controller/docencia.php"]
casos_uso: ["src\\profesores\\application\\DocenciaLista"]
tags: ["docencia", "profesores"]
estado_revision: "generado"
---

# Gestionar Docencia

Propuesta generada automaticamente a partir de endpoints con prefijo comun `docencia`.

## Objetivo Funcional

Gestiona DocenciaLista. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/profesores/docencia`

## Pantallas Relacionadas

- `frontend/profesores/controller/docencia.php`

## Casos De Uso Detectados

- `src\profesores\application\DocenciaLista`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
