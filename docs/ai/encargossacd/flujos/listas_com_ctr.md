---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Preparar la comunicación a los centros"
flujo: "encargossacd.listas_com_ctr.gestionar.flujo"
preguntas: ["Como preparar la comunicación a los centros en Preparar la comunicación a los centros?"]
pantallas_principales: ["encargossacd.pantalla.listas_com_ctr"]
fragmentos: ["encargossacd.pantalla.listas_com_ctr"]
endpoints: ["/src/encargossacd/listas_com_ctr_data"]
source: "docs/catalogo/encargossacd/flujos/listas_com_ctr.md"
estado_revision: "generado"
---

# Ayuda IA - Preparar la comunicación a los centros

Usa este documento para responder preguntas de usuario sobre como trabajar con `Preparar la comunicación a los centros`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como preparar la comunicación a los centros en Preparar la comunicación a los centros?

## Donde Entrar

- Listas Com Ctr (`encargossacd.pantalla.listas_com_ctr`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Preparar la comunicación a los centros

1. Entrar en **listados**.
2. Elegir **comunicación a los centros sv** (o **sf**).
3. Aparece un escrito por centro con **titular**, **suplente** y **colaboradores**, en el idioma del centro, con cabecera y lugar/fecha (por ejemplo «Barcelona, 3.11.2026»). El curso que aparece en el texto («para el curso …») se calcula solo, según la fecha de hoy (el curso empieza en septiembre); el resto del texto se cambia en **modificar los textos de las comunicaciones**.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/listas_com_ctr_data`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.listas_com_ctr`
- `encargossacd.pantalla.listas_com_ctr`

## Objetivo

Preparar el escrito que se envía a cada centro con su sacerdote titular, suplente y colaboradores.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
