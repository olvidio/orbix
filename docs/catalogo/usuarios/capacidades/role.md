---
id: "usuarios.role.gestionar"
tipo: "capacidad"
modulo: "usuarios"
nombre: "Gestionar Role"
entidades: ["rolesLista"]
acciones: ["eliminar", "guardar", "listar"]
endpoints: ["/src/usuarios/role_eliminar", "/src/usuarios/role_guardar", "/src/usuarios/role_lista"]
pantallas: ["frontend/usuarios/controller/role_lista.php", "frontend/usuarios/view/role_form.phtml", "frontend/usuarios/view/role_lista.phtml"]
casos_uso: ["src\\usuarios\\application\\rolesLista"]
tags: ["eliminar", "guardar", "lista", "role", "usuarios"]
estado_revision: "generado"
---

# Gestionar Role

Propuesta generada automaticamente a partir de endpoints con prefijo comun `role`.

## Objetivo Funcional

Gestiona rolesLista. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `eliminar`
- `guardar`
- `listar`

## Endpoints

- `/src/usuarios/role_eliminar`
- `/src/usuarios/role_guardar`
- `/src/usuarios/role_lista`

## Pantallas Relacionadas

- `frontend/usuarios/controller/role_lista.php`
- `frontend/usuarios/view/role_form.phtml`
- `frontend/usuarios/view/role_lista.phtml`

## Casos De Uso Detectados

- `src\usuarios\application\rolesLista`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
