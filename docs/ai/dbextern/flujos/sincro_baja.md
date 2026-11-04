---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dbextern"
titulo: "Dar de baja una ficha de Aquinate"
flujo: "dbextern.sincro_baja.gestionar.flujo"
preguntas: ["Como dar de baja una ficha de aquinate en Dar de baja una ficha de Aquinate?"]
pantallas_principales: ["dbextern.pantalla.ver_desaparecidos_de_listas"]
fragmentos: ["dbextern.pantalla.ver_desaparecidos_de_listas"]
endpoints: ["/src/dbextern/sincro_baja"]
source: "docs/catalogo/dbextern/flujos/sincro_baja.md"
estado_revision: "generado"
---

# Ayuda IA - Dar de baja una ficha de Aquinate

Usa este documento para responder preguntas de usuario sobre como trabajar con `Dar de baja una ficha de Aquinate`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como dar de baja una ficha de aquinate en Dar de baja una ficha de Aquinate?

## Donde Entrar

- Aquinate con BDU desaparecida (`dbextern.pantalla.ver_desaparecidos_de_listas`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Dar de baja una ficha de aquinate

1. En la lista de fichas Aquinate sin correspondencia en la BDU, pulsar **baja** en la persona.
2. La ficha pasa a situación de baja. Si no se pudo cambiar la situación, avisa «OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio.»

Referencias tecnicas para verificar la respuesta:
- `/src/dbextern/sincro_baja`

## Pantallas Y Fragmentos Relacionados

- `dbextern.pantalla.ver_desaparecidos_de_listas`
- `dbextern.pantalla.ver_desaparecidos_de_listas`

## Objetivo

Dar de baja la ficha de Aquinate de una persona que ya no está en la BDU.

## Errores Documentados

- `OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio.`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
