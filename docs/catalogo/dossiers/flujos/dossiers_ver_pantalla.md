---
id: "dossiers.dossiers_ver_pantalla.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dossiers"
nombre: "Flujo - Gestionar Dossiers Ver Pantalla"
capacidad: "dossiers.dossiers_ver_pantalla.gestionar"
pantallas_principales: []
fragmentos: ["dossiers.pantalla.dossiers_ver"]
acciones: ["obtener_datos"]
endpoints: ["/src/dossiers/dossiers_ver_pantalla_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Dossiers Ver Pantalla

Propuesta generada automaticamente desde la capacidad `dossiers.dossiers_ver_pantalla.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona DossiersVerPantalla. Cuerpo de dossiers_ver: datos de cabecera + lista o ficha. El backend NO firma URLs: devuelve *_link_spec ({path, query}) que firma el frontend. En modo ficha, ficha_segmentos mezcla: - Segmentos html ya generados por los Select_* (TODO: refactorizar para que tampoco lleven HTML/HashFront desde src/). - Segmentos datos_tabla con datos puros (action_tabla_link_spec, ins_traslado_link_spec, script_ctx, hash, tabla, permiso) que el frontend compone con HashFront, Lista y el script JS de DatosTablaRepo.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `dossiers.pantalla.dossiers_ver`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/dossiers/dossiers_ver_pantalla_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
