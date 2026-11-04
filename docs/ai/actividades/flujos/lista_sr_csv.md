---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Ver o descargar el listado de sr"
flujo: "actividades.lista_sr_csv.gestionar.flujo"
preguntas: ["Como ver o descargar el listado de sr en Ver o descargar el listado de sr?"]
pantallas_principales: ["actividades.pantalla.lista_sr_csv"]
fragmentos: ["actividades.pantalla.lista_sr_csv"]
endpoints: ["/src/actividades/lista_sr_csv_datos"]
source: "docs/catalogo/actividades/flujos/lista_sr_csv.md"
estado_revision: "generado"
---

# Ayuda IA - Ver o descargar el listado de sr

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver o descargar el listado de sr`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver o descargar el listado de sr en Ver o descargar el listado de sr?

## Donde Entrar

- Listado SR (tabla/CSV) (`actividades.pantalla.lista_sr_csv`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver o descargar el listado de sr

1. Desde el formulario de **listado csv**, elegir ver en pantalla o descargar CSV.
2. Si se descarga, abrir el fichero con una hoja de cálculo.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/lista_sr_csv_datos`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.lista_sr_csv`
- `actividades.pantalla.lista_sr_csv`

## Objetivo

Ver el listado de actividades de San Rafael o descargarlo como CSV.

## Errores Documentados

- `hay un error, no se ha guardado la preferencia (en pref_error, no bloquea listado)`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
