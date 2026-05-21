---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "pasarela"
titulo: "Contribucion No Duerme"
flujo: "pasarela.contribucion_no_duerme.gestionar.flujo"
preguntas: ["Como consultar el listado en Contribucion No Duerme?"]
pantallas_principales: []
fragmentos: ["pasarela.pantalla.contribucion_no_duerme_ajax"]
endpoints: ["/src/pasarela/contribucion_no_duerme_lista"]
source: "docs/catalogo/pasarela/flujos/contribucion_no_duerme.md"
estado_revision: "generado"
---

# Ayuda IA - Contribucion No Duerme

Usa este documento para responder preguntas de usuario sobre como trabajar con `Contribucion No Duerme`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar el listado en Contribucion No Duerme?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Referencias tecnicas para verificar la respuesta:
- `/src/pasarela/contribucion_no_duerme_lista`

## Pantallas Y Fragmentos Relacionados

- `pasarela.pantalla.contribucion_no_duerme_ajax`

## Objetivo

Gestiona ContribucionNoDuermeLista. Devuelve el listado del parámetro contribucion_no_duerme listo para serializar. Estructura: {default, excepciones: [{id_tipo_activ, etiqueta, valor}]}.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
