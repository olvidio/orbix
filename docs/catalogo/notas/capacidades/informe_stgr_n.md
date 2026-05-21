---
id: "notas.informe_stgr_n.gestionar"
tipo: "capacidad"
modulo: "notas"
nombre: "Gestionar Informe Stgr N"
entidades: ["InformeStgrNumerarios"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/informe_stgr_n_data"]
pantallas: ["frontend/notas/controller/informe_stgr_n.php"]
casos_uso: ["src\\notas\\application\\InformeStgrNumerarios"]
tags: ["data", "informe", "informe_stgr_n", "n", "notas", "stgr"]
estado_revision: "generado"
---

# Gestionar Informe Stgr N

Propuesta generada automaticamente a partir de endpoints con prefijo comun `informe_stgr_n`.

## Objetivo Funcional

Gestiona InformeStgrNumerarios. Calcula el informe anual STGR de "numerarios" (puntos 1..18 + x). Encapsula el uso de src\notas\application\legacy\Resumen (legacy) para que los controllers del frontend no importen la clase legacy directamente. Devuelve un array neutro {res, textos, curso_txt} listo para renderizado.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/notas/informe_stgr_n_data`

## Pantallas Relacionadas

- `frontend/notas/controller/informe_stgr_n.php`

## Casos De Uso Detectados

- `src\notas\application\InformeStgrNumerarios`

## Pistas Desde Endpoints

- Calcula el informe anual STGR de "numerarios" (puntos 1..18 + `x`). Encapsula el uso de `src\notas\application\legacy\Resumen` (legacy) para que los controllers del frontend no importen la clase legacy directamente. Devuelve un array neutro `{res, textos, curso_txt}` listo para renderizado.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
