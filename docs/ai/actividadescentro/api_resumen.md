---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "actividadescentro"
endpoints: 7
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - actividadescentro

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/actividadescentro/activ_ctr_shell_data`

- Id: `actividadescentro.activ_ctr_shell_data`
- Controller: `src/actividadescentro/infrastructure/ui/http/controllers/activ_ctr_shell_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/actividadescentro/centro_encargado_asignar`

- Id: `actividadescentro.centro_encargado_asignar`
- Controller: `src/actividadescentro/infrastructure/ui/http/controllers/centro_encargado_asignar.php`
- Entrada: `post.id_activ:integer`, `post.id_ubi:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadescentro/centro_encargado_eliminar`

- Id: `actividadescentro.centro_encargado_eliminar`
- Controller: `src/actividadescentro/infrastructure/ui/http/controllers/centro_encargado_eliminar.php`
- Entrada: `post.id_activ:integer`, `post.id_ubi:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadescentro/centro_encargado_reordenar`

- Id: `actividadescentro.centro_encargado_reordenar`
- Controller: `src/actividadescentro/infrastructure/ui/http/controllers/centro_encargado_reordenar.php`
- Entrada: `post.id_activ:integer`, `post.id_ubi:integer`, `post.num_orden:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadescentro/centros_disponibles_data`

- Id: `actividadescentro.centros_disponibles_data`
- Controller: `src/actividadescentro/infrastructure/ui/http/controllers/centros_disponibles_data.php`
- Entrada: `post.f_ini_act:string`, `post.fin:string`, `post.id_activ:integer`, `post.inicio:string`, `post.tipo:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadescentro/centros_encargados_data`

- Id: `actividadescentro.centros_encargados_data`
- Controller: `src/actividadescentro/infrastructure/ui/http/controllers/centros_encargados_data.php`
- Entrada: `post.dl_org:string`, `post.id_activ:integer`, `post.id_tipo_activ:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadescentro/lista_actividades_ctr_data`

- Id: `actividadescentro.lista_actividades_ctr_data`
- Controller: `src/actividadescentro/infrastructure/ui/http/controllers/lista_actividades_ctr_data.php`
- Entrada: `post.empiezamax:string`, `post.empiezamin:string`, `post.periodo:string`, `post.tipo:string`, `post.year:string`
- Respuesta: `standard_envelope_string_data`
