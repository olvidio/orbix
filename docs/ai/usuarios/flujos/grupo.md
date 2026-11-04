---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Buscar un grupo"
flujo: "usuarios.grupo.gestionar.flujo"
preguntas: ["Como buscar un grupo en Buscar un grupo?", "Como crear un grupo en Buscar un grupo?", "Como cambiar el nombre de un grupo en Buscar un grupo?", "Como eliminar un grupo en Buscar un grupo?"]
pantallas_principales: ["usuarios.pantalla.grupo_lista"]
fragmentos: ["usuarios.pantalla.grupo_lista"]
endpoints: ["/src/usuarios/grupo_eliminar", "/src/usuarios/grupo_guardar", "/src/usuarios/grupo_lista"]
source: "docs/catalogo/usuarios/flujos/grupo.md"
estado_revision: "generado"
---

# Ayuda IA - Buscar un grupo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Buscar un grupo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como buscar un grupo en Buscar un grupo?
- Como crear un grupo en Buscar un grupo?
- Como cambiar el nombre de un grupo en Buscar un grupo?
- Como eliminar un grupo en Buscar un grupo?

## Donde Entrar

- Grupo Lista (`usuarios.pantalla.grupo_lista`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Buscar un grupo

1. Entrar en **grupos**.
2. Escribir parte del nombre en **Nombre** y pulsar **Buscar**.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/grupo_eliminar`
- `/src/usuarios/grupo_guardar`
- `/src/usuarios/grupo_lista`

## Crear un grupo

1. Pulsar **NUEVO GRUPO**.
2. Escribir el nombre. Convención de la pantalla: poner entre paréntesis si el grupo se refiere a menús (m), permisos (p) o ambos (m+p).
3. Guardar.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/grupo_eliminar`
- `/src/usuarios/grupo_guardar`
- `/src/usuarios/grupo_lista`

## Cambiar el nombre de un grupo

1. Pulsar el grupo en la lista, modificar el nombre y guardar.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/grupo_eliminar`
- `/src/usuarios/grupo_guardar`
- `/src/usuarios/grupo_lista`

## Eliminar un grupo

1. Marcar el grupo en la lista y pulsar eliminar.
2. Confirmar. Si hay un error aparece «hay un error, no se ha eliminado».

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/grupo_eliminar`
- `/src/usuarios/grupo_guardar`
- `/src/usuarios/grupo_lista`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.grupo_lista`
- `usuarios.pantalla.grupo_lista`

## Objetivo

Administrar los grupos de permisos que se asignan a los usuarios: crear, renombrar y eliminar.

## Errores Documentados

- `Grupo no encontrado`
- `hay un error, no se ha eliminado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
