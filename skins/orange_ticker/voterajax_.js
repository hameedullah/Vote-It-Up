/* Adapted from VoteItUp */

var xmlHttp
var currentobj
var voteobj
var votedisptype

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

switch (votedisptype) {
case 'bar':

votenodisp.style.width = voteno;
votedisp.innerHTML = '';

break;
case 'ticker':
votenodisp.innerHTML = voteno;
votedisp.innerHTML = '';
votedisp.style.display = 'none';
break;
case 'nticker':
votenodisp.innerHTML = voteno;
votedisp.innerHTML = '';
var votecontainer = document.getElementById('votewidget' + currentobj);
votecontainer.setAttribute("class", "post_votewidget_closed"); 
votecontainer.setAttribute("className", "post_votewidget_closed"); 
break;
}

}

//return xmlHttp.responseText;
//document.write(xmlHttp.responseText);
}


function vote_nticker(articleID,voteID,userID,baseURL) {
	votedisptype = 'nticker';
	currentobj = voteID;
	var scripturl = baseURL+"/voteinterface.php?type=vote&tid=total&uid="+userID+"&pid="+articleID+"&auth="+Math.random();
	lg_AJAXrequest(scripturl);
}

function sink_nticker(articleID,voteID,userID,baseURL) {
	votedisptype = 'nticker'; //nticker
	currentobj = voteID;
	var scripturl = baseURL+"/voteinterface.php?type=sink&tid=total&uid="+userID+"&pid="+articleID+"&auth="+Math.random();
	lg_AJAXrequest(scripturl);
}
