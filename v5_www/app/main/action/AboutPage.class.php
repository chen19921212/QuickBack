<?php

class AboutPage extends MainBasePage {
    
    public function defaultAction() {
        
        $this->renderMain(array(), 'about.php');
    }
}
