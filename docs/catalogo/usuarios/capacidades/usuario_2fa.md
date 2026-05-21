---
id: "usuarios.usuario_2fa.gestionar"
tipo: "capacidad"
modulo: "usuarios"
nombre: "Gestionar Usuario 2fa"
entidades: ["Usuario2fa"]
acciones: ["crear_actualizar"]
endpoints: ["/src/usuarios/usuario_2fa_update"]
pantallas: ["frontend/usuarios/controller/usuario_form_2fa.php", "frontend/usuarios/controller/usuario_reset_2fa.php"]
casos_uso: []
tags: ["2fa", "update", "usuario", "usuario_2fa", "usuarios"]
estado_revision: "generado"
---

# Gestionar Usuario 2fa

Propuesta generada automaticamente a partir de endpoints con prefijo comun `usuario_2fa`.

## Objetivo Funcional

Gestiona Usuario2fa. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `crear_actualizar`

## Endpoints

- `/src/usuarios/usuario_2fa_update`

## Pantallas Relacionadas

- `frontend/usuarios/controller/usuario_form_2fa.php`
- `frontend/usuarios/controller/usuario_reset_2fa.php`

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
