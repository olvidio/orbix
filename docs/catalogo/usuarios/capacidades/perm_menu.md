---
id: "usuarios.perm_menu.gestionar"
tipo: "capacidad"
modulo: "usuarios"
nombre: "Gestionar Perm Menu"
entidades: ["PermMenu"]
acciones: ["eliminar", "guardar", "listar"]
endpoints: ["/src/usuarios/perm_menu_eliminar", "/src/usuarios/perm_menu_guardar", "/src/usuarios/perm_menu_lista"]
pantallas: ["frontend/usuarios/controller/grupo_form.php", "frontend/usuarios/view/perm_menu_form.phtml", "frontend/usuarios/view/perm_menu_lista.phtml"]
casos_uso: []
tags: ["eliminar", "guardar", "lista", "menu", "perm", "perm_menu", "usuarios"]
estado_revision: "generado"
---

# Gestionar Perm Menu

Propuesta generada automaticamente a partir de endpoints con prefijo comun `perm_menu`.

## Objetivo Funcional

Gestiona PermMenu. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `eliminar`
- `guardar`
- `listar`

## Endpoints

- `/src/usuarios/perm_menu_eliminar`
- `/src/usuarios/perm_menu_guardar`
- `/src/usuarios/perm_menu_lista`

## Pantallas Relacionadas

- `frontend/usuarios/controller/grupo_form.php`
- `frontend/usuarios/view/perm_menu_form.phtml`
- `frontend/usuarios/view/perm_menu_lista.phtml`

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
