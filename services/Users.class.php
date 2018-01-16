<?php
/*
*
 */
class Users {
   private $NameUsers;         //  string  
   private final static $SOLDE_CONGE = 800; //solde de congé euros
   
   
   public function __construct($name){
      $this->NameUsers =$name;
   }
   /*
    * texte name .
    */
   public function getName(){
      return $this->name;
   }
   
    public static function getSolde(){
      return $this->SOLDE_CONGE;
   }
   
   
}
?>