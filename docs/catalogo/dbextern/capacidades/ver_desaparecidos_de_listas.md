---
id: "dbextern.ver_desaparecidos_de_listas.gestionar"
tipo: "capacidad"
modulo: "dbextern"
nombre: "Gestionar Ver Desaparecidos De Listas"
entidades: ["VerDesaparecidosDeListas"]
acciones: ["obtener_datos"]
endpoints: ["/src/dbextern/ver_desaparecidos_de_listas_datos"]
pantallas: ["frontend/dbextern/controller/ver_desaparecidos_de_listas.php"]
casos_uso: ["src\\dbextern\\application\\VerDesaparecidosDeListasData"]
tags: ["datos", "dbextern", "de", "desaparecidos", "listas", "ver", "ver_desaparecidos_de_listas"]
estado_revision: "generado"
---

# Gestionar Ver Desaparecidos De Listas

Propuesta generada automaticamente a partir de endpoints con prefijo comun `ver_desaparecidos_de_listas`.

## Objetivo Funcional

Gestiona VerDesaparecidosDeListas. Obtiene datos de personas de Orbix desaparecidas de la BDU.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/dbextern/ver_desaparecidos_de_listas_datos`

## Pantallas Relacionadas

- `frontend/dbextern/controller/ver_desaparecidos_de_listas.php`

## Casos De Uso Detectados

- `src\dbextern\application\VerDesaparecidosDeListasData`

## Pistas Desde Endpoints

- Obtiene datos de personas de Orbix desaparecidas de la BDU.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
