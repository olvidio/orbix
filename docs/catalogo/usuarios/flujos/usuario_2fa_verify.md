---
id: "usuarios.usuario_2fa_verify.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Usuario 2fa Verify"
capacidad: "usuarios.usuario_2fa_verify.gestionar"
pantallas_principales: []
fragmentos: ["usuarios.pantalla.usuario_form_2fa"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/usuario_2fa_verify"]
estado_revision: "generado"
---

# Flujo - Gestionar Usuario 2fa Verify

Propuesta generada automaticamente desde la capacidad `usuarios.usuario_2fa_verify.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona Usuario2faVerify. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.usuario_form_2fa`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.enable_2fa`
- `form.secret_2fa`
- `form.verification_code`
- `html.btn_ok`
- `html.enable_2fa`
- `html.id_usuario`
- `html.verification_code`

Acciones JavaScript:
- `fnjs_enviar`
- `fnjs_guardar`
- `fnjs_guardar_datos`
- `fnjs_logout`

## Endpoints Del Flujo

- `/src/usuarios/usuario_2fa_verify`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
