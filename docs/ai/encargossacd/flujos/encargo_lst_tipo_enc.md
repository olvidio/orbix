---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Encargo Lst Tipo Enc"
flujo: "encargossacd.encargo_lst_tipo_enc.gestionar.flujo"
preguntas: ["Como obtener datos en Encargo Lst Tipo Enc?"]
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.encargo_ver"]
endpoints: ["/src/encargossacd/encargo_lst_tipo_enc_data"]
source: "docs/catalogo/encargossacd/flujos/encargo_lst_tipo_enc.md"
estado_revision: "generado"
---

# Ayuda IA - Encargo Lst Tipo Enc

Usa este documento para responder preguntas de usuario sobre como trabajar con `Encargo Lst Tipo Enc`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Encargo Lst Tipo Enc?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.encargo_ver`

## Objetivo

Gestiona EncargoLstTipoEnc. Payload de desplegable de tipos de encargo filtrados por prefijo de grupo (id_tipo_enc ~ ^grupo).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
