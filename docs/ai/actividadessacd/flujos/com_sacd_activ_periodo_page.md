---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadessacd"
titulo: "Com Sacd Activ Periodo Page"
flujo: "actividadessacd.com_sacd_activ_periodo_page.gestionar.flujo"
preguntas: ["Como obtener datos en Com Sacd Activ Periodo Page?"]
pantallas_principales: ["actividadessacd.pantalla.com_sacd_activ_periodo"]
fragmentos: ["actividadessacd.pantalla.com_sacd_activ_periodo"]
endpoints: ["/src/actividadessacd/com_sacd_activ_periodo_page_data"]
source: "docs/catalogo/actividadessacd/flujos/com_sacd_activ_periodo_page.md"
estado_revision: "generado"
---

# Ayuda IA - Com Sacd Activ Periodo Page

Usa este documento para responder preguntas de usuario sobre como trabajar con `Com Sacd Activ Periodo Page`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Com Sacd Activ Periodo Page?

## Donde Entrar

- Com Sacd Activ Periodo (`actividadessacd.pantalla.com_sacd_activ_periodo`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Abrir la pantalla de comunicación a los sacd.
2. Según el rol del usuario se decide si puede editar o solo ver.
3. Si hay permiso, se muestra el enlace para editar textos.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadessacd/com_sacd_activ_periodo_page_data`

## Pantallas Y Fragmentos Relacionados

- `actividadessacd.pantalla.com_sacd_activ_periodo`
- `actividadessacd.pantalla.com_sacd_activ_periodo`

## Objetivo

Al cargar la pantalla de comunicación, el sistema determina si el usuario puede editar los textos base (`perm_mod_txt`). Los usuarios con rol `p-sacd` no tienen permiso de edición.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
