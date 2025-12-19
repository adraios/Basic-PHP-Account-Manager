<?php
interface ControllerInterface
{
    public static function render(array $data = array()) : void;
}