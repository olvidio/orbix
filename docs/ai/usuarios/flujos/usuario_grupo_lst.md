---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Elegir un grupo para añadir"
flujo: "usuarios.usuario_grupo_lst.gestionar.flujo"
preguntas: ["Como elegir un grupo para añadir en Elegir un grupo para añadir?"]
pantallas_principales: ["usuarios.pantalla.usuario_grupo_lst"]
fragmentos: ["usuarios.pantalla.usuario_grupo_lst"]
endpoints: ["/src/usuarios/usuario_grupo_lst"]
source: "docs/catalogo/usuarios/flujos/usuario_grupo_lst.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir un grupo para añadir

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir un grupo para añadir`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir un grupo para añadir en Elegir un grupo para añadir?

## Donde Entrar

- Usuario Grupo Lst (`usuarios.pantalla.usuario_grupo_lst`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir un grupo para añadir

1. En la ficha del usuario, pulsar **añadir un grupo de permisos**.
2. Aparece la lista de grupos disponibles, sin los que el usuario ya tiene.
3. En la tabla (columnas **grupo**, **sección**, **acción**) pulsar **añadir** en la fila del grupo: se asigna al instante (ver «Asignar un grupo a un usuario»).

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/usuario_grupo_lst`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.usuario_grupo_lst`
- `usuarios.pantalla.usuario_grupo_lst`

## Objetivo

Mostrar los grupos de permisos que se pueden añadir a un usuario (los que aún no tiene).

## Errores Documentados

- `Usuario no encontrado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
