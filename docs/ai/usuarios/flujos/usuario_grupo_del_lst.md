---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Ver los grupos que se pueden quitar"
flujo: "usuarios.usuario_grupo_del_lst.gestionar.flujo"
preguntas: ["Como ver los grupos que se pueden quitar en Ver los grupos que se pueden quitar?"]
pantallas_principales: ["usuarios.pantalla.usuario_grupo_del_lst"]
fragmentos: ["usuarios.pantalla.usuario_grupo_del_lst"]
endpoints: ["/src/usuarios/usuario_grupo_del_lst"]
source: "docs/catalogo/usuarios/flujos/usuario_grupo_del_lst.md"
estado_revision: "generado"
---

# Ayuda IA - Ver los grupos que se pueden quitar

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver los grupos que se pueden quitar`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver los grupos que se pueden quitar en Ver los grupos que se pueden quitar?

## Donde Entrar

- Usuario Grupo Del Lst (`usuarios.pantalla.usuario_grupo_del_lst`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver los grupos que se pueden quitar

1. En la ficha del usuario, pulsar **quitar de un grupo de permisos**.
2. Aparece la lista de grupos asignados.
3. En la tabla pulsar **quitar** en la fila del grupo: se retira al instante, sin pedir confirmación (ver «Quitar un grupo a un usuario»).

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/usuario_grupo_del_lst`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.usuario_grupo_del_lst`
- `usuarios.pantalla.usuario_grupo_del_lst`

## Objetivo

Mostrar los grupos que el usuario ya tiene, para poder quitarle alguno.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
