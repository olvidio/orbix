---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Abrir la ficha de un rol"
flujo: "usuarios.role_info.gestionar.flujo"
preguntas: ["Como abrir la ficha de un rol en Abrir la ficha de un rol?"]
pantallas_principales: ["usuarios.pantalla.role_form"]
fragmentos: ["usuarios.pantalla.role_form"]
endpoints: ["/src/usuarios/role_info"]
source: "docs/catalogo/usuarios/flujos/role_info.md"
estado_revision: "generado"
---

# Ayuda IA - Abrir la ficha de un rol

Usa este documento para responder preguntas de usuario sobre como trabajar con `Abrir la ficha de un rol`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como abrir la ficha de un rol en Abrir la ficha de un rol?

## Donde Entrar

- Role Form (`usuarios.pantalla.role_form`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Abrir la ficha de un rol

1. En **lista de roles**, pulsar el rol.
2. Se muestran sus datos (sf, sv, pau, sacd, casas, dmz) y la tabla de grupos de menús asignados.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/role_info`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.role_form`
- `usuarios.pantalla.role_form`

## Objetivo

Cargar la ficha de un rol: sus datos, si se puede editar y los grupos de menús que ya tiene.

## Errores Documentados

- `Usuario no encontrado`
- `Rol no encontrado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
