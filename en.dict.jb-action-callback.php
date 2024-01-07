<?php
/**
 * @copyright   Copyright (c) 2022-2024 Jeffrey Bostoen
 * @license     See license.md
 * @version     2.7.240107
 *
 */

// Base classes
Dict::Add('EN US', 'English', 'English', array(

	// EventCallback
	'Class:EventCallback' => 'Callback event',
	'Class:EventCallback+' => 'Callback event',
	
	// ActionCallback
	'Class:ActionCallback' => 'Callback event',
	'Class:ActionCallback+' => 'Callback event',
	'Class:ActionCallback/Attribute:callback' => 'Callback',
	'Class:ActionCallback/Attribute:callback+' => 'You can use 2 types of methods:
- From the triggering object itself (eg. UserRequest), must be public. Example: $this->XXX($aContextArgs, $oLog, $oAction)
- From any PHP class, must be static AND public. Name must be name fully qualified. Example: \SomeClass::XXX($oObject, $aContextArgs, $oLog, $oAction)',

));
