<?php
    class MainController extends \core\Controller { 
        function action_index() {
            $this->model->run(); 
            $this->view->generate('template_view.php', 'main_view.php', 'main.css', 'main.js','Месенджер'); 
        } 
    }
?>