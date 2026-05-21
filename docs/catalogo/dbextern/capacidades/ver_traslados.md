---
id: "dbextern.ver_traslados.gestionar"
tipo: "capacidad"
modulo: "dbextern"
nombre: "Gestionar Ver Traslados"
entidades: ["VerTraslados"]
acciones: ["obtener_datos"]
endpoints: ["/src/dbextern/ver_traslados_datos"]
pantallas: ["frontend/dbextern/controller/ver_traslados.php"]
casos_uso: ["src\\dbextern\\application\\VerTrasladosData"]
tags: ["datos", "dbextern", "traslados", "ver", "ver_traslados"]
estado_revision: "generado"
---

# Gestionar Ver Traslados

Propuesta generada automaticamente a partir de endpoints con prefijo comun `ver_traslados`.

## Objetivo Funcional

Gestiona VerTraslados. Obtiene datos de personas a trasladar desde otras DL.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/dbextern/ver_traslados_datos`

## Pantallas Relacionadas

- `frontend/dbextern/controller/ver_traslados.php`

## Casos De Uso Detectados

- `src\dbextern\application\VerTrasladosData`

## Pistas Desde Endpoints

- Obtiene datos de personas a trasladar desde otras DL.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
