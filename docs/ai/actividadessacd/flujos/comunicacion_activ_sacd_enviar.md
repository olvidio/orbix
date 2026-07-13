---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadessacd"
titulo: "Comunicacion Activ Sacd Enviar"
flujo: "actividadessacd.comunicacion_activ_sacd_enviar.gestionar.flujo"
preguntas: ["Como ejecutar en Comunicacion Activ Sacd Enviar?"]
pantallas_principales: []
fragmentos: ["actividadessacd.pantalla.com_sacd_activ_periodo"]
endpoints: ["/src/actividadessacd/comunicacion_activ_sacd_enviar"]
source: "docs/catalogo/actividadessacd/flujos/comunicacion_activ_sacd_enviar.md"
estado_revision: "generado"
---

# Ayuda IA - Comunicacion Activ Sacd Enviar

Usa este documento para responder preguntas de usuario sobre como trabajar con `Comunicacion Activ Sacd Enviar`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Comunicacion Activ Sacd Enviar?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Tener un listado generado (flujo de búsqueda previo).
2. Pulsar **enviar mail**.
3. El sistema encola los correos y muestra el resultado.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadessacd/comunicacion_activ_sacd_enviar`

## Pantallas Y Fragmentos Relacionados

- `actividadessacd.pantalla.com_sacd_activ_periodo`

## Objetivo

El usuario pulsa **enviar mail**: el sistema encola los correos de comunicación (uno por sacd con copia al jefe de calendario, y otro para el ctr del sacd si tiene email). Requiere un periodo válido y el jefe de calendario configurado.

## Errores Documentados

- `falta determinar un periodo`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
