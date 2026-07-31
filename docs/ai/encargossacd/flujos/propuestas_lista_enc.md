---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Propuestas Lista Enc"
flujo: "encargossacd.propuestas_lista_enc.gestionar.flujo"
preguntas: ["Como obtener datos en Propuestas Lista Enc?"]
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.propuestas_lista_enc"]
endpoints: ["/src/encargossacd/propuestas_lista_enc_data"]
source: "docs/catalogo/encargossacd/flujos/propuestas_lista_enc.md"
estado_revision: "generado"
---

# Ayuda IA - Propuestas Lista Enc

Usa este documento para responder preguntas de usuario sobre como trabajar con `Propuestas Lista Enc`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Propuestas Lista Enc?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. FE llama `propuestas_lista_enc_data` con `filtro_ctr` (opcional).
2. Si `error` → echo del mensaje; si no → echo de `html`.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.propuestas_lista_enc`

## Objetivo

Ver el estado de las propuestas por encargos tras (o durante) la edición staging.

## Errores Documentados

- `Debe crear la tabla de propuestas`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
