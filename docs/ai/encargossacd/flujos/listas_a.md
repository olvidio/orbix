---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Listar la atención de los centros"
flujo: "encargossacd.listas_a.gestionar.flujo"
preguntas: ["Como listar la atención de los centros en Listar la atención de los centros?"]
pantallas_principales: ["encargossacd.pantalla.listas_a"]
fragmentos: ["encargossacd.pantalla.listas_a"]
endpoints: ["/src/encargossacd/listas_a_data"]
source: "docs/catalogo/encargossacd/flujos/listas_a.md"
estado_revision: "generado"
---

# Ayuda IA - Listar la atención de los centros

Usa este documento para responder preguntas de usuario sobre como trabajar con `Listar la atención de los centros`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como listar la atención de los centros en Listar la atención de los centros?

## Donde Entrar

- Listas A (`encargossacd.pantalla.listas_a`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Listar la atención de los centros

1. Entrar en **listados**.
2. En «listados de encargos para cr» elegir **atención ctr sv** o **atención ctr sf**.
3. Se muestra el listado, listo para imprimir: cabecera con el **Curso** (por ejemplo 1/9/2026 - 31/8/2027), «dlb» y la referencia «ref. cr 1/14, 10, a)»; luego «1. ctr de n» con cada centro y sus sacerdotes. Si un sacerdote asignado ya no existe, el listado lo dice («No encuentro a nadie con id_nom: …»); hay que corregir ese encargo.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/listas_a_data`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.listas_a`
- `encargossacd.pantalla.listas_a`

## Objetivo

Sacar el listado de atención de centros de sv y sf (cr 1/14, 10,a).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
