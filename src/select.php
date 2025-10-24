<?php
// Copyright (C) 2025 Murilo Gomes Julio
// SPDX-License-Identifier: MIT

// Site: https://github.com/mugomes

namespace MiPhantDB;

class select extends database
{
    private bool $sDesativarSQLCache = false;
    private array $sColunas = [];

    private array $sRows = [];

    public function disableSQLCache()
    {
        $this->sDesativarSQLCache = true;
        return $this;
    }

    public function innerJoin(string $name)
    {
        $this->sTabelas[] = ' INNER JOIN ' . $this->sPrefix . $name;
        return $this;
    }

    public function column(string $name, string $apelido = '')
    {
        $this->sColunas[] = (empty($apelido)) ? $name : sprintf('%s AS %s', $name, $apelido);
        return $this;
    }

    public function getColunas()
    {
        return implode(', ', $this->sColunas);
    }

    public function select()
    {
        try {
            $txt = 'SELECT ';
            $txt .= ($this->sDesativarSQLCache) ? '' : 'SQL_CACHE ';
            $txt .= (empty($this->getColunas())) ? '* ' : sprintf('%s ', $this->getColunas());
            $txt .= 'FROM ' . $this->getTable();
            $txt .= $this->getWhere();
            $txt .= $this->getOrderBy();
            $txt .= $this->getLimit();

            if (empty($this->sPreparado)) {
                if ($this->sResult = mysqli_query($this->sConecta, $txt)) {
                    $this->sFechaResult = true;
                } else {
                    $this->sFechaResult = false;
                }
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
        } catch (\mysqli_sql_exception | \Exception $ex) {
            $this->log($ex->__toString());
        }
    }

    public function execute()
    {
        mysqli_stmt_execute($this->sResult);
    }

    public function count(): string|int
    {
        return (empty($this->sPreparado)) ? mysqli_num_rows($this->sResult) : mysqli_num_rows($this->sQuery);
    }

    public function getResult()
    {
        $this->sQuery = mysqli_stmt_get_result($this->sResult);
    }

    public function fetch(): array|false|null
    {
        if (empty($this->sPreparado)) {
            return mysqli_fetch_array($this->sResult, MYSQLI_ASSOC);
        } else {
            return mysqli_fetch_array($this->sQuery, MYSQLI_ASSOC);
        }
    }

    public function rows(array $rows)
    {
        $this->sRows = $rows;
    }

    public function row(string $name): mixed
    {
        return empty($this->sRows[$name]) ? '' : $this->sRows[$name];
    }
}
