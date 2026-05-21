---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Ctr Get Select"
flujo: "encargossacd.ctr_get_select.gestionar.flujo"
preguntas: ["Como obtener datos en Ctr Get Select?"]
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.ctr_ficha", "encargossacd.pantalla.encargo_ver"]
endpoints: ["/src/encargossacd/ctr_get_select_data"]
source: "docs/catalogo/encargossacd/flujos/ctr_get_select.md"
estado_revision: "generado"
---

# Ayuda IA - Ctr Get Select

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ctr Get Select`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Ctr Get Select?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.ctr_ficha`
- `encargossacd.pantalla.encargo_ver`

## Objetivo

Gestiona EncargoCtrSelect. Payload JSON para el desplegable de centros segun filtro (y zona opcional). Devuelve el contrato estandar definido en refactor.md (id, name, opciones, selected, blanco, val_blanco, action) para que el frontend monte el <select> con fnjs_construir_desplegable (o el modelo frontend/encargossacd/model/DesplCentros). Importante: esta clase vive en capa application y por tanto **no** puede instanciar frontend\shared\web\Desplegable (ver refactor.md).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
