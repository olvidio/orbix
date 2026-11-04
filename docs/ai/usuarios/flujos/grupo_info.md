---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Abrir un grupo para editarlo"
flujo: "usuarios.grupo_info.gestionar.flujo"
preguntas: ["Como abrir un grupo para editarlo en Abrir un grupo para editarlo?"]
pantallas_principales: ["usuarios.pantalla.grupo_form"]
fragmentos: ["usuarios.pantalla.grupo_form"]
endpoints: ["/src/usuarios/grupo_info"]
source: "docs/catalogo/usuarios/flujos/grupo_info.md"
estado_revision: "generado"
---

# Ayuda IA - Abrir un grupo para editarlo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Abrir un grupo para editarlo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como abrir un grupo para editarlo en Abrir un grupo para editarlo?

## Donde Entrar

- Grupo Form (`usuarios.pantalla.grupo_form`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Abrir un grupo para editarlo

1. En **grupos**, pulsar el grupo.
2. Se carga su nombre en el formulario.
3. Si el grupo ya no existe aparece «Grupo no encontrado».

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/grupo_info`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.grupo_form`
- `usuarios.pantalla.grupo_form`

## Objetivo

Cargar el nombre de un grupo en su formulario de edición.

## Errores Documentados

- `Grupo no encontrado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
