---
id: "pasarela.tipo_activ_txt.gestionar"
tipo: "capacidad"
modulo: "pasarela"
nombre: "Gestionar Tipo Activ Txt"
entidades: ["TipoActivTxt"]
acciones: ["obtener_datos"]
endpoints: ["/src/pasarela/tipo_activ_txt_data"]
pantallas: ["frontend/pasarela/controller/activacion_ajax.php", "frontend/pasarela/controller/contribucion_no_duerme_ajax.php", "frontend/pasarela/controller/contribucion_reserva_ajax.php", "frontend/pasarela/controller/nombre_ajax.php"]
casos_uso: ["src\\pasarela\\application\\TipoActivTxtData"]
tags: ["activ", "data", "pasarela", "tipo", "tipo_activ_txt", "txt"]
estado_revision: "generado"
---

# Gestionar Tipo Activ Txt

Propuesta generada automaticamente a partir de endpoints con prefijo comun `tipo_activ_txt`.

## Objetivo Funcional

Gestiona TipoActivTxt. Devuelve el texto descriptivo (sfsv asistentes actividad) para un id_tipo_activ. Lo consumen los formularios form_modificar desde el frontend para mostrar a qué tipo de actividad corresponde la fila editada.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/pasarela/tipo_activ_txt_data`

## Pantallas Relacionadas

- `frontend/pasarela/controller/activacion_ajax.php`
- `frontend/pasarela/controller/contribucion_no_duerme_ajax.php`
- `frontend/pasarela/controller/contribucion_reserva_ajax.php`
- `frontend/pasarela/controller/nombre_ajax.php`

## Casos De Uso Detectados

- `src\pasarela\application\TipoActivTxtData`

## Pistas Desde Endpoints

- Devuelve el texto descriptivo (`sfsv asistentes actividad`) para un `id_tipo_activ`. Lo consumen los formularios `form_modificar` desde el frontend para mostrar a qué tipo de actividad corresponde la fila editada.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
