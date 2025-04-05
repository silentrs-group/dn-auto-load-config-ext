<?php

namespace behaviour\custom;


interface DataModify
{
    /**
     * @param string $data
     * @return string
     */
    public function onRead($data);

    /**
     * @param string $data
     * @return string
     */
    public function onWrite($data);
}