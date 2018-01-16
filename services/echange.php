<?php
/**
*author : KUNSANGABO NDONGALA Berfy
*class for message X to Y
*/

public class Echange{

	private $Identifiant;
	private $expediteur;
	private $destinataire ;

	public function _construct($id , $exp , $dst){
		
		$this ->Identifiant =$id;
		$this->expediteur = $exp;
		$this ->destinataire =$dst;

	}

	/**
	*function for saved the message from expediteur to destinataire.
	*:$message: the string to save from the $destinataire database
	*/

	public function SavedMessage($message){
	}
	
}
?>