---
id: "dossiers.dossiers_ver_pantalla.gestionar"
tipo: "capacidad"
modulo: "dossiers"
nombre: "Gestionar Dossiers Ver Pantalla"
entidades: ["DossiersVerPantalla"]
acciones: ["obtener_datos"]
endpoints: ["/src/dossiers/dossiers_ver_pantalla_data"]
pantallas: ["frontend/dossiers/controller/dossiers_ver.php"]
casos_uso: ["src\\dossiers\\application\\DossiersVerPantallaData"]
tags: ["data", "dossiers", "dossiers_ver_pantalla", "pantalla", "ver"]
estado_revision: "generado"
---

# Gestionar Dossiers Ver Pantalla

Propuesta generada automaticamente a partir de endpoints con prefijo comun `dossiers_ver_pantalla`.

## Objetivo Funcional

Gestiona DossiersVerPantalla. Cuerpo de dossiers_ver: datos de cabecera + lista o ficha. El backend NO firma URLs: devuelve *_link_spec ({path, query}) que firma el frontend. En modo ficha, ficha_segmentos mezcla: - Segmentos html ya generados por los Select_* (TODO: refactorizar para que tampoco lleven HTML/HashFront desde src/). - Segmentos datos_tabla con datos puros (action_tabla_link_spec, ins_traslado_link_spec, script_ctx, hash, tabla, permiso) que el frontend compone con HashFront, Lista y el script JS de DatosTablaRepo.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/dossiers/dossiers_ver_pantalla_data`

## Pantallas Relacionadas

- `frontend/dossiers/controller/dossiers_ver.php`

## Casos De Uso Detectados

- `src\dossiers\application\DossiersVerPantallaData`

## Pistas Desde Endpoints

- Cuerpo de dossiers_ver: datos de cabecera + lista o ficha. El backend NO firma URLs: devuelve `*_link_spec` ({path, query}) que firma el frontend. En modo ficha, `ficha_segmentos` mezcla: - Segmentos `html` ya generados por los `Select_*` (TODO: refactorizar para que tampoco lleven HTML/HashFront desde `src/`). - Segmentos `datos_tabla` con datos puros (`action_tabla_link_spec`, `ins_traslado_link_spec`, `script_ctx`, `hash`, `tabla`, `permiso`) que el frontend compone con HashFront, Lista y el script JS de `DatosTablaRepo`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
