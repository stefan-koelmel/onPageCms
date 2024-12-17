
var PageName = 'Home';
var PageId = 'pe9e40b60a5ef4f1499634794b5ec3bd4'
var PageUrl = 'Home.html'
document.title = 'Home';

if (top.location != self.location)
{
	if (parent.HandleMainFrameChanged) {
		parent.HandleMainFrameChanged();
	}
}

var $OnLoadVariable = '';

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
    return '#OnLoadVariable=' + encodeURIComponent($OnLoadVariable) + '&CSUM=1';
}

function PopulateVariables(value) {
  value = value.replace(/\[\[OnLoadVariable\]\]/g, $OnLoadVariable);
  value = value.replace(/\[\[PageName\]\]/g, PageName);
  return value;
}

function OnLoad(e) {

}

eval(GetDynamicPanelScript('u0', 3));

var u20 = document.getElementById('u20');

u20.style.cursor = 'pointer';
if (bIE) u20.attachEvent("onclick", Clicku20);
else u20.addEventListener("click", Clicku20, true);
function Clicku20(e)
{

if (true) {

	self.location.href="Color.html" + GetQuerystring();

}

}

if (bIE) u20.attachEvent("onmouseover", MouseOveru20);
else u20.addEventListener("mouseover", MouseOveru20, true);
function MouseOveru20(e)
{
if (!IsTrueMouseOver('u20',e)) return;
if (true) {

}

}

if (bIE) u20.attachEvent("onmouseout", MouseOutu20);
else u20.addEventListener("mouseout", MouseOutu20, true);
function MouseOutu20(e)
{
if (!IsTrueMouseOut('u20',e)) return;
if (true) {

	SetPanelStateu0("pd0u0");

}

}

var u21 = document.getElementById('u21');

var u22 = document.getElementById('u22');
gv_vAlignTable['u22'] = 'center';
var u23 = document.getElementById('u23');

var u24 = document.getElementById('u24');
gv_vAlignTable['u24'] = 'center';
var u25 = document.getElementById('u25');

var u26 = document.getElementById('u26');
gv_vAlignTable['u26'] = 'center';
var u27 = document.getElementById('u27');

var u28 = document.getElementById('u28');
gv_vAlignTable['u28'] = 'center';
var u29 = document.getElementById('u29');

if (bIE) u29.attachEvent("onmouseout", MouseOutu29);
else u29.addEventListener("mouseout", MouseOutu29, true);
function MouseOutu29(e)
{
if (!IsTrueMouseOut('u29',e)) return;
if (true) {

	SetPanelStateu0("pd0u0");

}

}

var u30 = document.getElementById('u30');
gv_vAlignTable['u30'] = 'center';
var u31 = document.getElementById('u31');
gv_vAlignTable['u31'] = 'top';
var u32 = document.getElementById('u32');

u32.style.cursor = 'pointer';
if (bIE) u32.attachEvent("onclick", Clicku32);
else u32.addEventListener("click", Clicku32, true);
function Clicku32(e)
{

if (true) {

	self.location.href="BW.html" + GetQuerystring();

}

}

if (bIE) u32.attachEvent("onmouseover", MouseOveru32);
else u32.addEventListener("mouseover", MouseOveru32, true);
function MouseOveru32(e)
{
if (!IsTrueMouseOver('u32',e)) return;
if (true) {

}

}

if (bIE) u32.attachEvent("onmouseout", MouseOutu32);
else u32.addEventListener("mouseout", MouseOutu32, true);
function MouseOutu32(e)
{
if (!IsTrueMouseOut('u32',e)) return;
if (true) {

	SetPanelStateu0("pd0u0");

}

}

var u33 = document.getElementById('u33');
gv_vAlignTable['u33'] = 'top';
var u34 = document.getElementById('u34');

var u35 = document.getElementById('u35');
gv_vAlignTable['u35'] = 'center';
var u36 = document.getElementById('u36');

u36.style.cursor = 'pointer';
if (bIE) u36.attachEvent("onclick", Clicku36);
else u36.addEventListener("click", Clicku36, true);
function Clicku36(e)
{

if (true) {

}

}
gv_vAlignTable['u36'] = 'top';
var u37 = document.getElementById('u37');

u37.style.cursor = 'pointer';
if (bIE) u37.attachEvent("onclick", Clicku37);
else u37.addEventListener("click", Clicku37, true);
function Clicku37(e)
{

if (true) {

}

}
gv_vAlignTable['u37'] = 'top';
var u0 = document.getElementById('u0');

var u1 = document.getElementById('u1');

var u2 = document.getElementById('u2');
gv_vAlignTable['u2'] = 'center';
var u3 = document.getElementById('u3');
gv_vAlignTable['u3'] = 'top';
var u4 = document.getElementById('u4');
gv_vAlignTable['u4'] = 'top';
var u5 = document.getElementById('u5');

if (bIE) u5.attachEvent("onmouseover", MouseOveru5);
else u5.addEventListener("mouseover", MouseOveru5, true);
function MouseOveru5(e)
{
if (!IsTrueMouseOver('u5',e)) return;
if (true) {

	SetPanelStateu0("pd2u0");

}

}

var u6 = document.getElementById('u6');
gv_vAlignTable['u6'] = 'top';
var u7 = document.getElementById('u7');

u7.style.cursor = 'pointer';
if (bIE) u7.attachEvent("onclick", Clicku7);
else u7.addEventListener("click", Clicku7, true);
function Clicku7(e)
{

if (true) {

}

}

if (bIE) u7.attachEvent("onmouseover", MouseOveru7);
else u7.addEventListener("mouseover", MouseOveru7, true);
function MouseOveru7(e)
{
if (!IsTrueMouseOver('u7',e)) return;
if (true) {

	SetPanelStateu0("pd1u0");

}

}

if (bIE) u7.attachEvent("onmouseout", MouseOutu7);
else u7.addEventListener("mouseout", MouseOutu7, true);
function MouseOutu7(e)
{
if (!IsTrueMouseOut('u7',e)) return;
if (true) {

}

}

var u8 = document.getElementById('u8');

var u9 = document.getElementById('u9');
gv_vAlignTable['u9'] = 'center';
var u10 = document.getElementById('u10');

var u11 = document.getElementById('u11');
gv_vAlignTable['u11'] = 'center';
var u12 = document.getElementById('u12');

if (bIE) u12.attachEvent("onmouseout", MouseOutu12);
else u12.addEventListener("mouseout", MouseOutu12, true);
function MouseOutu12(e)
{
if (!IsTrueMouseOut('u12',e)) return;
if (true) {

	SetPanelStateu0("pd0u0");

}

}

var u13 = document.getElementById('u13');
gv_vAlignTable['u13'] = 'center';
var u14 = document.getElementById('u14');

var u15 = document.getElementById('u15');
gv_vAlignTable['u15'] = 'center';
var u16 = document.getElementById('u16');

var u17 = document.getElementById('u17');
gv_vAlignTable['u17'] = 'center';
var u18 = document.getElementById('u18');
gv_vAlignTable['u18'] = 'top';
var u19 = document.getElementById('u19');
gv_vAlignTable['u19'] = 'top';
if (window.OnLoad) OnLoad();
