---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Cuadricula"
flujo: "misas.cuadricula.gestionar.flujo"
preguntas: ["Como crear o modificar en Cuadricula?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/misas/cuadricula_update"]
source: "docs/catalogo/misas/flujos/cuadricula.md"
estado_revision: "generado"
---

# Ayuda IA - Cuadricula

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cuadricula`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar en Cuadricula?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Referencias tecnicas para verificar la respuesta:
- `/src/misas/cuadricula_update`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Gestiona Cuadricula. Use case del endpoint cuadricula_update (migracion de apps/misas/controller/cuadricula_update.php al Slice 6a). Hace dos cosas en la misma transaccion logica: 1. Upsert / delete de un EncargoDia para un dia + encargo concretos, en funcion de key (si esta vacio, se borra; si trae id_nom, se guarda o actualiza). 2. Recalcula el bloque meta que la UI usa para pintar colores y textos (disponibilidad del sacd anterior y del nuevo, numero de misas del dia, conflictos con primera hora, etc.). El codigo es una traduccion casi literal del controlador original para minimizar riesgo de regresion: la logica de negocio en si no cambia en este slice; lo unico que cambia es donde vive.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
