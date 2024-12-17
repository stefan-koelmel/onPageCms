//function actb(obj,ca){
//  2      /* ---- Public Variables ---- */
//  3      this.actb_timeOut = -1; // Autocomplete Timeout in ms (-1: autocomplete never time out)
//  4      this.actb_lim = 4;    // Number of elements autocomplete can show (-1: no limit)
//  5      this.actb_firstText = false; // should the auto complete be limited to the beginning of keyword?
//  6      this.actb_mouse = true; // Enable Mouse Support
//  7      this.actb_delimiter = new Array(';',',');  // Delimiter for multiple autocomplete. Set it to empty array for single autocomplete
//  8      this.actb_startcheck = 1; // Show widget only after this number of characters is typed in.
//  9      /* ---- Public Variables ---- */
// 10
// 11      /* --- Styles --- */
// 12      this.actb_bgColor = '#888888';
// 13      this.actb_textColor = '#FFFFFF';
// 14      this.actb_hColor = '#000000';
// 15      this.actb_fFamily = 'Verdana';
// 16      this.actb_fSize = '11px';
// 17      this.actb_hStyle = 'text-decoration:underline;font-weight="bold"';
// 18      /* --- Styles --- */
// 19
// 20      /* ---- Private Variables ---- */
// 21      var actb_delimwords = new Array();
// 22      var actb_cdelimword = 0;
// 23      var actb_delimchar = new Array();
// 24      var actb_display = false;
// 25      var actb_pos = 0;
// 26      var actb_total = 0;
// 27      var actb_curr = null;
// 28      var actb_rangeu = 0;
// 29      var actb_ranged = 0;
// 30      var actb_bool = new Array();
// 31      var actb_pre = 0;
// 32      var actb_toid;
// 33      var actb_tomake = false;
// 34      var actb_getpre = "";
// 35      var actb_mouse_on_list = 1;
// 36      var actb_kwcount = 0;
// 37      var actb_caretmove = false;
// 38      this.actb_keywords = new Array();
// 39      /* ---- Private Variables---- */
// 40
// 41      this.actb_keywords = ca;
// 42      var actb_self = this;
// 43
// 44      actb_curr = obj;
// 45
// 46      addEvent(actb_curr,"focus",actb_setup);
// 47      function actb_setup(){
// 48          addEvent(document,"keydown",actb_checkkey);
// 49          addEvent(actb_curr,"blur",actb_clear);
// 50          addEvent(document,"keypress",actb_keypress);
// 51      }
// 52
// 53      function actb_clear(evt){
// 54          if (!evt) evt = event;
// 55          removeEvent(document,"keydown",actb_checkkey);
// 56          removeEvent(actb_curr,"blur",actb_clear);
// 57          removeEvent(document,"keypress",actb_keypress);
// 58          actb_removedisp();
// 59      }
// 60      function actb_parse(n){
// 61          if (actb_self.actb_delimiter.length > 0){
// 62              var t = actb_delimwords[actb_cdelimword].trim().addslashes();
// 63              var plen = actb_delimwords[actb_cdelimword].trim().length;
// 64          }else{
// 65              var t = actb_curr.value.addslashes();
// 66              var plen = actb_curr.value.length;
// 67          }
// 68          var tobuild = '';
// 69          var i;
// 70
// 71          if (actb_self.actb_firstText){
// 72              var re = new RegExp("^" + t, "i");
// 73          }else{
// 74              var re = new RegExp(t, "i");
// 75          }
// 76          var p = n.search(re);
// 77
// 78          for (i=0;i<p;i++){
// 79              tobuild += n.substr(i,1);
// 80          }
// 81          tobuild += "<font style='"+(actb_self.actb_hStyle)+"'>"
// 82          for (i=p;i<plen+p;i++){
// 83              tobuild += n.substr(i,1);
// 84          }
// 85          tobuild += "</font>";
// 86              for (i=plen+p;i<n.length;i++){
// 87              tobuild += n.substr(i,1);
// 88          }
// 89          return tobuild;
// 90      }
// 91      function actb_generate(){
// 92          if (document.getElementById('tat_table')){ actb_display = false;document.body.removeChild(document.getElementById('tat_table')); }
// 93          if (actb_kwcount == 0){
// 94              actb_display = false;
// 95              return;
// 96          }
// 97          a = document.createElement('table');
// 98          a.cellSpacing='1px';
// 99          a.cellPadding='2px';
//100          a.style.position='absolute';
//101          a.style.top = eval(curTop(actb_curr) + actb_curr.offsetHeight) + "px";
//102          a.style.left = curLeft(actb_curr) + "px";
//103          a.style.backgroundColor=actb_self.actb_bgColor;
//104          a.id = 'tat_table';
//105          document.body.appendChild(a);
//106          var i;
//107          var first = true;
//108          var j = 1;
//109          if (actb_self.actb_mouse){
//110              a.onmouseout = actb_table_unfocus;
//111              a.onmouseover = actb_table_focus;
//112          }
//113          var counter = 0;
//114          for (i=0;i<actb_self.actb_keywords.length;i++){
//115              if (actb_bool[i]){
//116                  counter++;
//117                  r = a.insertRow(-1);
//118                  if (first && !actb_tomake){
//119                      r.style.backgroundColor = actb_self.actb_hColor;
//120                      first = false;
//121                      actb_pos = counter;
//122                  }else if(actb_pre == i){
//123                      r.style.backgroundColor = actb_self.actb_hColor;
//124                      first = false;
//125                      actb_pos = counter;
//126                  }else{
//127                      r.style.backgroundColor = actb_self.actb_bgColor;
//128                  }
//129                  r.id = 'tat_tr'+(j);
//130                  c = r.insertCell(-1);
//131                  c.style.color = actb_self.actb_textColor;
//132                  c.style.fontFamily = actb_self.actb_fFamily;
//133                  c.style.fontSize = actb_self.actb_fSize;
//134                  c.innerHTML = actb_parse(actb_self.actb_keywords[i]);
//135                  c.id = 'tat_td'+(j);
//136                  c.setAttribute('pos',j);
//137                  if (actb_self.actb_mouse){
//138                      c.style.cursor = 'pointer';
//139                      c.onclick=actb_mouseclick;
//140                      c.onmouseover = actb_table_highlight;
//141                  }
//142                  j++;
//143              }
//144              if (j - 1 == actb_self.actb_lim && j < actb_total){
//145                  r = a.insertRow(-1);
//146                  r.style.backgroundColor = actb_self.actb_bgColor;
//147                  c = r.insertCell(-1);
//148                  c.style.color = actb_self.actb_textColor;
//149                  c.style.fontFamily = 'arial narrow';
//150                  c.style.fontSize = actb_self.actb_fSize;
//151                  c.align='center';
//152                  replaceHTML(c,'\\/');
//153                  if (actb_self.actb_mouse){
//154                      c.style.cursor = 'pointer';
//155                      c.onclick = actb_mouse_down;
//156                  }
//157                  break;
//158              }
//159          }
//160          actb_rangeu = 1;
//161          actb_ranged = j-1;
//162          actb_display = true;
//163          if (actb_pos <= 0) actb_pos = 1;
//164      }
//165      function actb_remake(){
//166          document.body.removeChild(document.getElementById('tat_table'));
//167          a = document.createElement('table');
//168          a.cellSpacing='1px';
//169          a.cellPadding='2px';
//170          a.style.position='absolute';
//171          a.style.top = eval(curTop(actb_curr) + actb_curr.offsetHeight) + "px";
//172          a.style.left = curLeft(actb_curr) + "px";
//173          a.style.backgroundColor=actb_self.actb_bgColor;
//174          a.id = 'tat_table';
//175          if (actb_self.actb_mouse){
//176              a.onmouseout= actb_table_unfocus;
//177              a.onmouseover=actb_table_focus;
//178          }
//179          document.body.appendChild(a);
//180          var i;
//181          var first = true;
//182          var j = 1;
//183          if (actb_rangeu > 1){
//184              r = a.insertRow(-1);
//185              r.style.backgroundColor = actb_self.actb_bgColor;
//186              c = r.insertCell(-1);
//187              c.style.color = actb_self.actb_textColor;
//188              c.style.fontFamily = 'arial narrow';
//189              c.style.fontSize = actb_self.actb_fSize;
//190              c.align='center';
//191              replaceHTML(c,'/\\');
//192              if (actb_self.actb_mouse){
//193                  c.style.cursor = 'pointer';
//194                  c.onclick = actb_mouse_up;
//195              }
//196          }
//197          for (i=0;i<actb_self.actb_keywords.length;i++){
//198              if (actb_bool[i]){
//199                  if (j >= actb_rangeu && j <= actb_ranged){
//200                      r = a.insertRow(-1);
//201                      r.style.backgroundColor = actb_self.actb_bgColor;
//202                      r.id = 'tat_tr'+(j);
//203                      c = r.insertCell(-1);
//204                      c.style.color = actb_self.actb_textColor;
//205                      c.style.fontFamily = actb_self.actb_fFamily;
//206                      c.style.fontSize = actb_self.actb_fSize;
//207                      c.innerHTML = actb_parse(actb_self.actb_keywords[i]);
//208                      c.id = 'tat_td'+(j);
//209                      c.setAttribute('pos',j);
//210                      if (actb_self.actb_mouse){
//211                          c.style.cursor = 'pointer';
//212                          c.onclick=actb_mouseclick;
//213                          c.onmouseover = actb_table_highlight;
//214                      }
//215                      j++;
//216                  }else{
//217                      j++;
//218                  }
//219              }
//220              if (j > actb_ranged) break;
//221          }
//222          if (j-1 < actb_total){
//223              r = a.insertRow(-1);
//224              r.style.backgroundColor = actb_self.actb_bgColor;
//225              c = r.insertCell(-1);
//226              c.style.color = actb_self.actb_textColor;
//227              c.style.fontFamily = 'arial narrow';
//228              c.style.fontSize = actb_self.actb_fSize;
//229              c.align='center';
//230              replaceHTML(c,'\\/');
//231              if (actb_self.actb_mouse){
//232                  c.style.cursor = 'pointer';
//233                  c.onclick = actb_mouse_down;
//234              }
//235          }
//236      }
//237      function actb_goup(){
//238          if (!actb_display) return;
//239          if (actb_pos == 1) return;
//240          document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_self.actb_bgColor;
//241          actb_pos--;
//242          if (actb_pos < actb_rangeu) actb_moveup();
//243          document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_self.actb_hColor;
//244          if (actb_toid) clearTimeout(actb_toid);
//245          if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list=0;actb_removedisp();},actb_self.actb_timeOut);
//246      }
//247      function actb_godown(){
//248          if (!actb_display) return;
//249          if (actb_pos == actb_total) return;
//250          document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_self.actb_bgColor;
//251          actb_pos++;
//252          if (actb_pos > actb_ranged) actb_movedown();
//253          document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_self.actb_hColor;
//254          if (actb_toid) clearTimeout(actb_toid);
//255          if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list=0;actb_removedisp();},actb_self.actb_timeOut);
//256      }
//257      function actb_movedown(){
//258          actb_rangeu++;
//259          actb_ranged++;
//260          actb_remake();
//261      }
//262      function actb_moveup(){
//263          actb_rangeu--;
//264          actb_ranged--;
//265          actb_remake();
//266      }
//267
//268      /* Mouse */
//269      function actb_mouse_down(){
//270          document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_self.actb_bgColor;
//271          actb_pos++;
//272          actb_movedown();
//273          document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_self.actb_hColor;
//274          actb_curr.focus();
//275          actb_mouse_on_list = 0;
//276          if (actb_toid) clearTimeout(actb_toid);
//277          if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list=0;actb_removedisp();},actb_self.actb_timeOut);
//278      }
//279      function actb_mouse_up(evt){
//280          if (!evt) evt = event;
//281          if (evt.stopPropagation){
//282              evt.stopPropagation();
//283          }else{
//284              evt.cancelBubble = true;
//285          }
//286          document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_self.actb_bgColor;
//287          actb_pos--;
//288          actb_moveup();
//289          document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_self.actb_hColor;
//290          actb_curr.focus();
//291          actb_mouse_on_list = 0;
//292          if (actb_toid) clearTimeout(actb_toid);
//293          if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list=0;actb_removedisp();},actb_self.actb_timeOut);
//294      }
//295      function actb_mouseclick(evt){
//296          if (!evt) evt = event;
//297          if (!actb_display) return;
//298          actb_mouse_on_list = 0;
//299          actb_pos = this.getAttribute('pos');
//300          actb_penter();
//301      }
//302      function actb_table_focus(){
//303          actb_mouse_on_list = 1;
//304      }
//305      function actb_table_unfocus(){
//306          actb_mouse_on_list = 0;
//307          if (actb_toid) clearTimeout(actb_toid);
//308          if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list = 0;actb_removedisp();},actb_self.actb_timeOut);
//309      }
//310      function actb_table_highlight(){
//311          actb_mouse_on_list = 1;
//312          document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_self.actb_bgColor;
//313          actb_pos = this.getAttribute('pos');
//314          while (actb_pos < actb_rangeu) actb_moveup();
//315          while (actb_pos > actb_ranged) actb_movedown();
//316          document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_self.actb_hColor;
//317          if (actb_toid) clearTimeout(actb_toid);
//318          if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list = 0;actb_removedisp();},actb_self.actb_timeOut);
//319      }
//320      /* ---- */
//321
//322      function actb_insertword(a){
//323          if (actb_self.actb_delimiter.length > 0){
//324              str = '';
//325              l=0;
//326              for (i=0;i<actb_delimwords.length;i++){
//327                  if (actb_cdelimword == i){
//328                      prespace = postspace = '';
//329                      gotbreak = false;
//330                      for (j=0;j<actb_delimwords[i].length;++j){
//331                          if (actb_delimwords[i].charAt(j) != ' '){
//332                              gotbreak = true;
//333                              break;
//334                          }
//335                          prespace += ' ';
//336                      }
//337                      for (j=actb_delimwords[i].length-1;j>=0;--j){
//338                          if (actb_delimwords[i].charAt(j) != ' ') break;
//339                          postspace += ' ';
//340                      }
//341                      str += prespace;
//342                      str += a;
//343                      l = str.length;
//344                      if (gotbreak) str += postspace;
//345                  }else{
//346                      str += actb_delimwords[i];
//347                  }
//348                  if (i != actb_delimwords.length - 1){
//349                      str += actb_delimchar[i];
//350                  }
//351              }
//352              actb_curr.value = str;
//353              setCaret(actb_curr,l);
//354          }else{
//355              actb_curr.value = a;
//356          }
//357          actb_mouse_on_list = 0;
//358          actb_removedisp();
//359      }
//360      function actb_penter(){
//361          if (!actb_display) return;
//362          actb_display = false;
//363          var word = '';
//364          var c = 0;
//365          for (var i=0;i<=actb_self.actb_keywords.length;i++){
//366              if (actb_bool[i]) c++;
//367              if (c == actb_pos){
//368                  word = actb_self.actb_keywords[i];
//369                  break;
//370              }
//371          }
//372          actb_insertword(word);
//373          l = getCaretStart(actb_curr);
//374      }
//375      function actb_removedisp(){
//376          if (actb_mouse_on_list==0){
//377              actb_display = 0;
//378              if (document.getElementById('tat_table')){ document.body.removeChild(document.getElementById('tat_table')); }
//379              if (actb_toid) clearTimeout(actb_toid);
//380          }
//381      }
//382      function actb_keypress(e){
//383          if (actb_caretmove) stopEvent(e);
//384          return !actb_caretmove;
//385      }
//386      function actb_checkkey(evt){
//387          if (!evt) evt = event;
//388          a = evt.keyCode;
//389          caret_pos_start = getCaretStart(actb_curr);
//390          actb_caretmove = 0;
//391          switch (a){
//392              case 38:
//393                  actb_goup();
//394                  actb_caretmove = 1;
//395                  return false;
//396                  break;
//397              case 40:
//398                  actb_godown();
//399                  actb_caretmove = 1;
//400                  return false;
//401                  break;
//402              case 13: case 9:
//403                  if (actb_display){
//404                      actb_caretmove = 1;
//405                      actb_penter();
//406                      return false;
//407                  }else{
//408                      return true;
//409                  }
//410                  break;
//411              default:
//412                  setTimeout(function(){actb_tocomplete(a)},50);
//413                  break;
//414          }
//415      }
//416
//417      function actb_tocomplete(kc){
//418          if (kc == 38 || kc == 40 || kc == 13) return;
//419          var i;
//420          if (actb_display){
//421              var word = 0;
//422              var c = 0;
//423              for (var i=0;i<=actb_self.actb_keywords.length;i++){
//424                  if (actb_bool[i]) c++;
//425                  if (c == actb_pos){
//426                      word = i;
//427                      break;
//428                  }
//429              }
//430              actb_pre = word;
//431          }else{ actb_pre = -1};
//432
//433          if (actb_curr.value == ''){
//434              actb_mouse_on_list = 0;
//435              actb_removedisp();
//436              return;
//437          }
//438          if (actb_self.actb_delimiter.length > 0){
//439              caret_pos_start = getCaretStart(actb_curr);
//440              caret_pos_end = getCaretEnd(actb_curr);
//441
//442              delim_split = '';
//443              for (i=0;i<actb_self.actb_delimiter.length;i++){
//444                  delim_split += actb_self.actb_delimiter[i];
//445              }
//446              delim_split = delim_split.addslashes();
//447              delim_split_rx = new RegExp("(["+delim_split+"])");
//448              c = 0;
//449              actb_delimwords = new Array();
//450              actb_delimwords[0] = '';
//451              for (i=0,j=actb_curr.value.length;i<actb_curr.value.length;i++,j--){
//452                  if (actb_curr.value.substr(i,j).search(delim_split_rx) == 0){
//453                      ma = actb_curr.value.substr(i,j).match(delim_split_rx);
//454                      actb_delimchar[c] = ma[1];
//455                      c++;
//456                      actb_delimwords[c] = '';
//457                  }else{
//458                      actb_delimwords[c] += actb_curr.value.charAt(i);
//459                  }
//460              }
//461
//462              var l = 0;
//463              actb_cdelimword = -1;
//464              for (i=0;i<actb_delimwords.length;i++){
//465                  if (caret_pos_end >= l && caret_pos_end <= l + actb_delimwords[i].length){
//466                      actb_cdelimword = i;
//467                  }
//468                  l+=actb_delimwords[i].length + 1;
//469              }
//470              var ot = actb_delimwords[actb_cdelimword].trim();
//471              var t = actb_delimwords[actb_cdelimword].addslashes().trim();
//472          }else{
//473              var ot = actb_curr.value;
//474              var t = actb_curr.value.addslashes();
//475          }
//476          if (ot.length == 0){
//477              actb_mouse_on_list = 0;
//478              actb_removedisp();
//479          }
//480          if (ot.length < actb_self.actb_startcheck) return this;
//481          if (actb_self.actb_firstText){
//482              var re = new RegExp("^" + t, "i");
//483          }else{
//484              var re = new RegExp(t, "i");
//485          }
//486
//487          actb_total = 0;
//488          actb_tomake = false;
//489          actb_kwcount = 0;
//490          for (i=0;i<actb_self.actb_keywords.length;i++){
//491              actb_bool[i] = false;
//492              if (re.test(actb_self.actb_keywords[i])){
//493                  actb_total++;
//494                  actb_bool[i] = true;
//495                  actb_kwcount++;
//496                  if (actb_pre == i) actb_tomake = true;
//497              }
//498          }
//499
//500          if (actb_toid) clearTimeout(actb_toid);
//501          if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list = 0;actb_removedisp();},actb_self.actb_timeOut);
//502          actb_generate();
//503      }
//504      return this;
//505  }
// http://www.codeproject.com/script/Articles/ViewDownloads.aspx?aid=8020

//http://jqueryui.com/demos/autocomplete/