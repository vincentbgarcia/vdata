<?php
namespace vinyoda197\Vdata;

class Vdata extends \ArrayIterator
{
    protected $_data = [];


    public function __construct($data = [])
    {
        if (!is_array($data)) {
            $j = json_decode($data, true);
            if ($j && !is_int($j)) {
                $data = $j;
            } else {
                $this->_data = $data;
                return;
            }
        }

        if (!$this->isAssoc($data)) {
            foreach ($data as $d) {
                $this->_data[] = new Vdata($d);
            }
        } else {
            foreach ($data as $k => $v) {
                $this->_data[$k] = new Vdata($v);
            }
        }

        parent::__construct($this->_data);
    }


    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }


    public function __get($name)
    {
        return isset($this->_data[$name])
            ? $this->_data[$name]
            : null;
    }


    public function data()
    {
        return $this->_data;
    }


    public function dataArray()
    {
        if (is_array($arr = $this->_data)) {
            foreach ($arr as $name => $value) {
                if (is_a($value, 'vinyoda197\Vdata\VData')
                  || is_subclass_of($value, 'vinyoda197\Vdata\Vdata')) {
                      $arr[$name] = $value->dataArray();
                } else {
                    if (is_object($value)) {
                        $arr[$name] = strval($value);
                    }
                }
            }
            return $arr;
        }
        return $this->_data;
    }


    public function __toString()
    {
        return is_array($this->_data)
                ? json_encode($this->dataArray(), JSON_HEX_APOS)
                : $this->_data;
    }


    protected function isAssoc(array $arr)
    {
        if (array() === $arr) {
            return false;
        }
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
