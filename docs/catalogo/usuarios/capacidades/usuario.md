---
id: "usuarios.usuario.gestionar"
tipo: "capacidad"
modulo: "usuarios"
nombre: "Gestionar Usuario"
entidades: ["usuario", "usuariosLista"]
acciones: ["eliminar", "guardar", "listar", "ver_formulario"]
endpoints: ["/src/usuarios/usuario_eliminar", "/src/usuarios/usuario_form", "/src/usuarios/usuario_guardar", "/src/usuarios/usuario_lista"]
pantallas: ["frontend/usuarios/controller/usuario_form.php", "frontend/usuarios/controller/usuario_form_pwd.php", "frontend/usuarios/controller/usuario_lista.php"]
casos_uso: ["src\\usuarios\\application\\usuarioEliminar", "src\\usuarios\\application\\usuariosLista"]
tags: ["eliminar", "form", "guardar", "lista", "usuario", "usuarios"]
estado_revision: "generado"
---

# Gestionar Usuario

Propuesta generada automaticamente a partir de endpoints con prefijo comun `usuario`.

## Objetivo Funcional

Gestiona usuario, usuariosLista. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `eliminar`
- `guardar`
- `listar`
- `ver_formulario`

## Endpoints

- `/src/usuarios/usuario_eliminar`
- `/src/usuarios/usuario_form`
- `/src/usuarios/usuario_guardar`
- `/src/usuarios/usuario_lista`

## Pantallas Relacionadas

- `frontend/usuarios/controller/usuario_form.php`
- `frontend/usuarios/controller/usuario_form_pwd.php`
- `frontend/usuarios/controller/usuario_lista.php`

## Casos De Uso Detectados

- `src\usuarios\application\usuarioEliminar`
- `src\usuarios\application\usuariosLista`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
