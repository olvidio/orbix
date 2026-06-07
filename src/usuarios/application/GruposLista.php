<?php

namespace src\usuarios\application;

use src\usuarios\domain\contracts\GrupoRepositoryInterface;

class GruposLista
{
    public function __construct(
        private GrupoRepositoryInterface $grupoRepository,
    ) {
    }

    /**
     * @return array{a_cabeceras: list<mixed>, a_botones: list<array<string, string>>, a_valores: array<int, array<int|string, mixed>>}
     */
    public function execute(string $username): array
    {
        $aWhere = [];
        $aOperador = [];
        if ($username !== '') {
            $aWhere['usuario'] = $username;
            $aOperador['usuario'] = 'sin_acentos';
        } else {
            $aWhere['id_usuario'] = '^5';
            $aOperador['id_usuario'] = '~';
        }
        $aWhere['_ordre'] = 'usuario';

        $cGrupos = $this->grupoRepository->getGrupos($aWhere, $aOperador);

        $a_cabeceras = [
            _('grupo'),
            ['name' => 'accion', 'formatter' => 'clickFormatter'],
        ];

        $a_botones[] = ['txt' => _('borrar'), 'click' => 'fnjs_eliminar(this.form)'];

        $a_valores = [];
        $i = 0;
        foreach ($cGrupos as $oGrupo) {
            $i++;
            $id_usuario = $oGrupo->getId_usuario();
            $usuario = $oGrupo->getUsuarioAsString();

            $a_valores[$i]['sel'] = "$id_usuario#";
            $a_valores[$i][1] = $usuario;
            $a_valores[$i][2] = [
                'link_spec' => [
                    'path' => 'frontend/usuarios/controller/grupo_form.php',
                    'query' => ['quien' => 'grupo', 'id_usuario' => $id_usuario],
                ],
                'valor' => 'editar',
            ];
        }

        return [
            'a_cabeceras' => $a_cabeceras,
            'a_botones' => $a_botones,
            'a_valores' => $a_valores,
        ];
    }
}
