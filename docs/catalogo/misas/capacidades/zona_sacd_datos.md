---
id: "misas.zona_sacd_datos.gestionar"
tipo: "capacidad"
modulo: "misas"
nombre: "Gestionar Zona Sacd Datos"
entidades: ["ZonaSacdDatosGet"]
acciones: ["obtener"]
endpoints: ["/src/misas/zona_sacd_datos_get"]
pantallas: ["frontend/zonassacd/controller/zona_sacd.php"]
casos_uso: ["src\\misas\\application\\ZonaSacdDatosGet"]
tags: ["datos", "get", "misas", "sacd", "zona", "zona_sacd_datos"]
estado_revision: "generado"
---

# Gestionar Zona Sacd Datos

Propuesta generada automaticamente a partir de endpoints con prefijo comun `zona_sacd_datos`.

## Objetivo Funcional

Gestiona ZonaSacdDatosGet. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `obtener`

## Endpoints

- `/src/misas/zona_sacd_datos_get`

## Pantallas Relacionadas

- `frontend/zonassacd/controller/zona_sacd.php`

## Casos De Uso Detectados

- `src\misas\application\ZonaSacdDatosGet`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
