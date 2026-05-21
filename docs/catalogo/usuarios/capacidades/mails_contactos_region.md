---
id: "usuarios.mails_contactos_region.gestionar"
tipo: "capacidad"
modulo: "usuarios"
nombre: "Gestionar Mails Contactos Region"
entidades: ["usuariosRegionContactos"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/mails_contactos_region"]
pantallas: ["frontend/usuarios/controller/mails_contactos_region.php"]
casos_uso: ["src\\usuarios\\application\\usuariosRegionContactos"]
tags: ["contactos", "mails", "mails_contactos_region", "region", "usuarios"]
estado_revision: "generado"
---

# Gestionar Mails Contactos Region

Propuesta generada automaticamente a partir de endpoints con prefijo comun `mails_contactos_region`.

## Objetivo Funcional

Gestiona usuariosRegionContactos. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/usuarios/mails_contactos_region`

## Pantallas Relacionadas

- `frontend/usuarios/controller/mails_contactos_region.php`

## Casos De Uso Detectados

- `src\usuarios\application\usuariosRegionContactos`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
