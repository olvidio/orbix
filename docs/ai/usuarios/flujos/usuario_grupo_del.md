---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Quitar un grupo a un usuario"
flujo: "usuarios.usuario_grupo_del.gestionar.flujo"
preguntas: ["Como quitar un grupo a un usuario en Quitar un grupo a un usuario?"]
pantallas_principales: ["usuarios.pantalla.usuario_form"]
fragmentos: ["usuarios.pantalla.usuario_form"]
endpoints: ["/src/usuarios/usuario_grupo_del"]
source: "docs/catalogo/usuarios/flujos/usuario_grupo_del.md"
estado_revision: "generado"
---

# Ayuda IA - Quitar un grupo a un usuario

Usa este documento para responder preguntas de usuario sobre como trabajar con `Quitar un grupo a un usuario`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como quitar un grupo a un usuario en Quitar un grupo a un usuario?

## Donde Entrar

- Usuario Form (`usuarios.pantalla.usuario_form`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Quitar un grupo a un usuario

1. Abrir la ficha del usuario y pulsar **quitar de un grupo de permisos**.
2. En la tabla pulsar **quitar** en la fila del grupo (sin confirmación).
3. La lista de grupos se actualiza.
4. Si aparece «Operación no autorizada», volver a abrir la ficha y repetir.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/usuario_grupo_del`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.usuario_form`
- `usuarios.pantalla.usuario_form`

## Objetivo

Quitar un grupo de permisos a un usuario.

## Errores Documentados

- `Operación no autorizada`
- `hay un error, no se ha eliminado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
