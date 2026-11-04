---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Borrar las contraseñas en pruebas"
flujo: "usuarios.borrar_pwd.gestionar.flujo"
preguntas: ["Como borrar las contraseñas en pruebas en Borrar las contraseñas en pruebas?"]
pantallas_principales: ["usuarios.pantalla.borrar_todos_pwd"]
fragmentos: []
endpoints: ["/src/usuarios/borrar_pwd"]
source: "docs/catalogo/usuarios/flujos/borrar_pwd.md"
estado_revision: "generado"
---

# Ayuda IA - Borrar las contraseñas en pruebas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Borrar las contraseñas en pruebas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como borrar las contraseñas en pruebas en Borrar las contraseñas en pruebas?

## Donde Entrar

- Borrar Todos Pwd (`usuarios.pantalla.borrar_todos_pwd`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Borrar las contraseñas en pruebas

1. Entrar en la opción del menú **sistema**.
2. Confirmar la acción: afecta a todos los esquemas.
3. Solo funciona en la base de pruebas; en producción aparece «Sólo se puede borrar en la base de datos de pruebas».

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/borrar_pwd`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.borrar_todos_pwd`

## Objetivo

Herramienta de pruebas: borra las contraseñas de todos los usuarios (salvo el superadministrador) para poder entrar sin ellas.

## Errores Documentados

- `No se pudieron obtener esquemas`
- `Sólo se puede borrar en la base de datos de pruebas`
- `hay un error, no se ha guardado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
