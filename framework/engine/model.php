<?php
    namespace framework\engine;

    class model
    {
        private $db = null;

        public function __construct($db)
        {
            $this->db = $db;    
        }
    }