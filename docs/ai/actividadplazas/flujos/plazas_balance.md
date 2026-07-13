---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadplazas"
titulo: "Plazas Balance"
flujo: "actividadplazas.plazas_balance.gestionar.flujo"
preguntas: ["Como obtener datos en Plazas Balance?"]
pantallas_principales: []
fragmentos: ["actividadplazas.pantalla.plazas_balance_dl"]
endpoints: ["/src/actividadplazas/plazas_balance_data"]
source: "docs/catalogo/actividadplazas/flujos/plazas_balance.md"
estado_revision: "generado"
---

# Ayuda IA - Plazas Balance

Usa este documento para responder preguntas de usuario sobre como trabajar con `Plazas Balance`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Plazas Balance?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. En **Balance de plazas**, elegir la delegación a comparar en el desplegable.
2. El sistema carga el HTML del grid en `#comparativa` vía `plazas_balance_dl.php`.
3. Ese fragmento obtiene los datos de `plazas_balance_data` (`dlA` = mi dl, `dlB` = la elegida):
4. Las celdas de mi dl son editables (doble clic → `gestion_plazas_update`).

Referencias tecnicas para verificar la respuesta:
- `/src/actividadplazas/plazas_balance_data`

## Pantallas Y Fragmentos Relacionados

- `actividadplazas.pantalla.plazas_balance_dl`

## Objetivo

Comparar, para un tipo de actividad, cuántas plazas concedidas y libres tiene cada actividad en mi delegación frente a otra delegación elegida en el desplegable.

## Errores Documentados

- `falta parametro dl`
- `no se puede comparar una dl consigo misma`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
