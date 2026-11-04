---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Asignar un grupo a un usuario"
flujo: "usuarios.usuario_grupo_add.gestionar.flujo"
preguntas: ["Como asignar un grupo a un usuario en Asignar un grupo a un usuario?"]
pantallas_principales: ["usuarios.pantalla.usuario_form"]
fragmentos: ["usuarios.pantalla.usuario_form"]
endpoints: ["/src/usuarios/usuario_grupo_add"]
source: "docs/catalogo/usuarios/flujos/usuario_grupo_add.md"
estado_revision: "generado"
---

# Ayuda IA - Asignar un grupo a un usuario

Usa este documento para responder preguntas de usuario sobre como trabajar con `Asignar un grupo a un usuario`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como asignar un grupo a un usuario en Asignar un grupo a un usuario?

## Donde Entrar

- Usuario Form (`usuarios.pantalla.usuario_form`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Asignar un grupo a un usuario

1. Abrir la ficha del usuario y pulsar **añadir un grupo de permisos**.
2. En la tabla pulsar **añadir** en la fila del grupo.
3. La lista de grupos de la ficha se actualiza.
4. Si la operación caduca o no está permitida aparece «Operación no autorizada»; hay que volver a abrir la ficha.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/usuario_grupo_add`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.usuario_form`
- `usuarios.pantalla.usuario_form`

## Objetivo

Asignar un grupo de permisos a un usuario.

## Errores Documentados

- `Operación no autorizada`
- `hay un error, no se ha guardado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
