---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Ctr Get Ficha"
flujo: "encargossacd.ctr_get_ficha.gestionar.flujo"
preguntas: ["Como obtener datos en Ctr Get Ficha?"]
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.ctr_get_ficha"]
endpoints: ["/src/encargossacd/ctr_get_ficha_data"]
source: "docs/catalogo/encargossacd/flujos/ctr_get_ficha.md"
estado_revision: "generado"
---

# Ayuda IA - Ctr Get Ficha

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ctr Get Ficha`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Ctr Get Ficha?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.ctr_get_ficha`

## Objetivo

Gestiona CtrGetFicha. Lectura de la ficha de atencion sacerdotal de un centro. Puerto del antiguo frontend/encargossacd/controller/ctr_get_ficha.php. Devuelve arrays planos/estructurados para que el controlador frontend arme frontend\shared\web\Desplegable y la HTML sin instanciar nada de src\.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
