---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dbextern"
titulo: "Trasladar una ficha a otra dl"
flujo: "dbextern.sincro_trasladar_a.gestionar.flujo"
preguntas: ["Como trasladar una ficha a otra dl en Trasladar una ficha a otra dl?"]
pantallas_principales: ["dbextern.pantalla.ver_orbix_otradl"]
fragmentos: ["dbextern.pantalla.ver_orbix_otradl"]
endpoints: ["/src/dbextern/sincro_trasladar_a"]
source: "docs/catalogo/dbextern/flujos/sincro_trasladar_a.md"
estado_revision: "generado"
---

# Ayuda IA - Trasladar una ficha a otra dl

Usa este documento para responder preguntas de usuario sobre como trabajar con `Trasladar una ficha a otra dl`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como trasladar una ficha a otra dl en Trasladar una ficha a otra dl?

## Donde Entrar

- Trasladar a otra DL (`dbextern.pantalla.ver_orbix_otradl`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Trasladar una ficha a otra dl

1. En el punto 7, pulsar **trasladar** en la persona.
2. Solo se puede si la región de destino es la misma que la actual; si no, avisa «Este traslado debe hacerse desde el dossier de traslados».

Referencias tecnicas para verificar la respuesta:
- `/src/dbextern/sincro_trasladar_a`

## Pantallas Y Fragmentos Relacionados

- `dbextern.pantalla.ver_orbix_otradl`
- `dbextern.pantalla.ver_orbix_otradl`

## Objetivo

Mover la ficha de Aquinate a la delegación donde la persona está en las listas de la BDU.

## Errores Documentados

- `No se encontró la delegación destino`
- `Este traslado debe hacerse desde el dossier de traslados`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
