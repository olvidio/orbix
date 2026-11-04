---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Listar la tabla por sacerdote"
flujo: "encargossacd.listas_d.gestionar.flujo"
preguntas: ["Como listar la tabla por sacerdote en Listar la tabla por sacerdote?"]
pantallas_principales: ["encargossacd.pantalla.listas_d"]
fragmentos: ["encargossacd.pantalla.listas_d"]
endpoints: ["/src/encargossacd/listas_d_data"]
source: "docs/catalogo/encargossacd/flujos/listas_d.md"
estado_revision: "generado"
---

# Ayuda IA - Listar la tabla por sacerdote

Usa este documento para responder preguntas de usuario sobre como trabajar con `Listar la tabla por sacerdote`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como listar la tabla por sacerdote en Listar la tabla por sacerdote?

## Donde Entrar

- Listas D (`encargossacd.pantalla.listas_d`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Listar la tabla por sacerdote

1. Entrar en **listados**.
2. Elegir **tabla por sacd** o **tabla por sacd sf**.
3. Se muestra, por sacerdote y ciudad, la lista de centros con el nº de horas (por ejemplo Raset 3, Moneders 5), incluidos estudio y descanso.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/listas_d_data`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.listas_d`
- `encargossacd.pantalla.listas_d`

## Objetivo

Sacar la tabla por sacerdote (cr 9/20, 10), para sv o sf.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
