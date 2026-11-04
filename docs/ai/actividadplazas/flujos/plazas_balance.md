---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadplazas"
titulo: "Comparar con otra delegación"
flujo: "actividadplazas.plazas_balance.gestionar.flujo"
preguntas: ["Como comparar con otra delegación en Comparar con otra delegación?"]
pantallas_principales: ["actividadplazas.pantalla.plazas_balance_dl"]
fragmentos: ["actividadplazas.pantalla.plazas_balance_dl"]
endpoints: ["/src/actividadplazas/plazas_balance_data"]
source: "docs/catalogo/actividadplazas/flujos/plazas_balance.md"
estado_revision: "generado"
---

# Ayuda IA - Comparar con otra delegación

Usa este documento para responder preguntas de usuario sobre como trabajar con `Comparar con otra delegación`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como comparar con otra delegación en Comparar con otra delegación?

## Donde Entrar

- Plazas Balance Dl (`actividadplazas.pantalla.plazas_balance_dl`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Comparar con otra delegación

1. En **Balance de plazas**, elegir en el desplegable la delegación con la que comparar.
2. Debajo aparece la tabla: arriba, las plazas que se conceden mutuamente las dos delegaciones, y por actividad las concedidas y las libres.
3. Las celdas de la propia delegación se pueden cambiar con doble clic; se guarda al momento.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadplazas/plazas_balance_data`

## Pantallas Y Fragmentos Relacionados

- `actividadplazas.pantalla.plazas_balance_dl`
- `actividadplazas.pantalla.plazas_balance_dl`

## Objetivo

Comparar, para un tipo de actividad, las plazas concedidas y libres de cada actividad en la propia delegación frente a otra delegación elegida.

## Errores Documentados

- `falta parametro dl`
- `no se puede comparar una dl consigo misma`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
