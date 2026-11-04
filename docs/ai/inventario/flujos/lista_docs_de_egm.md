---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Ver los documentos de una valija"
flujo: "inventario.lista_docs_de_egm.gestionar.flujo"
preguntas: ["Como ver los documentos de una valija en Ver los documentos de una valija?"]
pantallas_principales: ["inventario.pantalla.equipajes_form_del", "inventario.pantalla.equipajes_lista_docs"]
fragmentos: ["inventario.pantalla.equipajes_form_del", "inventario.pantalla.equipajes_lista_docs"]
endpoints: ["/src/inventario/lista_docs_de_egm"]
source: "docs/catalogo/inventario/flujos/lista_docs_de_egm.md"
estado_revision: "generado"
---

# Ayuda IA - Ver los documentos de una valija

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver los documentos de una valija`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver los documentos de una valija en Ver los documentos de una valija?

## Donde Entrar

- Quitar doc de maleta (`inventario.pantalla.equipajes_form_del`)
- Lista docs EGM/lugar (`inventario.pantalla.equipajes_lista_docs`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver los documentos de una valija

1. En **hacer equipajes**, pulsar **quitar documento** en una valija.
2. Aparece la lista de documentos de esa valija (sigla e identificador).
3. Marcar los que se quitan.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/lista_docs_de_egm`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.equipajes_form_del`
- `inventario.pantalla.equipajes_lista_docs`
- `inventario.pantalla.equipajes_form_del`
- `inventario.pantalla.equipajes_lista_docs`

## Objetivo

Ver los documentos que lleva una valija, para quitar alguno.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
