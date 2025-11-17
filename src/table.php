<?php
// Copyright (C) 2025 Murilo Gomes Julio
// SPDX-License-Identifier: LGPL-2.1-only

// Site: https://www.mugomes.com.br

namespace MiPhantDB;

class table extends database
{
    private string $sEngine = 'MyISAM';

    private array $sCreateColumns = [];

    private bool $ctInt = false;
    private bool $ctLongText = false;
    private bool $ctNull = false;
    private bool $ctAutoIncrement = false;
    private bool $ctPrimaryKey = false;
    private int $ctTamanho = 45;
    private int $ciTamanho = 11;
    private string $ctDefaultValue = '';
    private string $ctAfter = '';

    private function clean()
    {
        $this->ctInt = false;
        $this->ctLongText = false;
        $this->ctNull = false;
        $this->ctAutoIncrement = false;
        $this->ctPrimaryKey = false;
        $this->ctTamanho = 45;
        $this->ciTamanho = 11;
        $this->ctDefaultValue = '';
        $this->ctAfter = '';
        $this->sEngine = 'MyISAM';
    }

    public function cleanAll()
    {
        $this->clean();
        $this->sCreateColumns = [];
        $this->sTabelas = [];
    }

    public function int()
    {
        $this->ctInt = true;
        return $this;
    }

    public function longtext()
    {
        $this->ctLongText = true;
        return $this;
    }

    public function null()
    {
        $this->ctNull = true;
        return $this;
    }

    public function autoIncrement()
    {
        $this->ctAutoIncrement = true;
        return $this;
    }

    public function primaryKey()
    {
        $this->ctPrimaryKey = true;
        return $this;
    }

    public function varcharSize(int $value = 45)
    {
        $this->ctTamanho = $value;
        return $this;
    }

    public function intSize(int $value = 45)
    {
        $this->ciTamanho = $value;
        return $this;
    }

    public function defaultValue(string $value)
    {
        $this->ctDefaultValue = $value;
        return $this;
    }

    public function after(string $value)
    {
        $this->ctAfter = $value;
        return $this;
    }

    public function engine(string $name)
    {
        $this->sEngine = $name;
        return $this;
    }

    public function add(string $name)
    {
        $sql = $name;
        if ($this->ctLongText) {
            $sql .= ' LONGTEXT';
        } else {
            $sql .= ($this->ctInt) ? ' int(' . $this->ciTamanho . ')' : ' varchar(' . $this->ctTamanho . ')';
        }

        if (empty($this->ctDefaultValue)) {
            $sql .= ($this->ctNull) ? ' DEFAULT NULL' : ' NOT NULL';
        } else {
            $sql .= ' DEFAULT ' . $this->ctDefaultValue . ' NOT NULL';
        }

        if ($this->ctAutoIncrement) {
            $sql .= ' AUTO_INCREMENT';
        }

        if ($this->ctPrimaryKey) {
            $sql .= ' PRIMARY KEY';
        }

        if (!empty($this->ctAfter)) {
            $sql .= ' AFTER ' . $this->ctAfter;
        }

        $this->sCreateColumns[] = $sql;
        $this->clean();
        return $this;
    }

    public function create()
    {
        try {
            $columns = '';
            foreach ($this->sCreateColumns as $value) {
                $columns .= $value . ', ';
            }
            $columns = rtrim($columns, ', ');

            $txt = sprintf('CREATE TABLE IF NOT EXISTS %s (%s) ENGINE=%s DEFAULT CHARSET=%s COLLATE=%s_general_ci;', $this->getTable(), $columns, $this->sEngine, $this->sCharset, $this->sCharset);
            mysqli_query($this->sConecta, $txt);

            $this->sFechaResult = false;
            $this->cleanAll();
        } catch (\mysqli_sql_exception $ex) {
            $this->log($ex->__toString());
        }
    }

    const ALTER_ADD = 'add';
    const ALTER_MODIFY = 'modify';
    public function alter(string $type = 'add')
    {
        try {
            $columns = '';
            foreach ($this->sCreateColumns as $value) {
                $columns .= $value . ',';
            }

            $columns = rtrim($columns, ',');

            if ($type == 'add') {
                $txt = sprintf('ALTER TABLE %s ADD COLUMN %s', $this->getTable(), $columns);
            } elseif ($type == 'modify') {
                $txt = sprintf('ALTER TABLE %s MODIFY %s', $this->getTable(), $columns);
            }

            mysqli_query($this->sConecta, $txt);

            $this->sFechaResult = false;
            $this->cleanAll();
        } catch (\mysqli_sql_exception $ex) {
            $this->log($ex->__toString());
        }
    }

    public function columnExists(string $column): bool
    {
        $txt = false;
        try {
            $sql = sprintf("SELECT COUNT(*) AS count1 FROM information_schema.columns WHERE table_name='%s' AND column_name='%s'", $this->getTable(), $column);
            if ($this->sResult = mysqli_query($this->sConecta, $sql)) {
                $row = mysqli_fetch_array($this->sResult);
                if ($row['count1'] > 0) {
                    $txt = true;
                }
                mysqli_free_result($this->sResult);
            }
        } catch (\mysqli_sql_exception $ex) {
            $this->log($ex->__toString());
        } finally {
            return $txt;
        }
    }
}
