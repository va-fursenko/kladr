<?php


/**
 * Description of dbfClass
 *
 * @author Supervisor
 * @see http://jobtools.ru/2012/11/prosmotr-dbf-fajlov-onlajn-na-extjs-chast-1-for/
 */
class DBF {
    protected $filename = 'KLADR.DBF';
    protected $file;
    protected $header;
    protected $countColumns;
    protected $countRows;
    protected $columns  = [];



    public function __construct($filename)
    {
        $this->open($filename);
        $this->readHeader();
        $this->readColumns();
    }


    function open($filename)
    {
        $this->filename = $filename;
    }



    function close()
    {
        fclose($this->file);
    }



    function readHeader()
    {
        $this->file = fopen($this->filename, 'r');
        if (!$this->file) {

        }
        $tmp = fread($this->file, 12);
        $format = 'C1BYTE1/C1YY/C1MM/C1DD/l1RECORDSCOUNT/s1HEADERSIZE/s1RECORDSIZE';
        // массив с полями заголовка dbf
        $this->header = unpack($format, $tmp);
        $this->countColumns = ($this->header['HEADERSIZE'] - 33) / 32;
        $this->countRows = ($this->header['RECORDSCOUNT']);
    }
    
    
    
    function readColumns()
    {
        fseek($this->file, 32);
        $formatColumn = 'A11NAME/c1TIP/l1RESERVED1/C1SIZEBIN/C1ZPT/s1RESERVED2/C1ID/l1RESERVED3/s1RESERVED4/C1MDX/l1POS';           
        $pos = 0;
        $cnt = 0;
        for ($i = 1; $i <= $this->countColumns; $i++) {
            $tmp = fread($this->file, 32);
            $column = unpack($formatColumn, $tmp);         
            $posColumn = $pos;
            $pos = $pos + $column['SIZEBIN'];                        
            if ($column['TIP'] != 0) {
                array_push(
                    $this->columns,
                    [
                        'NAME'      => trim($column['NAME']),
                        'TIP'       => chr($column['TIP']),
                        'ZPT'       => $column['ZPT'],
                        'SIZEBIN'   => $column['SIZEBIN'],
                        'MDX'       => $column['MDX'],
                        'POS'       => $posColumn
                    ]
                );
                $cnt++;
            }
        }
        $this->countColumns = $cnt;
    }



    //Возвращае тип столбца
    function getColumnType($columnPosition)
    {
        if (($columnPosition > $this->countColumns - 1) || ($columnPosition < 0)) {
            return "ERR";
        }
        
        switch ($this->columns[$columnPosition]['TIP']) {
            case 'C': return "CHAR";
            case 'D': return "DATE";
            case 'N': return "NUMERIC";
            case 'M': return "MEMO";
            case 'L': return "BOOL";
            case 'F': return "FLOAT";
            case 'T': return "DATETIME";
            case 'I': return "INTEGER";
            case 'Y': return "CURRENCY";
            case 'O': return "DOUBLE";
            case 'B': return "DOUBLE";
            default:  return "UNKNOWN";
        }
    }   



    function getPostionRowInFileStart($row)
    {
        return $this->header['HEADERSIZE'] + 1 + $row * $this->header['RECORDSIZE'];
    }



    function getColumnSize($columnPosition)
    {
        if ($this->columns[$columnPosition]['NAME']=='') {
            return -1;
        } else {
            return $this->columns[$columnPosition]['SIZEBIN'];
        }
    }



    function getCountColumns()
    {
        return $this->countColumns;
    }



    function getCountRows()
    {
        return $this->countRows;
    }


    
    function getColumnName($columnPosition)
    {
        if ($this->columns[$columnPosition]['NAME']=='') {
            return -1;
        } else {
            return $this->columns[$columnPosition]['NAME'];
        }
    }



    function getColumnPos($columnName)
    {
        for ($i = 0; $i <= $this->countColumns - 1; $i++) {
            if ($this->columns[$i]['NAME'] == $columnName) {
                return $i;
            }
        }
        return -1;
    }



    function getColumnZpt($columnPosition)
    {
        if ($this->columns[$columnPosition]['NAME'] == '') {
            return -1;
        } else {
            return $this->columns[$columnPosition]['ZPT'];
        }
    }



    function getVersion()
    {
        switch ($this->header['BYTE1']) {
            case 2: return "FoxBASE";
            case 3: return "FoxPro, FoxBASE+, dBASE III PLUS, dBASE IV (без memo)";
            case 48: return "Visual FoxPro";
            case 49: return "Visual FoxPro";
            case 67: return "dBASE IV SQL файлы  (без memo)";
            case 99: return "dBASE IV SQL system file  (без memo)";
            case 131: return "FoxBASE+, dBASE III PLUS  (с memo)";
            case 139: return "dBASE IV  (с memo)";
            case 203: return "dBASE IV ASQL table file  (с memo)";
            case 245: return "FoxPro 2.x  (или более ранних версий)  (с memo)";
            case 251: return "FoxBASE";
        }
        return "Unknown";
    }



    function getValue($row, $column)
    {
        //проверка на диапазон кол-ва столбцов
        if ($row > $this->header['RECORDSCOUNT']) {
            return "ERR";
        }
        //проверка на диапазон кол-ва столбцов
        if ($column > $this->countColumns) {
            return "ERR";
        }
        fseek($this->file, $this->header["HEADERSIZE"] + 1 + $row * $this->header["RECORDSIZE"] + $this->columns[$column]["POS"]);
        $buf = fread($this->file, $this->columns[$column]["SIZEBIN"]);
        return iconv('cp866', 'utf-8', $this->parseValue($this->columns[$column]['TIP'], $buf));
    }    



    function getValueByColumnName($row, $columnName)
    {
        //проверка на диапазон кол-ва столбцов
        if ($row > $this->header['RECORDSCOUNT']) {
            return "ERR";
        }
        $column = $this->getColumnPos($columnName);
        //проверка на диапазон кол-ва столбцов
        if ($column < 0) {
            return "ERR";
        }
        fseek($this->file, $this->header["HEADERSIZE"] + 1 + $row * $this->header["RECORDSIZE"] + $this->columns[$column]["POS"]);
        $buf = fread($this->file, $this->columns[$column]["SIZEBIN"]);
        return $this->parseValue($this->columns[$column]['TIP'], $buf);
    }



    function parseValue($tip, $buffer)
    {
        if (($tip != 'D')&&($tip != 'T')) {
            return iconv("WINDOWS-1251", "WINDOWS-1251", $buffer);
        }
        if ($tip == 'T') {
            return $this->JulianDate($buffer);
            //return jdtogregorian((float)$buffer);
        }
        if ($tip == 'D') {
            $tmp  = $buffer[6];
            $tmp .= $buffer[7];
            $tmp .= '.';
            $tmp .= $buffer[4];
            $tmp .= $buffer[5];
            $tmp .= '.';
            $tmp .= $buffer[0];
            $tmp .= $buffer[1];
            $tmp .= $buffer[2];
            $tmp .= $buffer[3];
            return $tmp;
        }
    }







  /**
   *
   * Далее идёт и вовсе, какое-то творчество мистера Хэнки, составлю, как есть
   *
   */


    function JulianDate($buffer)
    {
        $format = 'l1DAYS';
        $julian = unpack($format, $buffer);
        $julian = $julian["DAYS"];
        $julian = $julian - 1721119; 
        $calc1 = 4 * $julian - 1; 
        $year = floor($calc1 / 146097); 
        $julian = floor($calc1 - 146097 * $year); 
        $day = floor($julian / 4); 
        $calc2 = 4 * $day + 3; 
        $julian = floor($calc2 / 1461); 
        $day = $calc2 - 1461 * $julian; 
        $day = floor(($day + 4) / 4); 
        $calc3 = 5 * $day - 3; 
        $month = floor($calc3 / 153); 
        $day = $calc3 - 153 * $month; 
        $day = floor(($day + 5) / 5); 
        $year = 100 * $year + $julian; 

        if ($month < 10) { 
           $month = $month + 3; 
        }        
        else { 
            $month = $month - 9; 
            $year = $year + 1; 
        } 

        if (strlen($month)<=1)
            $month = '0'.$month;
        if (strlen($day)<=1)
            $day = '0'.$day;
        
        return "$day.$month.$year"; 
    }
















    function debug_PrintHeaderInfo()
    {
        echo ' Записей всего - ';
        echo $this->header['RECORDSCOUNT'];
        echo ' Размер заголовка - ';
        echo $this->header['HEADERSIZE'];
        echo ' Размер записи - ';
        echo $this->header['RECORDSIZE'];
        echo ' Столбцов всего - ';
        echo ($this->header['HEADERSIZE'] - 33) / 32;
    }   



    function debug_PrintColumnInfo()
    {
        echo 'Всего колонок - &nbsp';
        echo count($this->columns);
        echo '<br/>';
        for($i=0;$i<=count($this->columns);$i++)
        {
            echo $i;
            echo ' ';
            echo $this->columns[$i]['NAME'];
            echo '&nbsp тип: ';            
            $tip = $this->columns[$i]['TIP'];
            echo $tip;
            echo '&nbsp длина: ';
            echo $this->columns[$i]['SIZEBIN'];
            if ($tip=='O' ||$tip=='B'||$tip=='N')
            {
                echo '&nbsp после точки: ';
                echo $this->columns[$i]['ZPT'];
            }
            echo '&nbsp позиция: ';
            echo $this->columns[$i]['POS'];

            echo '<br/>';
        }
    }
}
