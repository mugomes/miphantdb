<?php
// Copyright (C) 2025 Murilo Gomes Julio
// SPDX-License-Identifier: MIT

// Site: https://github.com/mugomes

namespace MiPhantDB;

class delete extends database
{
    public function delete()
    {
        try {
            $txt = 'DELETE FROM ' . $this->getTable();

            $txt .= $this->getWhere();
            $txt .= $this->getOrderBy();
            $txt .= $this->getLimit();

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
