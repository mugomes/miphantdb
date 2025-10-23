<?php
// Copyright (C) 2025 Murilo Gomes Julio
// SPDX-License-Identifier: MIT

// Site: https://github.com/mugomes

namespace MiPhantDB;

class update extends database
{
    private array $sUpdate = [];

    public function add(string $nome, string $valor = '?')
    {
        $this->sUpdate[] = ['coluna' => $nome, 'valor' => $valor];
        return $this;
    }

    public function update() {
        try {
            $valores = '';
            foreach ($this->sUpdate as $row) {
                if (empty($this->sPreparado)) {
                    $valores .= sprintf("%s='%s', ", $row['coluna'], $row['valor']);
                } else {
                    $valores .= sprintf('%s=%s, ', $row['coluna'], $row['valor']);
                }
            }

            $valores = rtrim($valores, ', ');

            $sql = $this->getWhere() . $this->getOrderBy() . $this->getLimit();

            $txt = sprintf('Update %s SET %s %s', $this->getTable(), $valores, $sql);

            if (empty($this->sPreparado)) {
                $this->sResult = mysqli_query($this->sConecta, $txt);
            } else {
                $sTipo = '';
                $sValores = [];
                foreach ($this->sPreparado as $row) {
                    $sTipo .= $row[0];
                    $sValores[] = $row[1];
                }

                if ($this->sResult = mysqli_prepare($this->sConecta, $txt)) {
                    mysqli_stmt_bind_param($this->sResult, $sTipo, ...$sValores);
                    mysqli_stmt_execute($this->sResult);
                }
            }

            $this->sFechaResult = false;
        } catch (\mysqli_sql_exception $ex) {
            $this->log($ex->__toString());
        }
    }
}
