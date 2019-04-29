function doPluginAction(scriptUrl, scriptPos, scriptArgs, actionDiv) {
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
				if((actVal == 'deleteProject') || (actVal == 'Activate') || (actVal == 'Inactivate') || (actVal == 'deleteDiary')){
					alertDemoMsg();
					return false;
				}
			}
		
			confirmLoad(scriptUrl, scriptPos, scriptArgs);
			break;
	}
}

function doDiaryAction(scriptUrl, scriptPos, scriptArgs, actionDiv, actionArg) {

	actVal = document.getElementById(actionDiv).value;
	scriptArgs += "&"+ actionArg+ "=" + actVal; 	
	scriptDoLoad(scriptUrl, scriptPos, scriptArgs);
}

