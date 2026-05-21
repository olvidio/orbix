---
id: "usuarios.app_login.gestionar"
tipo: "capacidad"
modulo: "usuarios"
nombre: "Gestionar App Login"
entidades: ["AppMobileLogin"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/app_login"]
pantallas: []
casos_uso: ["src\\usuarios\\application\\AppMobileLogin"]
tags: ["app", "app_login", "login", "usuarios"]
estado_revision: "generado"
---

# Gestionar App Login

Propuesta generada automaticamente a partir de endpoints con prefijo comun `app_login`.

## Objetivo Funcional

Gestiona AppMobileLogin. Login JSON para app móvil (Camino B).

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/usuarios/app_login`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\usuarios\application\AppMobileLogin`

## Pistas Desde Endpoints

- Login JSON para app móvil (Camino B).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
