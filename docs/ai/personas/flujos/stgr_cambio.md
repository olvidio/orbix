---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "personas"
titulo: "Cambiar el nivel stgr de una persona"
flujo: "personas.stgr_cambio.gestionar.flujo"
preguntas: ["Como cambiar el nivel stgr de una persona en Cambiar el nivel stgr de una persona?"]
pantallas_principales: ["personas.pantalla.stgr_cambio"]
fragmentos: ["personas.pantalla.stgr_cambio"]
endpoints: ["/src/personas/stgr_cambio_data"]
source: "docs/catalogo/personas/flujos/stgr_cambio.md"
estado_revision: "generado"
---

# Ayuda IA - Cambiar el nivel stgr de una persona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cambiar el nivel stgr de una persona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como cambiar el nivel stgr de una persona en Cambiar el nivel stgr de una persona?

## Donde Entrar

- Cambio nivel STGR (`personas.pantalla.stgr_cambio`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Cambiar el nivel stgr de una persona

1. Marcar exactamente una persona en la lista y pulsar **modificar stgr**.
2. Se abre «Cambiar el stgr» con su nombre y el desplegable de niveles: Bienio, Cuadrienio Año I, Cuadrienio Año II-IV, Repaso, centro estudios, Baja temporal, ap, pa, o ad, sin estudios, est. Ecles., bienio-cuadrienio.
3. Elegir el nivel y pulsar **Guardar** (sigue en el flujo «Guardar nivel stgr»).

Referencias tecnicas para verificar la respuesta:
- `/src/personas/stgr_cambio_data`

## Pantallas Y Fragmentos Relacionados

- `personas.pantalla.stgr_cambio`
- `personas.pantalla.stgr_cambio`

## Objetivo

Abrir el formulario para cambiar el nivel de estudios (stgr) de una persona.

## Errores Documentados

- `No existe la clase de la persona`
- `No se encuentra la persona`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
