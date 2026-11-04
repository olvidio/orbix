---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Buscar un encargo"
flujo: "encargossacd.encargo_select.gestionar.flujo"
preguntas: ["Como buscar un encargo en Buscar un encargo?", "Como crear un encargo en Buscar un encargo?"]
pantallas_principales: ["encargossacd.pantalla.encargo_select"]
fragmentos: ["encargossacd.pantalla.encargo_select"]
endpoints: ["/src/encargossacd/encargo_select_data"]
source: "docs/catalogo/encargossacd/flujos/encargo_select.md"
estado_revision: "generado"
---

# Ayuda IA - Buscar un encargo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Buscar un encargo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como buscar un encargo en Buscar un encargo?
- Como crear un encargo en Buscar un encargo?

## Donde Entrar

- Encargo Select (`encargossacd.pantalla.encargo_select`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Buscar un encargo

1. Entrar en **ver encargo**.
2. Rellenar los criterios de **buscar un encargo** y pulsar **buscar**.
3. Pulsar el encargo en la tabla para abrirlo (o usar **Buscar en todas las columnas**).

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/encargo_select_data`

## Crear un encargo

1. Pulsar **añadir encargo**.
2. Rellenar el formulario (ver «Modificar un encargo») y guardar.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/encargo_select_data`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.encargo_select`
- `encargossacd.pantalla.encargo_select`

## Objetivo

Buscar los encargos existentes y acceder a su ficha, o crear uno nuevo.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
