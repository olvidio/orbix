---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Abrir el formulario de permiso de menú"
flujo: "usuarios.perm_menu_info.gestionar.flujo"
preguntas: ["Como abrir el formulario de permiso de menú en Abrir el formulario de permiso de menú?"]
pantallas_principales: ["usuarios.pantalla.perm_menu_form"]
fragmentos: ["usuarios.pantalla.perm_menu_form"]
endpoints: ["/src/usuarios/perm_menu_info"]
source: "docs/catalogo/usuarios/flujos/perm_menu_info.md"
estado_revision: "generado"
---

# Ayuda IA - Abrir el formulario de permiso de menú

Usa este documento para responder preguntas de usuario sobre como trabajar con `Abrir el formulario de permiso de menú`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como abrir el formulario de permiso de menú en Abrir el formulario de permiso de menú?

## Donde Entrar

- Perm Menu Form (`usuarios.pantalla.perm_menu_form`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Abrir el formulario de permiso de menú

1. En la ficha del usuario, pulsar añadir (o la fila a editar).
2. Se abre el formulario con el grupo de menús elegible.
3. Si el grupo ya no existe aparece «Grupo no encontrado».

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/perm_menu_info`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.perm_menu_form`
- `usuarios.pantalla.perm_menu_form`

## Objetivo

Cargar el formulario (nuevo o de edición) de un permiso de menú.

## Errores Documentados

- `Grupo no encontrado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
