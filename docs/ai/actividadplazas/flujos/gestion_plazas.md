---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadplazas"
titulo: "Gestion Plazas"
flujo: "actividadplazas.gestion_plazas.gestionar.flujo"
preguntas: ["Como obtener datos en Gestion Plazas?", "Como crear o modificar en Gestion Plazas?"]
pantallas_principales: []
fragmentos: ["actividadplazas.pantalla.gestion_plazas", "actividadplazas.pantalla.plazas_balance_dl"]
endpoints: ["/src/actividadplazas/gestion_plazas_data", "/src/actividadplazas/gestion_plazas_update"]
source: "docs/catalogo/actividadplazas/flujos/gestion_plazas.md"
estado_revision: "generado"
---

# Ayuda IA - Gestion Plazas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Gestion Plazas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Gestion Plazas?
- Como crear o modificar en Gestion Plazas?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Elegir el periodo (año + periodo, o rango de fechas) y pulsar **Buscar**.
2. El sistema carga el cuadro desde `gestion_plazas_data` (actividades × delegaciones).

Referencias tecnicas para verificar la respuesta:
- `/src/actividadplazas/gestion_plazas_data`

## Crear o modificar

1. Localizar la actividad en la tabla.
2. Doble clic en una celda editable (total, concedidas `-c` o pedidas `-p` de mi delegación).
3. Escribir el nuevo valor; se guarda al instante vía `gestion_plazas_update`.
4. Si la actividad no tiene plazas en el calendario común, se muestra el aviso para darlas de alta antes.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadplazas/gestion_plazas_update`

## Pantallas Y Fragmentos Relacionados

- `actividadplazas.pantalla.gestion_plazas`
- `actividadplazas.pantalla.plazas_balance_dl`

## Objetivo

Ver, para un periodo y tipo de actividad, cuántas plazas tiene cada actividad y cómo se reparten (concedidas/pedidas) entre las delegaciones del grupo, y ajustar esos valores desde la propia tabla.

## Errores Documentados

- `no se encuentra la actividad`
- `hay un error, no se ha guardado`
- `Aviso de calendario (la actividad aún no tiene plazas en el calendario común).`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
