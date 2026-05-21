---
id: "dbextern.ver_listas.gestionar"
tipo: "capacidad"
modulo: "dbextern"
nombre: "Gestionar Ver Listas"
entidades: ["VerListas"]
acciones: ["obtener_datos"]
endpoints: ["/src/dbextern/ver_listas_datos"]
pantallas: ["frontend/dbextern/controller/ver_listas.php"]
casos_uso: ["src\\dbextern\\application\\VerListasData"]
tags: ["datos", "dbextern", "listas", "ver", "ver_listas"]
estado_revision: "generado"
---

# Gestionar Ver Listas

Propuesta generada automaticamente a partir de endpoints con prefijo comun `ver_listas`.

## Objetivo Funcional

Gestiona VerListas. Obtiene la lista de personas BDU sin unir y los posibles matches Orbix.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/dbextern/ver_listas_datos`

## Pantallas Relacionadas

- `frontend/dbextern/controller/ver_listas.php`

## Casos De Uso Detectados

- `src\dbextern\application\VerListasData`

## Pistas Desde Endpoints

- Obtiene la lista de personas BDU sin unir y los posibles matches Orbix.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
