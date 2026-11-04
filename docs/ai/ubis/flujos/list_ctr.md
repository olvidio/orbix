---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Listar centros o casas"
flujo: "ubis.list_ctr.gestionar.flujo"
preguntas: ["Como listar centros o casas en Listar centros o casas?", "Como trasladar centros o casas a otra delegación en Listar centros o casas?"]
pantallas_principales: ["ubis.pantalla.list_ctr"]
fragmentos: ["ubis.pantalla.list_ctr"]
endpoints: ["/src/ubis/list_ctr_data"]
source: "docs/catalogo/ubis/flujos/list_ctr.md"
estado_revision: "generado"
---

# Ayuda IA - Listar centros o casas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Listar centros o casas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como listar centros o casas en Listar centros o casas?
- Como trasladar centros o casas a otra delegación en Listar centros o casas?

## Donde Entrar

- List Ctr (`ubis.pantalla.list_ctr`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Listar centros o casas

1. Entrar en **listados**.
2. Elegir **Localización** (la propia dl, otras o «otras») y el **Tipo de ubi**: solo centros de n, todos los ctr de la dl, solo centros de agd, de s, de sss+, obras corporativas, casas de retiros y de cv, colegios o resto de casas cdc.
3. Pulsar **listar**.
4. Pulsar un nombre para abrir su ficha; la tabla admite **Buscar en todas las columnas**.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/list_ctr_data`

## Trasladar centros o casas a otra delegación

1. En el listado, marcar los centros o casas.
2. Pulsar el botón de trasladar y elegir la delegación destino.
3. Confirmar con **Trasladar** (o **Cancelar**).

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/list_ctr_data`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.list_ctr`
- `ubis.pantalla.list_ctr`

## Objetivo

Sacar un listado de centros o casas de una delegación o del exterior, con teléfonos y enlaces a la ficha.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
