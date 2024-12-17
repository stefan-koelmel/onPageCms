
var PageName = 'Home';
var PageId = 'p57acbab34c334278863a7a7654567338'
var PageUrl = 'Home.html'
document.title = 'Home';

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

if ((GetGlobalVariableValue('$login')) == (PopulateVariables('1'))) {

	SetPanelStateu14("pd1u14");

}
else
if (true) {

	SetPanelStateu14("pd0u14");

}

if (true) {

SetWidgetRichText('u23', PopulateVariables('<span style=" color:#000000; font-size:13px;">[[page]]</span>'));

SetWidgetRichText('u24', PopulateVariables('<span style=" color:#000000; font-size:13px;">[[pageName]]</span>'));

SetWidgetRichText('u25', PopulateVariables('<span style=" color:#000000; font-size:13px;">[[userLevel]]</span>'));

SetWidgetRichText('u26', PopulateVariables('<span style=" color:#000000; font-size:13px;">[[wireframe]]</span>'));

SetWidgetRichText('u28', PopulateVariables('<span style=" color:#000000; font-size:13px;">[[OnLoadVariable]]</span>'));

}

}

eval(GetDynamicPanelScript('u6', 4));

eval(GetDynamicPanelScript('u14', 2));

var u16 = document.getElementById('u16');
gv_vAlignTable['u16'] = 'center';
var u7 = document.getElementById('u7');
gv_vAlignTable['u7'] = 'top';
var u28 = document.getElementById('u28');
gv_vAlignTable['u28'] = 'top';
var u15 = document.getElementById('u15');

u15.style.cursor = 'pointer';
if (bIE) u15.attachEvent("onclick", Clicku15);
else u15.addEventListener("click", Clicku15, true);
function Clicku15(e)
{

if (true) {

SetGlobalVariableValue('$login', PopulateVariables('1'));

	self.location.href="Resources/reload.html#" + encodeURI(PageUrl + GetQuerystring());


}

}

var u2 = document.getElementById('u2');

u2.style.cursor = 'pointer';
if (bIE) u2.attachEvent("onclick", Clicku2);
else u2.addEventListener("click", Clicku2, true);
function Clicku2(e)
{

if (true) {

	SetPanelStateu6("pd2u6");

ApplyImageAndTextStyles('o', 'u0', 'u1', '', false);
ApplyImageAndTextStyles('s', 'u2', 'u3', sJsonu3, false);
ApplyImageAndTextStyles('o', 'u4', 'u5', '', false);
}

}

var u19 = document.getElementById('u19');
gv_vAlignTable['u19'] = 'top';
var u13 = document.getElementById('u13');
gv_vAlignTable['u13'] = 'center';
var u22 = document.getElementById('u22');
gv_vAlignTable['u22'] = 'top';
var u12 = document.getElementById('u12');

u12.style.cursor = 'pointer';
if (bIE) u12.attachEvent("onclick", Clicku12);
else u12.addEventListener("click", Clicku12, true);
function Clicku12(e)
{

if (true) {

	self.location.href="subPage.html" + GetQuerystring();

}

}

var u5 = document.getElementById('u5');
gv_vAlignTable['u5'] = 'center';
var u8 = document.getElementById('u8');
gv_vAlignTable['u8'] = 'top';
var u10 = document.getElementById('u10');

u10.style.cursor = 'pointer';
if (bIE) u10.attachEvent("onclick", Clicku10);
else u10.addEventListener("click", Clicku10, true);
function Clicku10(e)
{

if (true) {

	top.location.href="/onPage/index.php"  + GetQuerystring(); //cmsChanged

}

}

var u0 = document.getElementById('u0');

u0.style.cursor = 'pointer';
if (bIE) u0.attachEvent("onclick", Clicku0);
else u0.addEventListener("click", Clicku0, true);
function Clicku0(e)
{

if (true) {

	SetPanelStateu6("pd1u6");

ApplyImageAndTextStyles('s', 'u0', 'u1', sJsonu1, false);
ApplyImageAndTextStyles('o', 'u2', 'u3', '', false);
ApplyImageAndTextStyles('o', 'u4', 'u5', '', false);
}

}

var u26 = document.getElementById('u26');
gv_vAlignTable['u26'] = 'top';
var u25 = document.getElementById('u25');
gv_vAlignTable['u25'] = 'top';
var u21 = document.getElementById('u21');
gv_vAlignTable['u21'] = 'top';
var u17 = document.getElementById('u17');

u17.style.cursor = 'pointer';
if (bIE) u17.attachEvent("onclick", Clicku17);
else u17.addEventListener("click", Clicku17, true);
function Clicku17(e)
{

if (true) {

SetGlobalVariableValue('$login', PopulateVariables('0'));

	self.location.href="Resources/reload.html#" + encodeURI(PageUrl + GetQuerystring());


}

}

var u3 = document.getElementById('u3');
gv_vAlignTable['u3'] = 'center';
var u23 = document.getElementById('u23');
gv_vAlignTable['u23'] = 'top';
var u14 = document.getElementById('u14');

var u6 = document.getElementById('u6');

var u9 = document.getElementById('u9');
gv_vAlignTable['u9'] = 'top';
var u20 = document.getElementById('u20');
gv_vAlignTable['u20'] = 'top';
var u1 = document.getElementById('u1');
gv_vAlignTable['u1'] = 'center';
var u11 = document.getElementById('u11');
gv_vAlignTable['u11'] = 'center';
var u18 = document.getElementById('u18');
gv_vAlignTable['u18'] = 'center';
var u24 = document.getElementById('u24');
gv_vAlignTable['u24'] = 'top';
var u4 = document.getElementById('u4');

u4.style.cursor = 'pointer';
if (bIE) u4.attachEvent("onclick", Clicku4);
else u4.addEventListener("click", Clicku4, true);
function Clicku4(e)
{

if (true) {

	SetPanelStateu6("pd3u6");

ApplyImageAndTextStyles('o', 'u0', 'u1', '', false);
ApplyImageAndTextStyles('o', 'u2', 'u3', '', false);
ApplyImageAndTextStyles('s', 'u4', 'u5', sJsonu5, false);
}

}

var u27 = document.getElementById('u27');
gv_vAlignTable['u27'] = 'top';
if (window.OnLoad) OnLoad();
