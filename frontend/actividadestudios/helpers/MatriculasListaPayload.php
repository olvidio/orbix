<?php

declare(strict_types=1);

namespace frontend\actividadestudios\helpers;

use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;

final class MatriculasListaPayload
{
    /**
     * @param array<string, mixed> $payload
     * @return array{titulo: string, msg_err: string, a_valores: array<int|string, mixed>}
     */
    public static function fromPayload(array $payload): array
    {
        return [
            'titulo' => \frontend\shared\helpers\PayloadCoercion::string($payload['titulo'] ?? ''),
            'msg_err' => \frontend\shared\helpers\PayloadCoercion::string($payload['msg_err'] ?? ''),
            'a_valores' => ActividadesListaSupport::datos($payload['a_valores'] ?? []),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{titulo: string, msg_err: string, aviso: string, a_valores: array<int|string, mixed>}
     */
    public static function fromPayloadOtrasR(array $payload): array
    {
        return [
            'titulo' => \frontend\shared\helpers\PayloadCoercion::string($payload['titulo'] ?? ''),
            'msg_err' => \frontend\shared\helpers\PayloadCoercion::string($payload['msg_err'] ?? ''),
            'aviso' => \frontend\shared\helpers\PayloadCoercion::string($payload['aviso'] ?? ''),
            'a_valores' => ActividadesListaSupport::datos($payload['a_valores'] ?? []),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{msg_err: string, aviso: string, a_valores: array<int|string, mixed>}
     */
    public static function fromPayloadPendientes(array $payload): array
    {
        return [
            'msg_err' => \frontend\shared\helpers\PayloadCoercion::string($payload['msg_err'] ?? ''),
            'aviso' => \frontend\shared\helpers\PayloadCoercion::string($payload['aviso'] ?? ''),
            'a_valores' => ActividadesListaSupport::datos($payload['a_valores'] ?? []),
        ];
    }
}
