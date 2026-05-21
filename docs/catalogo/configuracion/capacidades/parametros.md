---
id: "configuracion.parametros.gestionar"
tipo: "capacidad"
modulo: "configuracion"
nombre: "Gestionar Parametros"
entidades: ["Parametros"]
acciones: ["crear_actualizar", "listar"]
endpoints: ["/src/configuracion/parametros_lista", "/src/configuracion/parametros_update"]
pantallas: ["frontend/configuracion/controller/parametros.php"]
casos_uso: []
tags: ["configuracion", "lista", "parametros", "update"]
estado_revision: "generado"
---

# Gestionar Parametros

Propuesta generada automaticamente a partir de endpoints con prefijo comun `parametros`.

## Objetivo Funcional

Gestiona Parametros. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `crear_actualizar`
- `listar`

## Endpoints

- `/src/configuracion/parametros_lista`
- `/src/configuracion/parametros_update`

## Pantallas Relacionadas

- `frontend/configuracion/controller/parametros.php`

## Casos De Uso Detectados

No se han detectado casos de uso de aplicacion.

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
