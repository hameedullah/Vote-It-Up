/* Adapted from VoteItUp */
/* A script for updating the contents of the vote widget on the fly */
/*
USAGE:
vote (object to update with vote count, object to update with after vote text, post id, user id, base url)
*/

/* Note: This script is meant for the bar theme of the plugin */

var xmlHttp
var currentobj
var voteobj
var aftervotetext

//Useful for compatibility
function function_exists( function_name ) { 
    if (typeof function_name == 'string'){
        return (typeof window[function_name] == 'function');
    } else{
        return (function_name instanceof Function);
    }
}

//Javascript Function for JavaScript to communicate with Server-side scripts
function lg_AJAXrequest(scriptURL) {
	xmlHttp=zGetXmlHttpObject()
if (xmlHttp==null)
  {
  alert ("Your browser does not support AJAX!");
  return;
  } 
	xmlHttp.onreadystatechange=zvoteChanged;
	xmlHttp.open("GET",scriptURL,true);
	xmlHttp.send(null);
}

function zGetXmlHttpObject()
{
var xmlHttp=null;
try
  {
  // Firefox, Opera 8.0+, Safari
  xmlHttp=new XMLHttpRequest();
  }
catch (e)
  {
  // Internet Explorer
  try
    {
    xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
  catch (e)
    {
    xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
  }
return xmlHttp;
}

function zvoteChanged() 
{ 
if (xmlHttp.readyState==4)
{ 
	var votedisp = document.getElementById('voteid' + currentobj);
	var votenodisp = document.getElementById('votes' + currentobj);
	var voteno = xmlHttp.responseText;
	
	currentobj_obj = document.getElementById(currentobj);

	currentobj_obj.style.width = voteno;

        if ( voteobj !== null )  {
	    voteobj_obj = document.getElementById(voteobj);
	    voteobj_obj.innerHTML = aftervotetext;
        }


}


}


function vote(obj, votelinkobj, aftervote, postID ,userID, baseURL) {
	currentobj = obj;
	voteobj = votelinkobj;
	aftervotetext = aftervote;
	var scripturl = baseURL+"/voteinterface.php?type=vote&uid="+userID+"&pid="+postID+"&auth="+Math.random();
	lg_AJAXrequest(scripturl);
}

function sink(obj, votelinkobj, aftervote, postID ,userID, baseURL) {
	currentobj = obj;
	voteobj = votelinkobj;
	aftervotetext = aftervote;
	var scripturl = baseURL+"/voteinterface.php?type=sink&uid="+userID+"&pid="+postID+"&auth="+Math.random();
	lg_AJAXrequest(scripturl);
}

// Arghhh!!! missing vote_ticker function
// I am just copying and posting vote and sink functions to create vote_ticker and sink_ticker
// dont have nerves to create my own function in this mess. sorry
function vote_ticker(obj, postID ,userID, baseURL) {
	currentobj = obj;
	voteobj = null; // ahh!!! global javascript vars
	aftervotetext = null; // ahh!!! global javascript vars
	var scripturl = baseURL+"/voteinterface.php?type=vote&uid="+userID+"&pid="+postID+"&auth="+Math.random();
	lg_AJAXrequest(scripturl);
}

function sink_ticker(obj,  postID ,userID, baseURL) {
	currentobj = obj;
	voteobj = null; // ahh!! global javascript vars
	aftervotetext = null; // ahh!! global javascript vars
	var scripturl = baseURL+"/voteinterface.php?type=sink&uid="+userID+"&pid="+postID+"&auth="+Math.random();
	lg_AJAXrequest(scripturl);
}
