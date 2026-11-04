---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Consultar y modificar la ficha de un sacerdote"
flujo: "encargossacd.sacd_ficha.gestionar.flujo"
preguntas: ["Como consultar y modificar la ficha de un sacerdote en Consultar y modificar la ficha de un sacerdote?"]
pantallas_principales: ["encargossacd.pantalla.sacd_ficha"]
fragmentos: ["encargossacd.pantalla.sacd_ficha_ajax"]
endpoints: ["/src/encargossacd/sacd_ficha_data", "/src/encargossacd/sacd_ficha_update"]
source: "docs/catalogo/encargossacd/flujos/sacd_ficha.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar y modificar la ficha de un sacerdote

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar y modificar la ficha de un sacerdote`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar y modificar la ficha de un sacerdote en Consultar y modificar la ficha de un sacerdote?

## Donde Entrar

- Sacd Ficha (`encargossacd.pantalla.sacd_ficha`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar y modificar la ficha de un sacerdote

1. Entrar en **ficha sacd**; la pantalla se titula «fase real».
2. Elegir el **tipo** (n, agd, sss+, cp) y luego el sacerdote en **Sacd:**.
3. Aparecen sus encargos agrupados en **titular**, **titular (no cl)**, **suplente** y **colaborador** (por ejemplo «Atención de un ctr (Raset)», «descanso», «estudio», «oficial dre»). Cada uno tiene tres casillas numéricas: **mañanas**, **tarde 1ª hora** y **tarde 2ª hora**. El número es la cantidad de días (a la semana) que el sacerdote cubre esa franja.
4. Para añadir otro encargo, elegir en **añadir encargo** (estudio, descanso, retiro, otros).
5. Escribir o cambiar las **observaciones** si hace falta y pulsar **Guardar**.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/sacd_ficha_data`
- `/src/encargossacd/sacd_ficha_update`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.sacd_ficha`
- `encargossacd.pantalla.sacd_ficha_ajax`

## Objetivo

Ver y modificar los encargos de un sacerdote y su dedicación en cada uno.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
