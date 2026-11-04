---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Elegir la zona de las iniciales"
flujo: "misas.modificar_iniciales_sacd_zona.gestionar.flujo"
preguntas: ["Como elegir la zona de las iniciales en Elegir la zona de las iniciales?"]
pantallas_principales: ["misas.pantalla.modificar_iniciales_sacd_zona"]
fragmentos: ["misas.pantalla.modificar_iniciales_sacd_zona"]
endpoints: ["/src/misas/modificar_iniciales_sacd_zona_data"]
source: "docs/catalogo/misas/flujos/modificar_iniciales_sacd_zona.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir la zona de las iniciales

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir la zona de las iniciales`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir la zona de las iniciales en Elegir la zona de las iniciales?

## Donde Entrar

- Modificar Iniciales Sacd Zona (`misas.pantalla.modificar_iniciales_sacd_zona`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir la zona de las iniciales

1. Entrar en **Iniciales sacd**.
2. Elegir la zona en **Iniciales sacerdotes de la zona**.

Referencias tecnicas para verificar la respuesta:
- `/src/misas/modificar_iniciales_sacd_zona_data`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.modificar_iniciales_sacd_zona`
- `misas.pantalla.modificar_iniciales_sacd_zona`

## Objetivo

Elegir la zona cuyas iniciales de sacerdotes se quieren editar.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
