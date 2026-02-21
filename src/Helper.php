<?php
/**
 * @copyright   Copyright (c) 2022-2024 Jeffrey Bostoen
 * @license     See license.md
 * @version     2.7.240107	
 *
 */

namespace JeffreyBostoenExtensions\ActionCallback;


// Generic
use Exception;

// iTop internals
use DBObject;
use IssueLog;
use Metamodel;

// iTop classes
use ActionCallback;
use EventCallback;
use Ticket;

/**
 * Class Helper. This contains some helper methods for the callback action.
 */
abstract class Helper {

	/** @const string MODULE_CODE The module code. */
	const MODULE_CODE = 'jb-action-callback';

	/** @var string|null $sTraceId The trace ID. */
	private static $sTraceId = null;

	/**
	 * Trace function used for debugging.
	 *
	 * @param string $sMessage The message.
	 * @param mixed ...$args
	 *
	 * @return void
	 */
	public static function Trace($sMessage, ...$args) : void {
		
		// Store somewhere?		
		if(MetaModel::GetModuleSetting(static::MODULE_CODE, 'trace_log', false) == true) {
			
			$sTraceFileName = sprintf(APPROOT.'/log/trace_map_%1$s.log', date('Ymd'));

			try {
				
				if(static::$sTraceId == null) {
				
					static::$sTraceId = bin2hex(random_bytes(10));
					
				}
				
				$sMessage = call_user_func_array('sprintf', func_get_args());

				// Not looking to create an error here 
				file_put_contents($sTraceFileName, $sMessage.PHP_EOL , FILE_APPEND | LOCK_EX);
				
			}
			catch(Exception $e) {
				// Don't do anything.
			}
			
		}

	}

	/**
	 * Closes the ticket.
     * 
     * @details Applies the ev_close stimulus.
	 *
	 * @param DBObject $oObject iTop object.
	 * @param Array $aContextArgs Hash table containing context arguments.
	 * @param EventCallback $oLog Notification object (log).
	 * @param ActionCallback $oAction The action which is being executed.
	 *
	 * @return void.
	 */
	public static function CloseTicket($oObject, $aContextArgs, $oLog, $oAction) {

        static::Trace('Called CloseTicket');
	
		/**@var DBObject $oObject */
		if($oObject instanceof Ticket) {

            static::Trace('The object is a Ticket.');

            /** @var Ticket $oObject */

            try {

                static::Trace('Applying stimulus');
                $oObject->ApplyStimulus('ev_close');
				$oObject->DBUpdate();
                static::Trace(sprintf('Applied stimulus. New state: %1$s', $oObject->Get('status')));

            }
            catch(Exception $e) {

                // Closing may fail if some fields that are mandatory are still blank.
               static::Trace(sprintf('Error while trying to close ticket %1$s', $oObject->GetKey()));

            }

        }
        else {

            static::Trace('The object is NOT a Ticket.');

            $sError = sprintf('Can not call %1$s on a non-ticket class.', __METHOD__);
            IssueLog::Error($sError);
            throw new Exception($sError);
        }
		
	}



	/**
	 * Just a demo method. 
	 * This will create a action_callback_demo.txt file under iTop's directory/log containing some basic info on the trigger, the action and the object.
	 *
	 * @param DBObject $oObject iTop object.
	 * @param Array $aContextArgs Hash table containing context arguments.
	 * @param EventCallback $oLog Notification object (log).
	 * @param ActionCallback $oAction The action which is being executed.
	 *
	 * @return void.
	 */
	public static function DemoMethod(DBObject $oObject, array $aContextArgs, EventCallback $oLog, ActionCallback $oAction) {
	
		$sTemplate = str_repeat('-', 10).' '.date('Y-m-d H:i:s').PHP_EOL.
			'Trigger: %1$s - %2$s'.PHP_EOL.
			'Action: %3$s - %4$s'.PHP_EOL.
			'Object: %5$s - %6$s'.PHP_EOL.

		/** @var Trigger $oTrigger */
		$oTrigger = $aContextArgs['trigger->object()'];
		
		$sContent = sprintf($sTemplate,
			$oTrigger->GetKey(),
			$oTrigger->Get('friendlyname'),
			$oAction->GetKey(),
			$oAction->Get('friendlyname'),
			$oObject->GetKey(),
			$oObject->Get('friendlyname')
		);

		file_put_contents(APPROOT.'/log/action_callback_demo.txt', $sContent, FILE_APPEND);
		
	}

}
