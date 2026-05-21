---
id: "usuarios.app_session.gestionar"
tipo: "capacidad"
modulo: "usuarios"
nombre: "Gestionar App Session"
entidades: ["AppSession"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/app_session"]
pantallas: []
casos_uso: []
tags: ["app", "app_session", "session", "usuarios"]
estado_revision: "generado"
---

# Gestionar App Session

Propuesta generada automaticamente a partir de endpoints con prefijo comun `app_session`.

## Objetivo Funcional

Gestiona AppSession. Comprueba si hay sesión autenticada (útil al arrancar la app).

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/usuarios/app_session`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

No se han detectado casos de uso de aplicacion.

## Pistas Desde Endpoints

- Comprueba si hay sesión autenticada (útil al arrancar la app).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
