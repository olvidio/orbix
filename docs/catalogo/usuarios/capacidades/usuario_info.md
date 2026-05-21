---
id: "usuarios.usuario_info.gestionar"
tipo: "capacidad"
modulo: "usuarios"
nombre: "Gestionar Usuario Info"
entidades: ["UsuarioInfo"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/usuario_info"]
pantallas: ["frontend/usuarios/controller/usuario_form.php", "frontend/usuarios/controller/usuario_form_2fa.php", "frontend/usuarios/controller/usuario_form_mail.php", "frontend/usuarios/controller/usuario_form_pwd.php"]
casos_uso: []
tags: ["info", "usuario", "usuario_info", "usuarios"]
estado_revision: "generado"
---

# Gestionar Usuario Info

Propuesta generada automaticamente a partir de endpoints con prefijo comun `usuario_info`.

## Objetivo Funcional

Gestiona UsuarioInfo. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/usuarios/usuario_info`

## Pantallas Relacionadas

- `frontend/usuarios/controller/usuario_form.php`
- `frontend/usuarios/controller/usuario_form_2fa.php`
- `frontend/usuarios/controller/usuario_form_mail.php`
- `frontend/usuarios/controller/usuario_form_pwd.php`

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
