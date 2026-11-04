---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadplazas"
titulo: "Ver el reparto de plazas de un periodo"
flujo: "actividadplazas.gestion_plazas.gestionar.flujo"
preguntas: ["Como ver el reparto de plazas de un periodo en Ver el reparto de plazas de un periodo?", "Como cambiar las plazas de una actividad en Ver el reparto de plazas de un periodo?"]
pantallas_principales: ["actividadplazas.pantalla.gestion_plazas"]
fragmentos: ["actividadplazas.pantalla.gestion_plazas", "actividadplazas.pantalla.plazas_balance_dl"]
endpoints: ["/src/actividadplazas/gestion_plazas_data", "/src/actividadplazas/gestion_plazas_update"]
source: "docs/catalogo/actividadplazas/flujos/gestion_plazas.md"
estado_revision: "generado"
---

# Ayuda IA - Ver el reparto de plazas de un periodo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver el reparto de plazas de un periodo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver el reparto de plazas de un periodo en Ver el reparto de plazas de un periodo?
- Como cambiar las plazas de una actividad en Ver el reparto de plazas de un periodo?

## Donde Entrar

- Gestion Plazas (`actividadplazas.pantalla.gestion_plazas`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver el reparto de plazas de un periodo

1. Abrir **Gestión de plazas**.
2. Elegir el periodo (año y periodo, o un rango de fechas) y pulsar **Buscar**.
3. Aparece un cuadro con las actividades en filas y las delegaciones en columnas.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadplazas/gestion_plazas_data`
- `/src/actividadplazas/gestion_plazas_update`

## Cambiar las plazas de una actividad

1. Localizar la actividad en la tabla.
2. Hacer doble clic en una celda editable: el total, las concedidas (**-c**) o las pedidas (**-p**) de la propia delegación.
3. Escribir el nuevo valor; se guarda al momento.
4. Si la actividad no tiene plazas en el calendario común, sale un aviso: hay que darlas de alta antes.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadplazas/gestion_plazas_data`
- `/src/actividadplazas/gestion_plazas_update`

## Pantallas Y Fragmentos Relacionados

- `actividadplazas.pantalla.gestion_plazas`
- `actividadplazas.pantalla.gestion_plazas`
- `actividadplazas.pantalla.plazas_balance_dl`

## Objetivo

Ver, para un periodo y un tipo de actividad, cuántas plazas tiene cada actividad y cómo se reparten (concedidas y pedidas) entre las delegaciones del grupo, y ajustar esos números desde la propia tabla.

## Errores Documentados

- `no se encuentra la actividad`
- `hay un error, no se ha guardado`
- `Aviso de calendario (la actividad aún no tiene plazas en el calendario común).`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
