---
id: "usuarios.usuario_guardar_pwd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Usuario Guardar Pwd"
capacidad: "usuarios.usuario_guardar_pwd.gestionar"
pantallas_principales: []
fragmentos: ["usuarios.pantalla.usuario_form_pwd"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/usuario_guardar_pwd"]
estado_revision: "generado"
---

# Flujo - Gestionar Usuario Guardar Pwd

Propuesta generada automaticamente desde la capacidad `usuarios.usuario_guardar_pwd.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona UsuarioGuardarPwd. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.usuario_form_pwd`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_usuario`
- `form.password`
- `form.password1`
- `html.password`
- `html.password1`

Acciones JavaScript:
- `fnjs_chk_passwd`
- `fnjs_guardar`
- `fnjs_guardar_datos`
- `fnjs_logout`

## Endpoints Del Flujo

- `/src/usuarios/usuario_guardar_pwd`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
