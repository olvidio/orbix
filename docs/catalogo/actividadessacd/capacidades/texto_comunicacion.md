---
id: "actividadessacd.texto_comunicacion.gestionar"
tipo: "capacidad"
modulo: "actividadessacd"
nombre: "Gestionar Texto Comunicacion"
entidades: ["TextoComunicacion"]
acciones: ["guardar", "obtener_datos"]
endpoints: ["/src/actividadessacd/texto_comunicacion_data", "/src/actividadessacd/texto_comunicacion_guardar"]
pantallas: ["frontend/actividadessacd/controller/com_sacd_txt.php", "frontend/actividadessacd/view/com_sacd_txt.phtml"]
casos_uso: ["src\\actividadessacd\\application\\TextoComunicacionData", "src\\actividadessacd\\application\\TextoComunicacionGuardar"]
tags: ["actividadessacd", "comunicacion", "data", "guardar", "texto", "texto_comunicacion"]
estado_revision: "generado"
---

# Gestionar Texto Comunicacion

Propuesta generada automaticamente a partir de endpoints con prefijo comun `texto_comunicacion`.

## Objetivo Funcional

Gestiona TextoComunicacion. Devuelve el texto de comunicacion (clave, idioma). Upsert/delete del texto de comunicacion (clave, idioma, texto).

## Acciones Detectadas

- `guardar`
- `obtener_datos`

## Endpoints

- `/src/actividadessacd/texto_comunicacion_data`
- `/src/actividadessacd/texto_comunicacion_guardar`

## Pantallas Relacionadas

- `frontend/actividadessacd/controller/com_sacd_txt.php`
- `frontend/actividadessacd/view/com_sacd_txt.phtml`

## Casos De Uso Detectados

- `src\actividadessacd\application\TextoComunicacionData`
- `src\actividadessacd\application\TextoComunicacionGuardar`

## Pistas Desde Endpoints

- Endpoint backend: devuelve el texto de comunicacion (`clave`, `idioma`).
- Endpoint backend: upsert/delete del texto de comunicacion (`clave`, `idioma`, `texto`).

## Errores Conocidos

- `faltan parametros clave / idioma`
- `hay un error, no se ha eliminado el texto`
- `hay un error, no se ha guardado el texto`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
