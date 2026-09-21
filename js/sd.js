// state-changing actions dispatched through here MUST go through
// confirmSubmit() (POST), not confirmLoad() (GET) - a plain GET link/
// image tag can trigger these with no JS at all, and isn't mitigated by
// SameSite=Lax cookies (which still allow simple/top-level GET). Matches
// the identical fix already applied to the core app's js/common.js
// doAction() dispatcher.
var SD_STATE_CHANGING_ACTIONS = ['deleteProject', 'deleteDiary', 'Activate', 'Inactivate'];

function doSDPluginAction(scriptUrl, scriptPos, scriptArgs, actionDiv) {
	actVal = document.getElementById(actionDiv).value;
	scriptArgs += "&action=" + actVal;
	switch (actVal) {
		case "select":
			break;

		case "editProject":
		case "projectSummery":
		case "diaryManager":
		case "editDiary":
		case "newComment":
			scriptDoLoad(scriptUrl, scriptPos, scriptArgs);
			break;

		default:
			/* check whether the system is demo or not */
			if(spdemo){
				if(SD_STATE_CHANGING_ACTIONS.indexOf(actVal) !== -1){
					alertDemoMsg();
					return false;
				}
			}

			if (SD_STATE_CHANGING_ACTIONS.indexOf(actVal) !== -1) {
				// 'listform' need not exist on every view this is called
				// from - scriptDoLoadPost()'s jQuery('#listform').serialize()
				// on a missing selector just returns '', so this degrades
				// safely rather than breaking (scriptArgs already carries
				// project_id/diary_id directly)
				confirmSubmit(scriptUrl, 'listform', scriptPos, scriptArgs);
			} else {
				confirmLoad(scriptUrl, scriptPos, scriptArgs);
			}
			break;
	}
}

function doDiaryAction(scriptUrl, scriptPos, scriptArgs, actionDiv, actionArg) {
	actVal = document.getElementById(actionDiv).value;
	scriptArgs += "&"+ actionArg+ "=" + actVal; 	
	scriptDoLoad(scriptUrl, scriptPos, scriptArgs);
}

