---
id: "notas.informe_stgr_profesores.gestionar"
tipo: "capacidad"
modulo: "notas"
nombre: "Gestionar Informe Stgr Profesores"
entidades: ["InformeStgrProfesores"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/informe_stgr_profesores_data"]
pantallas: ["frontend/notas/controller/informe_stgr_profesores.php"]
casos_uso: ["src\\notas\\application\\InformeStgrProfesores"]
tags: ["data", "informe", "informe_stgr_profesores", "notas", "profesores", "stgr"]
estado_revision: "generado"
---

# Gestionar Informe Stgr Profesores

Propuesta generada automaticamente a partir de endpoints con prefijo comun `informe_stgr_profesores`.

## Objetivo Funcional

Gestiona InformeStgrProfesores. Calcula el informe anual STGR de "profesores" (puntos 36..47). Encapsula el uso de src\notas\application\legacy\Resumen (legacy) para que los controllers del frontend no importen la clase legacy directamente. Devuelve un array neutro {res, textos, curso_txt} listo para renderizado. Tipos de profesor utilizados: 1 Ordinario 2 Extraordinario 3 Adjunto 4 Encargado 5 Ayudante 6 Asociado 0 (todos).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/notas/informe_stgr_profesores_data`

## Pantallas Relacionadas

- `frontend/notas/controller/informe_stgr_profesores.php`

## Casos De Uso Detectados

- `src\notas\application\InformeStgrProfesores`

## Pistas Desde Endpoints

- Calcula el informe anual STGR de "profesores" (puntos 36..47). Encapsula el uso de `src\notas\application\legacy\Resumen` (legacy) para que los controllers del frontend no importen la clase legacy directamente. Devuelve un array neutro `{res, textos, curso_txt}` listo para renderizado. Tipos de profesor utilizados: 1 Ordinario 2 Extraordinario 3 Adjunto 4 Encargado 5 Ayudante 6 Asociado 0 (todos)

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
