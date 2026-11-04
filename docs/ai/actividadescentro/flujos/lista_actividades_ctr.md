---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadescentro"
titulo: "Consultar las actividades y sus centros encargados"
flujo: "actividadescentro.lista_actividades_ctr.gestionar.flujo"
preguntas: ["Como consultar las actividades y sus centros encargados en Consultar las actividades y sus centros encargados?"]
pantallas_principales: ["actividadescentro.pantalla.activ_ctr"]
fragmentos: ["actividadescentro.pantalla.activ_ctr"]
endpoints: ["/src/actividadescentro/lista_actividades_ctr_data"]
source: "docs/catalogo/actividadescentro/flujos/lista_actividades_ctr.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar las actividades y sus centros encargados

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar las actividades y sus centros encargados`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar las actividades y sus centros encargados en Consultar las actividades y sus centros encargados?

## Donde Entrar

- Activ Ctr (`actividadescentro.pantalla.activ_ctr`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar las actividades y sus centros encargados

1. Elegir el periodo.
2. Pulsar **buscar**.
3. Se muestra la tabla de actividades con sus centros encargados; según los permisos aparecen los botones para modificar o crear.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadescentro/lista_actividades_ctr_data`

## Pantallas Y Fragmentos Relacionados

- `actividadescentro.pantalla.activ_ctr`
- `actividadescentro.pantalla.activ_ctr`

## Objetivo

Ver las actividades de un periodo con los centros encargados actuales.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
