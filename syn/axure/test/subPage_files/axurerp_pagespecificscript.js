
var PageName = 'subPage';
var PageId = 'p2d0c88dbfb1c47ba93f9e49b89532580'
var PageUrl = 'subPage.html'
document.title = 'subPage';

if (top.location != self.location)
{
	if (parent.HandleMainFrameChanged) {
		parent.HandleMainFrameChanged();
	}
}

var $OnLoadVariable = '';

var $login = '';

var $userLevel = '';

var $wireframe = '';

var $page = '';

var $pageName = '';

var $CSUM;

var hasQuery = false;
var query = window.location.hash.substring(1);
if (query.length > 0) hasQuery = true;
var vars = query.split("&");
for (var i = 0; i < vars.length; i++) {
    var pair = vars[i].split("=");
    if (pair[0].length > 0) eval("$" + pair[0] + " = decodeURIComponent(pair[1]);");
} 

if (hasQuery && $CSUM != 1) {
alert('Prototype Warning: The variable values were too long to pass to this page.\nIf you are using IE, using Firefox will support more data.');
}

function GetQuerystring() {
    return '#OnLoadVariable=' + encodeURIComponent($OnLoadVariable) + '&login=' + encodeURIComponent($login) + '&userLevel=' + encodeURIComponent($userLevel) + '&wireframe=' + encodeURIComponent($wireframe) + '&page=' + encodeURIComponent($page) + '&pageName=' + encodeURIComponent($pageName) + '&CSUM=1';
}

function PopulateVariables(value) {
  value = value.replace(/\[\[OnLoadVariable\]\]/g, $OnLoadVariable);
  value = value.replace(/\[\[login\]\]/g, $login);
  value = value.replace(/\[\[userLevel\]\]/g, $userLevel);
  value = value.replace(/\[\[wireframe\]\]/g, $wireframe);
  value = value.replace(/\[\[page\]\]/g, $page);
  value = value.replace(/\[\[pageName\]\]/g, $pageName);
  value = value.replace(/\[\[PageName\]\]/g, PageName);
  return value;
}

function OnLoad(e) {

}

var u2 = document.getElementById('u2');
gv_vAlignTable['u2'] = 'top';
var u1 = document.getElementById('u1');
gv_vAlignTable['u1'] = 'center';
var u0 = document.getElementById('u0');

u0.style.cursor = 'pointer';
if (bIE) u0.attachEvent("onclick", Clicku0);
else u0.addEventListener("click", Clicku0, true);
function Clicku0(e)
{

if (true) {

	self.location.href="Home.html" + GetQuerystring();

}

}

var u3 = document.getElementById('u3');
gv_vAlignTable['u3'] = 'top';
if (window.OnLoad) OnLoad();
