<?php

class ManagerBasePage extends MainBasePage {
    
    
    public function __construct() {
        
        parent::__construct();
        
        $this->checkLogin();
        
    }
}
