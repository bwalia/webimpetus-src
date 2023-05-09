
var today = new Date();
var expiry = new Date(today.getTime() + 28 * 24 * 60 * 60 * 1000);

function splitCookie(cookie){
    var cookieArray = new Array();
   if(cookie.length>0){
     cookieArray = cookie.split('$');
  }
   return cookieArray;
}


function local_setCookie(cookieName,cookieValue,nDays) {
//alert(cookieName + ' - ' +cookieValue);
 var today = new Date();
 var expire = new Date();
 if (nDays==null || nDays==0) nDays=1;
 expire.setTime(today.getTime() + 3600000*24*nDays);
 document.cookie = cookieName+"="+escape(cookieValue) + ";expires="+expire.toGMTString();
}
function local_splitCookie(cookie){
    var cookieArray = new Array();
   if(cookie.length>0){
     cookieArray = cookie.split('$');
  }
   return cookieArray;
}
 function local_getCookie(cookiename_var2){
   var docCookie = document.cookie
   var pieces = docCookie.split(";");
   var aRecordNums = new Array();
   for (i=0; i < pieces.length; i++)
   {
      nextpiece = pieces[i].split("=");
      if(nextpiece[0].charAt(0) == ' '){
      nextpiece[0] = nextpiece[0].substring(1, nextpiece[0].length);
      }
      if (nextpiece[0] == cookiename_var2)
      {
         if(nextpiece[1])
         {
            var cookie =  unescape(nextpiece[1]);
            aRecordNums = local_splitCookie(cookie)
          }
       }
    }
   return aRecordNums;
}
function clearSearch(){
 
var exceptNameStr = 'workstation_search_cat2';
 
 if(exceptNameStr != 'workstation_search_box') { local_setCookie('workstation_search_box', '', 366); }
 if(exceptNameStr != 'workstation_search_cat1') { local_setCookie('workstation_search_cat1', '', 366); }	
 if(exceptNameStr != 'workstation_search_cat2') { local_setCookie('workstation_search_cat2', '', 366); }
 
  if(exceptNameStr != 'workstation_search_desc_title') { local_setCookie('workstation_search_desc_title', '', 366); }
  if(exceptNameStr != 'workstation_search_perm') { local_setCookie('workstation_search_perm', 0, 366); }
  if(exceptNameStr != 'workstation_search_contract') { local_setCookie('workstation_search_contract', 0, 366); }
  if(exceptNameStr != 'workstation_search_freelance') { local_setCookie('workstation_search_freelance', 0, 366); }
 
}
function setMarketSector(nameOrIDStr) {
local_setCookie('workstation_search_cat2', nameOrIDStr, 366);
//alert(nameOrIDStr);
}



function joinCookie(aRecordNums)
{
   var cookie = "";
   for(var i = 0; i < aRecordNums.length; i++){
         cookie += aRecordNums[i] + '$';
  }
  if(cookie.length>0)cookie = cookie.substring(0, cookie.length-1);
  return cookie;
}

function findIntheSelection(item,cookieArray){
  var index = 0;
  var position = -1;
  for(var i = 0; i < cookieArray.length; i++){
    if(cookieArray[i] == item){
      position = i;
      break;
      }
  }
  return position;
}

function getCookie(cookiename_var2){
   var docCookie = document.cookie
   var pieces = docCookie.split(";");
   var aRecordNums = new Array();
   for (i=0; i < pieces.length; i++)
   {
      nextpiece = pieces[i].split("=");
      if(nextpiece[0].charAt(0) == ' '){
      nextpiece[0] = nextpiece[0].substring(1, nextpiece[0].length);
      }
      if (nextpiece[0] == cookiename_var2)
      {
         if(nextpiece[1])
         {
            var cookie =  unescape(nextpiece[1]);
            aRecordNums = splitCookie(cookie)
          }
       }
    }
   return aRecordNums;
}

function setCookie(name, value)
{
   document.cookie=name+"="+(value); path="/";
}

function removeFromSelection(position,cookiename_var1)
{
   var aRecordNums = getCookie(cookiename_var1);
	for(i = position+1;i < aRecordNums.length; i++)
	aRecordNums[i - 1] = aRecordNums[i];
	aRecordNums.length = aRecordNums.length - 1;
	cookietext = joinCookie(aRecordNums);
	setCookie(cookiename_var1,cookietext);
}

function addToSelection(item,cookiename_var3)
{
  //var aRecordNums = getCookie(cookiename_var3);
  if (document.cookie.indexOf(cookiename_var3) === -1 ) {
	  aRecordNums = [];
  } else {
	  var jsonObject = getCookie(cookiename_var3);
	  aRecordNums = jsonObject.split('$')
  }
  var position = findIntheSelection(item,aRecordNums);
  if (position == -1) {
       var nextPosition = aRecordNums.length?aRecordNums.length:0;
       aRecordNums.push(item);
  }
  var cookie =joinCookie(aRecordNums);
  setCookie(cookiename_var3,cookie)
}

function imageSwap(imageName,imageSource){
  imageName.src=imageSource;
}


function mylib_getObj(id,d){
	var i,x;  if(!d) d=document;
	if(!(x=d[id])&&d.all) x=d.all[id];
	for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][id];
	for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=ylib_getObj(id,d.layers[i].document);
	if(!x && document.getElementById) x=document.getElementById(id);
	return x;
}

function showHideAddRemoveArea(nRow, bShowAdd, bShowRemove){

if(bShowAdd) {
mylib_getObj("add"+nRow).style.display = document.all ? "block" : "table-row";
} else {
mylib_getObj("add"+nRow).style.display = "none";
}

if(bShowRemove) {
mylib_getObj("remove"+nRow).style.display = document.all ? "block" : "table-row";
} else {
mylib_getObj("remove"+nRow).style.display = "none";
}

}


function updateMyList(imageName, item, cookiename_var, pReloadPageBool){

nParams = updateMyList.arguments.lenght;

if(nParams<4) {	pReloadPageBool = false;	}

  var negativeImage='images/remove.gif';
  var positiveImage='images/add.gif';

  var aRecordNums=getCookie(cookiename_var);
  var position = findIntheSelection(item,aRecordNums)
  if(position>-1){
 //alert(imageName+'1');
  imageSwap(imageName,positiveImage)
    removeFromSelection(position,cookiename_var)}
  else{
 //alert(imageName);
    imageSwap(imageName,negativeImage)
    addToSelection(item,cookiename_var)
  }
  
  if(pReloadPageBool)
  {
  __updateMyApplication();
  }
  
}

var urlStr = "";
//var urlStr = "apply-now.html"

function __updateMyApplication(){

if(urlStr == ""){
urlStr=window.location.href;
//urlStr = urlStr +'?'+ Math.random();
}

setTimeout("document.location='"+urlStr+"'",100);
}
/*
function _updateMyJobsList(pReferenceStr, item, cookiename_var, pReloadPageBool, pReverseModeBool){

nParams = _updateMyJobsList.arguments.lenght;

var	linkTitleObj					= eval('document.getElementById("' + pReferenceStr + '")');
var aRecordNums						= getCookie(cookiename_var);
var position						= findIntheSelection(item,aRecordNums)

if(nParams<4) {	pReloadPageBool = false;	}
if(nParams<5) { pReverseModeBool = false;	}

if(position > -1){

if(pReverseModeBool == false){
removeFromSelection(position,cookiename_var);
linkTitleObj.innerHTML = "[ x Remove from shortlisted jobs ]";
} else {
linkTitleObj.innerHTML = "[ x Remove from shortlisted jobs ]";
}
			}else{
if(pReverseModeBool == false){
addToSelection(item,cookiename_var);
linkTitleObj.innerHTML = "[ Apply for this job ]";
} else  {
linkTitleObj.innerHTML = "[ Apply for this job ]";
}
}


  if(pReloadPageBool)
  {
  __updateMyApplication();
  }

}    */   

function _updateMyJobsList(pReferenceStr, item, cookiename_var, pReloadPageBool, pOnLoadBool){

nParams = _updateMyJobsList.arguments.length;

//var	linkTitleObj					= eval('document.getElementsByClassName("' + pReferenceStr + '")');
var aRecordNums						= getCookie(cookiename_var);
var position						= findIntheSelection(item,aRecordNums)

if(nParams<4) {	pReloadPageBool = true;	}
if(nParams<5) { pOnLoadBool = false;	}


if(pOnLoadBool){
if(position > -1){ // in user set
// linkTitleObj.innerHTML = "[ x Remove from shortlisted jobs ]";
//linkTitleObj.innerHTML = "x Remove from shortlisted jobs";
//linkTitleObj.title = "Remove from shortlisted jobs";
if(pReferenceStr=='addback'){
	$('.'+pReferenceStr).html("x Remove from shortlisted jobs");
	$('.'+pReferenceStr).attr("title", "Remove from shortlisted jobs");
	
} 
else if(pReferenceStr=='addforward'){
	$('.'+pReferenceStr).html("Submit Application");
	$('.'+pReferenceStr).attr("title", "Submit Application");
}
else{
	$('.'+pReferenceStr).html("x Remove from shortlisted jobs");
	$('.'+pReferenceStr).attr("title", "Remove from shortlisted jobs");
}
} else {// NOT in user set
if(pReferenceStr=='addback'){
	$('.'+pReferenceStr).html("Shortlist this Job");
	$('.'+pReferenceStr).attr("title", "Shortlist this Job");
} 
else if(pReferenceStr=='addforward'){
	$('.'+pReferenceStr).html("Apply for this job");
	$('.'+pReferenceStr).attr("title", "Apply for this job");
}
else{
	$('.'+pReferenceStr).html("Apply for this job");
	$('.'+pReferenceStr).attr("title", "Apply for this job");
}
//linkTitleObj.innerHTML = "Apply for this job";
//linkTitleObj.title = "Apply for this job";
}
} else {// NOT ONLOAD 
if(position > -1){ // in user set


if(pReferenceStr=='addback'){
	removeFromSelection(position,cookiename_var); //remove from set
	//$('.'+pReferenceStr).html("Shortlist this Job");
//	$('.'+pReferenceStr).attr("title", "Shortlist this Job");
	urlStr=window.location.href;
} 
else if(pReferenceStr=='addforward'){
	
	//$('.'+pReferenceStr).html("Apply for this job");
//	$('.'+pReferenceStr).attr("title", "Apply for this job");
	urlStr="apply-now.html";
}
else{
	removeFromSelection(position,cookiename_var); //remove from set
	$('.'+pReferenceStr).html("x Remove from shortlisted jobs");
	$('.'+pReferenceStr).attr("title", "Remove from shortlisted jobs");
	urlStr=window.location.href;
}



//urlStr = urlStr +'?'+ Math.random();
// linkTitleObj.innerHTML = "[ Apply for this job ]";
} else {


if(pReferenceStr=='addback'){
	addToSelection(item,cookiename_var); //add to set
	//$('.'+pReferenceStr).html("x Remove from shortlisted jobs");
//	$('.'+pReferenceStr).attr("title", "Remove from shortlisted jobs");
	
	var coki_srch= getCookie('last_srch');
	var coki_page= getCookie('last_page');
	urlStr= getCookie('referrer');
	if(coki_srch!=''){
		urlStr+='?category='+coki_srch;
	}
} 
else if(pReferenceStr=='addforward'){
	addToSelection(item,cookiename_var); //add to set
	//$('.'+pReferenceStr).html("Submit Application");
//	$('.'+pReferenceStr).attr("title", "Submit Application");
	urlStr = "apply-now.html";
}
else{
	addToSelection(item,cookiename_var); //add to set
	$('.'+pReferenceStr).html("x Remove from shortlisted jobs");
	$('.'+pReferenceStr).attr("title", "Remove from shortlisted jobs");
	urlStr = "apply-now.html";
}


//linkTitleObj.innerHTML = "x Remove from shortlisted jobs";
//linkTitleObj.title = "Remove from shortlisted jobs";

}
}

  if(pReloadPageBool)
  {
  __updateMyApplication();
  }

} 