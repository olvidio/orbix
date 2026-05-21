---
id: "usuarios.grupo.gestionar"
tipo: "capacidad"
modulo: "usuarios"
nombre: "Gestionar Grupo"
entidades: ["GruposLista"]
acciones: ["eliminar", "guardar", "listar"]
endpoints: ["/src/usuarios/grupo_eliminar", "/src/usuarios/grupo_guardar", "/src/usuarios/grupo_lista"]
pantallas: ["frontend/usuarios/controller/grupo_lista.php", "frontend/usuarios/view/grupo_lista.phtml"]
casos_uso: ["src\\usuarios\\application\\GruposLista"]
tags: ["eliminar", "grupo", "guardar", "lista", "usuarios"]
estado_revision: "generado"
---

# Gestionar Grupo

Propuesta generada automaticamente a partir de endpoints con prefijo comun `grupo`.

## Objetivo Funcional

Gestiona GruposLista. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `eliminar`
- `guardar`
- `listar`

## Endpoints

- `/src/usuarios/grupo_eliminar`
- `/src/usuarios/grupo_guardar`
- `/src/usuarios/grupo_lista`

## Pantallas Relacionadas

- `frontend/usuarios/controller/grupo_lista.php`
- `frontend/usuarios/view/grupo_lista.phtml`

## Casos De Uso Detectados

- `src\usuarios\application\GruposLista`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
