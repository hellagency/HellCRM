/************************************************************************************************************
(C) www.dhtmlgoodies.com, November 2005

This is a script from www.dhtmlgoodies.com. You will find this and a lot of other scripts at our website.	

Terms of use:
You are free to use this script as long as the copyright message is kept intact. However, you may not
redistribute, sell or repost it without our permission.

Thank you!

www.dhtmlgoodies.com
Alf Magne Kalleland

************************************************************************************************************/	
	
var currentlyActiveInputRef = false;
var currentlyActiveInputClassName = false;

function highlightActiveInput()
{
	if(currentlyActiveInputRef){
		currentlyActiveInputRef.className = currentlyActiveInputClassName;
	}
	currentlyActiveInputClassName = this.className;
	this.className = 'inputHighlighted';
	currentlyActiveInputRef = this;
	
	
}

function blurActiveInput()
{
	this.className = currentlyActiveInputClassName;
	
	
}


function initInputHighlightScript()
{
	var tags = ['INPUT','TEXTAREA'];
	
	for(tagCounter=0;tagCounter<tags.length;tagCounter++){
		var inputs = document.getElementsByTagName(tags[tagCounter]);
		for(var no=0;no<inputs.length;no++){
			if(inputs[no].className && inputs[no].className=='doNotHighlightThisInput')continue;
			
			if(inputs[no].tagName.toLowerCase()=='textarea' || (inputs[no].tagName.toLowerCase()=='input' && inputs[no].type.toLowerCase()=='text')){
				inputs[no].onfocus = highlightActiveInput;
				inputs[no].onblur = blurActiveInput;
			}
		}
	}
}