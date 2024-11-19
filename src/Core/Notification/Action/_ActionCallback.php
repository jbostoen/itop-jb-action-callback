<?php
/**
 * @copyright   Copyright (c) 2022-2024 Jeffrey Bostoen
 * @license     See license.md
 * @version     2.7.240107	
 *
 */
 
namespace JeffreyBostoenExtensions\ActionCallback\Core\Notification\Action;

use ActionNotification;
use ApplicationContext;
use EventCallback;
use Exception;
use MetaModel;
use UserRights;
use JeffreyBostoenExtensions\ActionCallback\ActionCallbackHelper;

abstract class _ActionCallback extends ActionNotification {
	
	/**
	 * @inheritDoc
	 *
	 * @throws ArchivedObjectException
	 * @throws CoreException
	 */
	public function DoExecute($oTrigger, $aContextArgs) {
		
		// Create notification object to log status information if enabled
		if (MetaModel::IsLogEnabledNotification()) {
			$oLog = new EventCallback();
			if ($this->IsBeingTested()) {
				$oLog->Set('message', 'TEST - Scheduled');
			}
			else {
				$oLog->Set('message', 'Scheduled');
			}
			$oLog->Set('userinfo', UserRights::GetUser());
			$oLog->Set('trigger_id', $oTrigger->GetKey());
			$oLog->Set('action_id', $this->GetKey());
			$oLog->Set('object_id', $aContextArgs['this->object()']->GetKey());
			$oLog->DBInsertNoReload();
		}
		else {
			$oLog = null;
		}

		try {
			// Execute Request
			$sRes = $this->_DoExecute($oTrigger, $aContextArgs, $oLog);

			// Logging Feedback
			if($oLog) {
				$sPrefix = ($this->IsBeingTested()) ? 'TEST - ' : '';
				$oLog->Set('message', $sPrefix.$sRes);
			}

		}
		catch(Exception $oException) {
			if($oLog) {
				$oLog->Set('message', 'Error: '.$oException->getMessage());
			}
		}
		
		if($oLog) {
			$oLog->DBUpdate();
		}
		
	}

	/**
	 * Do the execution itself
	 *
	 * @param Trigger           $oTrigger TriggerObject which called the action
	 * @param array              $aContextArgs
	 * @param EventNotification $oLog     Reference to the Log Object for store information in EventNotification
	 *
	 * @return string
	 * @throws Exception
	 */
	protected function _DoExecute($oTrigger, $aContextArgs, &$oLog) {
		
		$sPreviousUrlMaker = ApplicationContext::SetUrlMakerClass();
		
		try {
			
			/** @var \DBObject $oTriggeringObject */
			$oTriggeringObject = $aContextArgs['this->object()'];
			
			$sCallbackFQCN = $this->Get('callback');
			
			if(empty($sCallbackFQCN)) {
				
				ActionCallbackHelper::Trace('Callback not specified.');
				throw new Exception('Callback not specified.');
				
			}
			else {
				
				/** @var \DBObject $oTriggeringObject */
				$oTriggeringObject = $aContextArgs['this->object()'];

				// Check if callback is on the object itself
				if(stripos($sCallbackFQCN, '$this->') !== false) {
					
					$sMethodName = str_ireplace('$this->', '', $sCallbackFQCN);
					ActionCallbackHelper::Trace(sprintf('Call method %1$s on object %$2s', $sMethodName, $oTriggeringObject->Get('friendlyname')));
					$sReturn = $oTriggeringObject->$sMethodName($aContextArgs, $oLog, $this);
					
				}
				// Otherwise, check if callback is callable as a static method
				elseif(is_callable($sCallbackFQCN)) {
					
					ActionCallbackHelper::Trace(sprintf('Call method %1$s for object %2$s', $sCallbackFQCN, $oTriggeringObject->Get('friendlyname')));
					$sReturn = call_user_func($sCallbackFQCN, $oTriggeringObject, $aContextArgs, $oLog, $this);
					
				}
				// Otherwise, there is a problem
				else {
					$sError = sprintf('Specified callback is not callable: %1$s for object %2$s', $sCallbackFQCN, $oTriggeringObject->Get('friendlyname'));
					ActionCallbackHelper::Trace($sError);
					throw new Exception($sError);
				}
				
			}

			
			return ($sReturn != '' ? $sReturn : 'Executed');
			
		}
		catch(Exception $oException) {
			ApplicationContext::SetUrlMakerClass($sPreviousUrlMaker);
			throw $oException;
		}
		
		ApplicationContext::SetUrlMakerClass($sPreviousUrlMaker);

		return 'Bug: Unknown behavior, check the event notification log.';
		
	}


	/**
	 * Just a demo method. 
	 * This will creae a action_callback_demo.txt file under iTop's directory/log containing the trigger name and object name.
	 *
	 * @param DBObject $oObject iTop object.
	 * @param Array $aContextArgs Hash table containing context arguments.
	 * @param EventCallback $oLog Notification object (log).
	 * @param ActionCallback $oAction The action which is being executed.
	 *
	 * @return void.
	 */
	public static function DemoMethod($oObject, $aContextArgs, $oLog, $oAction) {
	
		file_put_contents(APPROOT.'/log/action_callback_demo.txt', 'Trigger: '.$oAction->GetRawName().' - Object: '.$oObject->GetRawName());
		
	}

}
