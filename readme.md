# jb-action-callback

Copyright (c) 2019-2022 Jeffrey Bostoen

[![License](https://img.shields.io/github/license/jbostoen/iTop-custom-extensions)](https://github.com/jbostoen/iTop-custom-extensions/blob/master/license.md)
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.me/jbostoen)
🍻 ☕

Need assistance with iTop or one of its extensions?  
Need custom development?  
Please get in touch to discuss the terms: **info@jeffreybostoen.be** / https://jeffreybostoen.be

## What?

Adds a "Callback" action which can be linked to a trigger.

The use case is to quickly enable developers to hook a custom action to the existing trigger workflow in iTop.  

This action allows the developer to specify a method on any class (custom PHP class or a method on an object).


## Examples

Methods can look like this:


```

abstract class SomeHelper {

	/**
	 * Just a demo method. It saves a action_callback_demo.txt file under iTop's directory/log containing the trigger name and object name.
	 *
	 * @param \DBObject $oObject iTop object.
	 * @param \Array $aContextArgs Hash table containing context arguments.
	 * @param \EventCallback $oLog Notification object (log).
	 * @param \ActionCallback $oAction The action which is being executed.
	 *
	 * @return void.
	 */
	public static function DemoMethod($oObject, $aContextArgs, $oLog, $oAction) {
	
		file_put_contents(APPROOT.'/log/action_callback_demo.txt', 'Trigger: '.$oAction->GetRawName().' - Object: '.$oObject->GetRawName());
		
	}

}


```

Or add it in the datamodel as a method of an object class:

```

	<methods>
		<method id="SetLastReminder">
			<static>false</static>
			<access>protected</access>
			<type>Internal</type>
			<code><![CDATA[
		  
				/**
				 * Just a demo method. It saves a action_callback_demo.txt file under iTop's directory/log containing the trigger name and object name.
				 *
				 * @param \DBObject $oObject iTop object.
				 * @param \Array $aContextArgs Hash table containing context arguments.
				 * @param \EventCallback $oLog Notification object (log).
				 * @param \ActionCallback $oAction The action which is being executed.
				 *
				 * @return void.
				 */
				public function SetLastReminder($aContextArgs, $oLog, $oAction) {
				
					$this->Set('last_reminder', date('Y-m-d H:i:s'));
					$this->DBUpdate();
					
				}
				
			]]></code>
		</method>
	</methods>
			
```


## Cookbook

XML:
* Add new classes

PHP:
* Create new triggers and notifications.
