<?php

namespace App\Traites;
/**
 * Функции для страниц админки где выводится таблица всех записей модели и информация о конкретной записи
 *
 */
trait ApiArrays{

    /**
     * Возвращаем данные полям из $forSmallArray для сводной таблицы по всем записям модели
     *
     * @return array
     */
    public function getSmallArray() {
        $array = [];
        foreach ($this->forSmallArray as $value) {
            $array[$value] = $this->$value;
        }
        
        return $this->afterGetSmallArray($array);
    }

    /**
     * Функция, для допполнительной обработки маленького массива 
     * Можно переопределить при необходимости
     * @param array $array
     * @return array
     */
    public function afterGetSmallArray($array){
        return $array;
    }

    /**
     * Возвращаем данные по полям из $forBigArray определённой записи
     *
     * @return array
     */
    public function getBigArray() {
        $array = [];
        foreach ($this->forBigArray as $value) {
            $array[$value] = $this->$value;
        }
        return $this->afterGetBigArray($array);
    }

    /**
     * Функция, для дополнительной обработки большого массива 
     * Можно переопределить при необходимости
     * @param array $array
     * @return array
     */
    public function afterGetBigArray($array){
        return $array;
    }
}