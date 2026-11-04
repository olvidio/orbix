---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "zonassacd"
titulo: "Consultar los sacerdotes por zona"
flujo: "zonassacd.zona_sacd_lista_tot.gestionar.flujo"
preguntas: ["Como consultar los sacerdotes por zona en Consultar los sacerdotes por zona?"]
pantallas_principales: ["zonassacd.pantalla.zona_sacd_lista_ajax"]
fragmentos: []
endpoints: ["/src/zonassacd/zona_sacd_lista_tot"]
source: "docs/catalogo/zonassacd/flujos/zona_sacd_lista_tot.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar los sacerdotes por zona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar los sacerdotes por zona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar los sacerdotes por zona en Consultar los sacerdotes por zona?

## Donde Entrar

- Zona Sacd Lista Ajax (`zonassacd.pantalla.zona_sacd_lista_ajax`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar los sacerdotes por zona

1. Entrar en **lista sacd-zona**.
2. Se muestra una fila por cada sacerdote y zona asignada.

Referencias tecnicas para verificar la respuesta:
- `/src/zonassacd/zona_sacd_lista_tot`

## Pantallas Y Fragmentos Relacionados

- `zonassacd.pantalla.zona_sacd_lista_ajax`

## Objetivo

Ver todos los sacerdotes con la zona o zonas que tienen asignadas.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
