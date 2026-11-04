---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Elegir la zona de los encargos visibles por centro"
flujo: "misas.modificar_encargos_centros.gestionar.flujo"
preguntas: ["Como elegir la zona de los encargos visibles por centro en Elegir la zona de los encargos visibles por centro?"]
pantallas_principales: ["misas.pantalla.modificar_encargos_centros"]
fragmentos: ["misas.pantalla.modificar_encargos_centros"]
endpoints: ["/src/misas/modificar_encargos_centros_data"]
source: "docs/catalogo/misas/flujos/modificar_encargos_centros.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir la zona de los encargos visibles por centro

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir la zona de los encargos visibles por centro`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir la zona de los encargos visibles por centro en Elegir la zona de los encargos visibles por centro?

## Donde Entrar

- Modificar Encargos Centros (`misas.pantalla.modificar_encargos_centros`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir la zona de los encargos visibles por centro

1. Entrar en **Encargos centro**.
2. Elegir la zona en **Encargos de la zona**.
3. Se abre la lista de encargos visibles por centro.

Referencias tecnicas para verificar la respuesta:
- `/src/misas/modificar_encargos_centros_data`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.modificar_encargos_centros`
- `misas.pantalla.modificar_encargos_centros`

## Objetivo

Elegir la zona para decidir qué encargos ve cada centro.

## Errores Documentados

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
