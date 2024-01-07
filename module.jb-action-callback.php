<?php
//
// iTop module definition file
//
//
//
SetupWebPage::AddModule(__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'jb-action-callback/2.7.240107', array(
		// Identification
		//
		'label' => 'Feature: Callback action',
		'category' => 'business',

		// Setup
		//
		'dependencies' => array(
			'itop-config-mgmt/2.7.0 || itop-structure/3.0.0',
			// Dependency to this module is only here to force compatibility with iTop 2.7+, there is no functional dependency
			'itop-config/2.7.0',
		),
		'mandatory' => false,
		'visible' => true,

		// Components
		//
		'datamodel' => array(
			// Extension autoloader
			'vendor/autoload.php',
			'model.jb-action-callback.php',
		),
		'webservice' => array(),
		'data.struct' => array(
		),
		'data.sample' => array(),

		// Documentation
		//
		'doc.manual_setup' => '', // hyperlink to manual setup documentation, if any
		'doc.more_information' => '', // hyperlink to more information, if any

		// Default settings
		//
		'settings' => array(),
	)
);
