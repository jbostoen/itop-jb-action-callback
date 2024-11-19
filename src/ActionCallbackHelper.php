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
use IssueLog;
use Metamodel;
use Ticket;

abstract class ActionCallbackHelper {

	/** @const string MODULE_CODE The module code. */
	const MODULE_CODE = 'jb-action-callback';

	/** @var string|null $sTraceId The trace ID. */
	private static $sTraceId = null;
    
	/**
	 * Trace function used for debugging.
	 *
	 * @param string $sMessage The message.
	 * @return void
	 */
	public static function Trace(string $sMessage) : void {
		
		// Store somewhere?		
		if(MetaModel::GetModuleSetting(static::MODULE_CODE, 'trace_log', false) == true) {
			
			$sTraceFileName = APPROOT.'/log/trace_callback_'.date('Ymd').'.log';
			
			try {
				
				if(static::$sTraceId == null) {
				
					static::$sTraceId = bin2hex(random_bytes(10));
					
				}
				
				// Not looking to create an error here 
				file_put_contents($sTraceFileName, date('Y-m-d H:i:s').' | '.static::$sTraceId.' | '.$sMessage.PHP_EOL , FILE_APPEND | LOCK_EX);
				
			}
			catch(Exception $e) {
				// Don't do anything
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

}
