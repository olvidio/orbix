-- Huella del contenido académico en la última impresión del acta (tabla padre; las hijas heredan).
SELECT migracion_add_columna_si_no_existe('publicf', 'e_actas', 'hash_impreso', 'varchar(64) NULL');

DO $$
BEGIN
    IF public.migracion_tabla_existe('publicf', 'e_actas_ex')
       AND NOT public.migracion_columna_existe('publicf', 'e_actas_ex', 'hash_impreso') THEN
        PERFORM public.migracion_add_columna_si_no_existe('publicf', 'e_actas_ex', 'hash_impreso', 'varchar(64) NULL');
    END IF;
END $$;
