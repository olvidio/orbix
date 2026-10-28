<?php

namespace src\personas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de las personas (`PersonaDl` y subtipos, `PersonaEx`, `PersonaPub`, `PersonaSacd`) por los que se
 * puede buscar u ordenar. El valor es el nombre del campo en la entidad (= columna). Lo comparten todos los
 * repositorios de personas; cada tabla/vista solo tiene los que le corresponden (`id_auto` solo `Ex`,
 * `edad` y `profesor_stgr` solo `Ex`/`Pub`/`Sacd`, `id_ctr` no está en `Ex`).
 * Se omite `publicado_para` (jsonb: no se filtra por él).
 */
enum PersonaField: string implements CriteriaField
{
    case IdSchema = 'id_schema';
    case IdAuto = 'id_auto';
    case IdNom = 'id_nom';
    case IdTabla = 'id_tabla';
    case Dl = 'dl';
    case Sacd = 'sacd';
    case Trato = 'trato';
    case Nom = 'nom';
    case Nx1 = 'nx1';
    case Apellido1 = 'apellido1';
    case Nx2 = 'nx2';
    case Apellido2 = 'apellido2';
    case FNacimiento = 'f_nacimiento';
    case IdiomaPreferido = 'idioma_preferido';
    case Situacion = 'situacion';
    case FSituacion = 'f_situacion';
    case ApelFam = 'apel_fam';
    case Inc = 'inc';
    case FInc = 'f_inc';
    case NivelStgr = 'nivel_stgr';
    case Eap = 'eap';
    case Observ = 'observ';
    case IdCtr = 'id_ctr';
    case LugarNacimiento = 'lugar_nacimiento';
    case Edad = 'edad';
    case ProfesorStgr = 'profesor_stgr';
}
