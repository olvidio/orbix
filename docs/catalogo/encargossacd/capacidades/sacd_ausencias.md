---
id: "encargossacd.sacd_ausencias.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Sacd Ausencias"
entidades: ["SacdAusencias"]
acciones: ["crear_actualizar"]
endpoints: ["/src/encargossacd/sacd_ausencias_update"]
pantallas: ["frontend/encargossacd/controller/sacd_ausencias_update.php"]
casos_uso: ["src\\encargossacd\\application\\SacdAusenciasUpdate"]
tags: ["ausencias", "encargossacd", "sacd", "sacd_ausencias", "update"]
estado_revision: "generado"
---

# Gestionar Sacd Ausencias

Propuesta generada automaticamente a partir de endpoints con prefijo comun `sacd_ausencias`.

## Objetivo Funcional

Gestiona SacdAusencias. Guarda/modifica las ausencias de un SACD (frontend/encargossacd/controller/sacd_ausencias_update.php). Devuelve ['error' => bool, 'mensajes' => string] donde mensajes acumula los errores de guardado/eliminacion para mostrar al usuario.

## Acciones Detectadas

- `crear_actualizar`

## Endpoints

- `/src/encargossacd/sacd_ausencias_update`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/sacd_ausencias_update.php`

## Casos De Uso Detectados

- `src\encargossacd\application\SacdAusenciasUpdate`

## Pistas Desde Endpoints

- Guarda/modifica las ausencias de un SACD (`frontend/encargossacd/controller/sacd_ausencias_update.php`). Devuelve ['error' => bool, 'mensajes' => string] donde `mensajes` acumula los errores de guardado/eliminacion para mostrar al usuario.

## Errores Conocidos

- `no se ha encontrado el encargo del sacd`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
