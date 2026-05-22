-- Vista av_actividades en esquemas región STGR (solo actividades publicadas de la región).
CREATE OR REPLACE VIEW "H-H".av_actividades AS
SELECT id_activ, id_tipo_activ, dl_org, nom_activ, id_ubi, desc_activ, f_ini, h_ini, f_fin, h_fin, tipo_horario, precio, num_asistentes, status, observ, nivel_stgr, observ_material, lugar_esp, tarifa, id_repeticion, publicado, id_tabla, plazas, idioma
FROM public.av_actividades_pub
WHERE (av_actividades_pub.dl_org)::text = ANY (
    ARRAY(
        SELECT split_part(d.schema, '-', 2)::varchar
        FROM public.db_idschema d
        WHERE d.id >= 3000
          AND d.id < 4000
          AND d.schema LIKE 'H-%'
          AND d.schema <> 'H-crH'
          AND d.schema NOT LIKE '%v'
          AND d.schema NOT LIKE '%f'
    )::text[]
);

CREATE OR REPLACE VIEW "M-M".av_actividades AS
SELECT id_activ, id_tipo_activ, dl_org, nom_activ, id_ubi, desc_activ, f_ini, h_ini, f_fin, h_fin, tipo_horario, precio, num_asistentes, status, observ, nivel_stgr, observ_material, lugar_esp, tarifa, id_repeticion, publicado, id_tabla, plazas, idioma
FROM public.av_actividades_pub
WHERE (av_actividades_pub.dl_org)::text = ANY (
    ARRAY(
        SELECT split_part(d.schema, '-', 2)::varchar
        FROM public.db_idschema d
        WHERE d.id >= 3000
          AND d.id < 4000
          AND d.schema LIKE 'M-%'
          AND d.schema <> 'M-crM'
          AND d.schema NOT LIKE '%v'
          AND d.schema NOT LIKE '%f'
    )::text[]
);
