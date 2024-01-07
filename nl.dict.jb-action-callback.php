<?php
/**
 * @copyright   Copyright (c) 2022-2024 Jeffrey Bostoen
 * @license     See license.md
 * @version     2.7.240107
 *
 */

// Base classes
Dict::Add('NL NL', 'Dutch', 'Nederlands', array(

	// EventCallback
	'Class:EventCallback' => 'Callback-gebeurtenis',
	'Class:EventCallback+' => 'Callback-gebeurtenis',
	
	// ActionCallback
	'Class:ActionCallback' => 'Callback',
	'Class:ActionCallback+' => 'Callback',
	'Class:ActionCallback/Attribute:callback' => 'Callback',
	'Class:ActionCallback/Attribute:callback+' => 'Je kan 2 soorten methodes gebruiken:
- Gedefinieerd op het object zelf (bv. Gebruikersverzoek). Bv: "$this->XXX" voor een functie die binnen de PHP-klasse van het object gedefinieerd is als "public function XXX($aContextArgs, $oLog, $oAction)"
- Via eender welke PHP-klasse. De methode moet static en public zijn. De naam moet fully qualified opgegeven worden. Bv: "\SomeClass::XXX" voor een functie die in de PHP-klasse "\SomeClass" gedefinieerd is "public static function XXX($oObject, $aContextArgs, $oLog, $oAction)"',

));
