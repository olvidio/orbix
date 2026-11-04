---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Añadir o editar una telecomunicación"
flujo: "ubis.teleco_editar.gestionar.flujo"
preguntas: ["Como añadir o editar una telecomunicación en Añadir o editar una telecomunicación?"]
pantallas_principales: ["ubis.pantalla.teleco_editar"]
fragmentos: ["ubis.pantalla.teleco_editar"]
endpoints: ["/src/ubis/teleco_editar"]
source: "docs/catalogo/ubis/flujos/teleco_editar.md"
estado_revision: "generado"
---

# Ayuda IA - Añadir o editar una telecomunicación

Usa este documento para responder preguntas de usuario sobre como trabajar con `Añadir o editar una telecomunicación`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como añadir o editar una telecomunicación en Añadir o editar una telecomunicación?

## Donde Entrar

- Teleco Editar (`ubis.pantalla.teleco_editar`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Añadir o editar una telecomunicación

1. En **telecos** pulsar **nuevo**, o marcar un registro y pulsar **modificar**.
2. Elegir el **Tipo teleco** (correo electrónico, fax, página web, teléfono fijo o teléfono móvil); entonces el desplegable **Descripción** se rellena con las opciones de ese tipo (por ejemplo otros, de la casa, dirección, administración, sacd). Escribir el **Número** y, si hace falta, las **Observaciones**.
3. Pulsar **Guardar Cambios**. Vuelve a la ficha y el número aparece junto a los demás teléfonos.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/teleco_editar`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.teleco_editar`
- `ubis.pantalla.teleco_editar`

## Objetivo

Añadir una telecomunicación o modificar una existente.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
