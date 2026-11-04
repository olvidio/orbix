---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Hacer visible un encargo para un centro"
flujo: "misas.ver_encargos_centros.gestionar.flujo"
preguntas: ["Como hacer visible un encargo para un centro en Hacer visible un encargo para un centro?"]
pantallas_principales: ["misas.pantalla.ver_encargos_centros"]
fragmentos: ["misas.pantalla.ver_encargos_centros"]
endpoints: ["/src/misas/ver_encargos_centros_data"]
source: "docs/catalogo/misas/flujos/ver_encargos_centros.md"
estado_revision: "generado"
---

# Ayuda IA - Hacer visible un encargo para un centro

Usa este documento para responder preguntas de usuario sobre como trabajar con `Hacer visible un encargo para un centro`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como hacer visible un encargo para un centro en Hacer visible un encargo para un centro?

## Donde Entrar

- Ver Encargos Centros (`misas.pantalla.ver_encargos_centros`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Hacer visible un encargo para un centro

1. En **Encargos centro**, elegir la zona.
2. Pulsar **nuevo**; en **Zona del encargo**, **Encargo** y **Centro** elegir los valores y pulsar **Grabar**.
3. Para modificarlo, pulsarlo y **Grabar**; para quitarlo, **Eliminar encargo**.
4. Si falta encargo o centro aparece «Falta encargo o centro».

Referencias tecnicas para verificar la respuesta:
- `/src/misas/ver_encargos_centros_data`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.ver_encargos_centros`
- `misas.pantalla.ver_encargos_centros`

## Objetivo

Ver y modificar qué encargos de la zona aparecen en el plan de cada centro.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
