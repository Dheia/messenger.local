<?php

/** Добавить контакт(чат) для пользователя */
class AddContactModel extends \core\Model
{
    private $contacts;

    public function __construct($CONFIG){
        $this->contacts = $CONFIG->getContacts();
    }

    public function run(){
        session_start();
        $email = isset($_COOKIE['auth']) ?  $_COOKIE['email'] : $_SESSION['email'];
        echo $this->contacts->addContact($_GET['contact'], $email) ;
    }
}