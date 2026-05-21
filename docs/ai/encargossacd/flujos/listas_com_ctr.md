---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Listas Com Ctr"
flujo: "encargossacd.listas_com_ctr.gestionar.flujo"
preguntas: ["Como obtener datos en Listas Com Ctr?"]
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.listas_com_ctr"]
endpoints: ["/src/encargossacd/listas_com_ctr_data"]
source: "docs/catalogo/encargossacd/flujos/listas_com_ctr.md"
estado_revision: "generado"
---

# Ayuda IA - Listas Com Ctr

Usa este documento para responder preguntas de usuario sobre como trabajar con `Listas Com Ctr`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Listas Com Ctr?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.listas_com_ctr`

## Objetivo

Gestiona ListasComCtr. Datos para la comunicacion a los centros. Sustituye la logica de frontend/encargossacd/controller/listas_com_ctr.php. El modelo de salida replica el consumido por la vista listas_com_ctr.phtml: - array_atn_sacd[nombre_ubi] con titular, suplente, colaboradores y el texto de comunicacion traducido al idioma del idioma actual. - origen_txt cabecera de emisor y lugar_fecha pie.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
