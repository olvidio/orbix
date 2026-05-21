---
id: "usuarios.preferencias.gestionar"
tipo: "capacidad"
modulo: "usuarios"
nombre: "Gestionar Preferencias"
entidades: ["Preferencias"]
acciones: ["guardar"]
endpoints: ["/src/usuarios/preferencias_guardar"]
pantallas: ["frontend/shared/security/HashFront.php"]
casos_uso: []
tags: ["guardar", "preferencias", "usuarios"]
estado_revision: "generado"
---

# Gestionar Preferencias

Propuesta generada automaticamente a partir de endpoints con prefijo comun `preferencias`.

## Objetivo Funcional

Gestiona Preferencias. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `guardar`

## Endpoints

- `/src/usuarios/preferencias_guardar`

## Pantallas Relacionadas

- `frontend/shared/security/HashFront.php`

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
