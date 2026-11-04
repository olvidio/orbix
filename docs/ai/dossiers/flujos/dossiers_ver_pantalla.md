---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dossiers"
titulo: "Abrir un dossier"
flujo: "dossiers.dossiers_ver_pantalla.gestionar.flujo"
preguntas: ["Como abrir un dossier en Abrir un dossier?"]
pantallas_principales: ["dossiers.pantalla.dossiers_ver"]
fragmentos: ["dossiers.pantalla.dossiers_ver"]
endpoints: ["/src/dossiers/dossiers_ver_pantalla_data"]
source: "docs/catalogo/dossiers/flujos/dossiers_ver_pantalla.md"
estado_revision: "generado"
---

# Ayuda IA - Abrir un dossier

Usa este documento para responder preguntas de usuario sobre como trabajar con `Abrir un dossier`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como abrir un dossier en Abrir un dossier?

## Donde Entrar

- Dossiers Ver (`dossiers.pantalla.dossiers_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Abrir un dossier

1. Desde la ficha, pulsar dossiers y luego la carpeta que interesa.
2. Se muestra su contenido; según el permiso se puede solo consultar o también modificar.
3. Usar los enlaces de la cabecera para volver a la relación de dossiers o a la ficha de origen.

Referencias tecnicas para verificar la respuesta:
- `/src/dossiers/dossiers_ver_pantalla_data`

## Pantallas Y Fragmentos Relacionados

- `dossiers.pantalla.dossiers_ver`
- `dossiers.pantalla.dossiers_ver`

## Objetivo

Abrir y recorrer los dossiers de una persona, actividad o centro/casa: cabecera, relación de carpetas y el contenido de cada una (matrículas, asistentes, certificados, tablas).

## Errores Documentados

- `clase_info invalida`
- `No encuentro a nadie con id_nom: <id>`
- `ubi no encontrada`
- `actividad no encontrada`
- `pau desconocido`
- `El dossier <id> no está disponible (sin widget ni datos configurados en d_tipos_dossiers).`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
