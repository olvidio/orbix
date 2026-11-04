---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dossiers"
titulo: "Elegir los dossiers a configurar"
flujo: "dossiers.perm_dossiers.gestionar.flujo"
preguntas: ["Como elegir los dossiers a configurar en Elegir los dossiers a configurar?"]
pantallas_principales: ["dossiers.pantalla.perm_dossiers"]
fragmentos: ["dossiers.pantalla.perm_dossiers"]
endpoints: ["/src/dossiers/perm_dossiers_data"]
source: "docs/catalogo/dossiers/flujos/perm_dossiers.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir los dossiers a configurar

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir los dossiers a configurar`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir los dossiers a configurar en Elegir los dossiers a configurar?

## Donde Entrar

- Perm Dossiers (`dossiers.pantalla.perm_dossiers`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir los dossiers a configurar

1. Entrar en **perm_dossiers** y elegir **personas**, **ubis** o **actividades**.
2. Aparece la lista de **dossiers seleccionados**.
3. Pulsar **ver o modificar permisos** del tipo que interese.

Referencias tecnicas para verificar la respuesta:
- `/src/dossiers/perm_dossiers_data`

## Pantallas Y Fragmentos Relacionados

- `dossiers.pantalla.perm_dossiers`
- `dossiers.pantalla.perm_dossiers`

## Objetivo

Elegir si se quieren configurar los dossiers de personas, centros/casas o actividades, y abrir los permisos de cada tipo.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
