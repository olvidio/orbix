<?php

namespace src\shared\domain\contracts;

/**
 * Patrón Unit of Work para gestionar transacciones y eventos de dominio
 *
 * Permite ejecutar operaciones dentro de una transacción y despachar
 * automáticamente los eventos de dominio al finalizar con éxito.
 */
interface UnitOfWorkInterface
{
    /**
     * Ejecuta una operación dentro de una transacción
     *
     * Si la operación tiene éxito, se hace commit y se despachan los eventos
     * Si falla, se hace rollback y no se despachan eventos
     *
     * @param callable $operation Función que contiene las operaciones a ejecutar
     * @return mixed El resultado de la operación
     * @throws \Exception Si la operación falla
     */
    public function execute(callable $operation): mixed;

    /**
     * Registra una entidad para despachar sus eventos al finalizar la transacción
     *
     * @param object $entity Entidad con eventos de dominio pendientes
     * @return void
     */
    public function registerEntity(object $entity): void;

    /**
     * Inicia una transacción manualmente
     *
     * @return void
     */
    public function beginTransaction(): void;

    /**
     * Confirma la transacción y despacha los eventos
     *
     * @return void
     */
    public function commit(): void;

    /**
     * Revierte la transacción sin despachar eventos
     *
     * @return void
     */
    public function rollback(): void;
}
