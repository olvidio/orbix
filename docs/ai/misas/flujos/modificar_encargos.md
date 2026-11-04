---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Elegir la zona de los encargos"
flujo: "misas.modificar_encargos.gestionar.flujo"
preguntas: ["Como elegir la zona de los encargos en Elegir la zona de los encargos?"]
pantallas_principales: ["misas.pantalla.modificar_encargos"]
fragmentos: ["misas.pantalla.modificar_encargos"]
endpoints: ["/src/misas/modificar_encargos_data"]
source: "docs/catalogo/misas/flujos/modificar_encargos.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir la zona de los encargos

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir la zona de los encargos`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir la zona de los encargos en Elegir la zona de los encargos?

## Donde Entrar

- Modificar Encargos (`misas.pantalla.modificar_encargos`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir la zona de los encargos

1. Entrar en **Modificar encargos**.
2. Elegir la zona en **Encargos de la zona** y el **Orden**.
3. Se abre la lista de encargos de esa zona.

Referencias tecnicas para verificar la respuesta:
- `/src/misas/modificar_encargos_data`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.modificar_encargos`
- `misas.pantalla.modificar_encargos`

## Objetivo

Elegir la zona para crear y modificar sus encargos (tareas de misa).

## Errores Documentados

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`
- `orden`
- `prioridad`
- `alfabético`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
