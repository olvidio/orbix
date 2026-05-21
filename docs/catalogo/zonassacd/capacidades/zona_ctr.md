---
id: "zonassacd.zona_ctr.gestionar"
tipo: "capacidad"
modulo: "zonassacd"
nombre: "Gestionar Zona Ctr"
entidades: ["ZonaCtr", "ZonaCtrLista", "ZonaCtrPage"]
acciones: ["crear_actualizar", "ejecutar", "listar"]
endpoints: ["/src/zonassacd/zona_ctr", "/src/zonassacd/zona_ctr_lista", "/src/zonassacd/zona_ctr_update"]
pantallas: ["frontend/zonassacd/controller/zona_ctr.php", "frontend/zonassacd/controller/zona_ctr_lista_ajax.php", "frontend/zonassacd/controller/zona_ctr_update_ajax.php"]
casos_uso: ["src\\zonassacd\\application\\ZonaCtrLista", "src\\zonassacd\\application\\ZonaCtrPage", "src\\zonassacd\\application\\ZonaCtrUpdate"]
tags: ["ctr", "lista", "update", "zona", "zona_ctr", "zonassacd"]
estado_revision: "generado"
---

# Gestionar Zona Ctr

Propuesta generada automaticamente a partir de endpoints con prefijo comun `zona_ctr`.

## Objetivo Funcional

Gestiona ZonaCtr, ZonaCtrLista, ZonaCtrPage. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `crear_actualizar`
- `ejecutar`
- `listar`

## Endpoints

- `/src/zonassacd/zona_ctr`
- `/src/zonassacd/zona_ctr_lista`
- `/src/zonassacd/zona_ctr_update`

## Pantallas Relacionadas

- `frontend/zonassacd/controller/zona_ctr.php`
- `frontend/zonassacd/controller/zona_ctr_lista_ajax.php`
- `frontend/zonassacd/controller/zona_ctr_update_ajax.php`

## Casos De Uso Detectados

- `src\zonassacd\application\ZonaCtrLista`
- `src\zonassacd\application\ZonaCtrPage`
- `src\zonassacd\application\ZonaCtrUpdate`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
