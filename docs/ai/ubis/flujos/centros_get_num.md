---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Consultar o cambiar buzón, pi y cartas"
flujo: "ubis.centros_get_num.gestionar.flujo"
preguntas: ["Como consultar o cambiar buzón, pi y cartas en Consultar o cambiar buzón, pi y cartas?"]
pantallas_principales: ["ubis.pantalla.centros_get_num"]
fragmentos: ["ubis.pantalla.centros_get_num"]
endpoints: ["/src/ubis/centros_get_num"]
source: "docs/catalogo/ubis/flujos/centros_get_num.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar o cambiar buzón, pi y cartas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar o cambiar buzón, pi y cartas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar o cambiar buzón, pi y cartas en Consultar o cambiar buzón, pi y cartas?

## Donde Entrar

- Centros Get Num (`ubis.pantalla.centros_get_num`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar o cambiar buzón, pi y cartas

1. Entrar en **modificar centros** y elegir **pi, cartas, nº buzón**; pulsar **buscar**.
2. Se muestra la lista con el nº de buzón, nº de ejemplares de pi y nº de cartas.
3. Pulsar un centro para editar sus números.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/centros_get_num`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.centros_get_num`
- `ubis.pantalla.centros_get_num`

## Objetivo

Listar los centros de la dl con su nº de buzón, ejemplares de pi y cartas, para editarlos.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
