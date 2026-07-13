---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dossiers"
titulo: "Dossiers Ver Pantalla"
flujo: "dossiers.dossiers_ver_pantalla.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["dossiers.pantalla.dossiers_ver"]
endpoints: ["/src/dossiers/dossiers_ver_pantalla_data"]
source: "docs/catalogo/dossiers/flujos/dossiers_ver_pantalla.md"
estado_revision: "generado"
---

# Ayuda IA - Dossiers Ver Pantalla

Usa este documento para responder preguntas de usuario sobre como trabajar con `Dossiers Ver Pantalla`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `dossiers.pantalla.dossiers_ver`

## Objetivo

Abrir y navegar los dossiers de una persona, actividad o ubi: cabecera, relación de carpetas o ficha con widgets embebidos (matrículas, asistentes, certificados, tablas genéricas). Reutilizado desde `home_persona`, `home_ubis`, `actividad_ver` y otras pantallas vía `fnjs_update_div`.

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
