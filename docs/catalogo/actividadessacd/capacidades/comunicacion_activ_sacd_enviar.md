---
id: "actividadessacd.comunicacion_activ_sacd_enviar.gestionar"
tipo: "capacidad"
modulo: "actividadessacd"
nombre: "Gestionar Comunicacion Activ Sacd Enviar"
entidades: ["ComunicacionActividadesSacdEnviar"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadessacd/comunicacion_activ_sacd_enviar"]
pantallas: ["frontend/actividadessacd/controller/com_sacd_activ_periodo.php", "frontend/actividadessacd/view/com_sacd_activ_periodo.phtml"]
casos_uso: ["src\\actividadessacd\\application\\ComunicacionActividadesSacdEnviar"]
tags: ["activ", "actividadessacd", "comunicacion", "comunicacion_activ_sacd_enviar", "enviar", "sacd"]
estado_revision: "generado"
---

# Gestionar Comunicacion Activ Sacd Enviar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `comunicacion_activ_sacd_enviar`.

## Objetivo Funcional

Gestiona ComunicacionActividadesSacdEnviar. Encola los mails de comunicacion de actividades a los sacd y al ctr del sacd, con copia al jefe de calendario.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/actividadessacd/comunicacion_activ_sacd_enviar`

## Pantallas Relacionadas

- `frontend/actividadessacd/controller/com_sacd_activ_periodo.php`
- `frontend/actividadessacd/view/com_sacd_activ_periodo.phtml`

## Casos De Uso Detectados

- `src\actividadessacd\application\ComunicacionActividadesSacdEnviar`

## Pistas Desde Endpoints

- Endpoint backend: encola los mails de comunicacion de actividades a los sacd y al ctr del sacd, con copia al jefe de calendario.

## Errores Conocidos

- `falta determinar un periodo`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
