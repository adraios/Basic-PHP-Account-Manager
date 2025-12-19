<?php
class LoginController extends Controller implements ControllerInterface
{
    public static function render(array $data = array()) : void
    {
        self::buildPage('login', $data);
    }
}