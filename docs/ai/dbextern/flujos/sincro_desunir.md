---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dbextern"
titulo: "Desunir una persona de la BDU"
flujo: "dbextern.sincro_desunir.gestionar.flujo"
preguntas: ["Como desunir una persona de la bdu en Desunir una persona de la BDU?"]
pantallas_principales: ["dbextern.pantalla.ver_desaparecidos_de_orbix"]
fragmentos: ["dbextern.pantalla.ver_desaparecidos_de_orbix"]
endpoints: ["/src/dbextern/sincro_desunir"]
source: "docs/catalogo/dbextern/flujos/sincro_desunir.md"
estado_revision: "generado"
---

# Ayuda IA - Desunir una persona de la BDU

Usa este documento para responder preguntas de usuario sobre como trabajar con `Desunir una persona de la BDU`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como desunir una persona de la bdu en Desunir una persona de la BDU?

## Donde Entrar

- BDU desaparecidas en Aquinate (`dbextern.pantalla.ver_desaparecidos_de_orbix`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Desunir una persona de la bdu

1. En la lista «BDU sin ficha Aquinate», pulsar **desunir** en la persona.
2. Si se ha hecho bien, la fila aparece tachada.

Referencias tecnicas para verificar la respuesta:
- `/src/dbextern/sincro_desunir`

## Pantallas Y Fragmentos Relacionados

- `dbextern.pantalla.ver_desaparecidos_de_orbix`
- `dbextern.pantalla.ver_desaparecidos_de_orbix`

## Objetivo

Romper un vínculo incorrecto entre una persona de la BDU y una ficha de Aquinate, para poder unirla de nuevo o crear la ficha después.

## Errores Documentados

- `no se encontró el registro a desunir`
- `hay un error, no se ha eliminado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
