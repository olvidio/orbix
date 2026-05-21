---
id: "zonassacd.zona_sacd.gestionar"
tipo: "capacidad"
modulo: "zonassacd"
nombre: "Gestionar Zona Sacd"
entidades: ["ZonaSacd", "ZonaSacdLista", "ZonaSacdPage"]
acciones: ["crear_actualizar", "ejecutar", "listar"]
endpoints: ["/src/zonassacd/zona_sacd", "/src/zonassacd/zona_sacd_lista", "/src/zonassacd/zona_sacd_update"]
pantallas: ["frontend/zonassacd/controller/zona_sacd.php", "frontend/zonassacd/controller/zona_sacd_lista_ajax.php", "frontend/zonassacd/controller/zona_sacd_update_ajax.php"]
casos_uso: ["src\\zonassacd\\application\\ZonaSacdLista", "src\\zonassacd\\application\\ZonaSacdPage", "src\\zonassacd\\application\\ZonaSacdUpdate"]
tags: ["lista", "sacd", "update", "zona", "zona_sacd", "zonassacd"]
estado_revision: "generado"
---

# Gestionar Zona Sacd

Propuesta generada automaticamente a partir de endpoints con prefijo comun `zona_sacd`.

## Objetivo Funcional

Gestiona ZonaSacd, ZonaSacdLista, ZonaSacdPage. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `crear_actualizar`
- `ejecutar`
- `listar`

## Endpoints

- `/src/zonassacd/zona_sacd`
- `/src/zonassacd/zona_sacd_lista`
- `/src/zonassacd/zona_sacd_update`

## Pantallas Relacionadas

- `frontend/zonassacd/controller/zona_sacd.php`
- `frontend/zonassacd/controller/zona_sacd_lista_ajax.php`
- `frontend/zonassacd/controller/zona_sacd_update_ajax.php`

## Casos De Uso Detectados

- `src\zonassacd\application\ZonaSacdLista`
- `src\zonassacd\application\ZonaSacdPage`
- `src\zonassacd\application\ZonaSacdUpdate`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
