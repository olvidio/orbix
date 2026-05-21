---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Usuario 2fa"
flujo: "usuarios.usuario_2fa.gestionar.flujo"
preguntas: ["Como crear o modificar en Usuario 2fa?"]
pantallas_principales: []
fragmentos: ["usuarios.pantalla.usuario_form_2fa", "usuarios.pantalla.usuario_reset_2fa"]
endpoints: ["/src/usuarios/usuario_2fa_update"]
source: "docs/catalogo/usuarios/flujos/usuario_2fa.md"
estado_revision: "generado"
---

# Ayuda IA - Usuario 2fa

Usa este documento para responder preguntas de usuario sobre como trabajar con `Usuario 2fa`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar en Usuario 2fa?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/usuario_2fa_update`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.usuario_form_2fa`
- `usuarios.pantalla.usuario_reset_2fa`

## Objetivo

Gestiona Usuario2fa. Descripcion funcional pendiente de revisar.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
