NS6 = (document.getElementById&&!document.all)
IE = (document.all)
NS = (navigator.appName=="Netscape" && navigator.appVersion.charAt(0)=="4")

tempBar='';barBuilt=0;ssmItems=new Array();

moving=setTimeout('null',1)
function moveOut() {
if ((NS6||NS)&&parseInt(ssm.left)<0 || IE && ssm.pixelLeft<0) {
clearTimeout(moving);moving = setTimeout('moveOut()', slideSpeed);slideMenu(10)}
else {clearTimeout(moving);moving=setTimeout('null',1)}};
function moveBack() {clearTimeout(moving);moving = setTimeout('moveBack1()', waitTime)}
function moveBack1() {
if ((NS6||NS) && parseInt(ssm.left)>(-menuWidth) || IE && ssm.pixelLeft>(-menuWidth)) {
clearTimeout(moving);moving = setTimeout('moveBack1()', slideSpeed);slideMenu(-10)}
else {clearTimeout(moving);moving=setTimeout('null',1)}}
function slideMenu(num){
if (IE) {ssm.pixelLeft += num;}
if (NS||NS6) {ssm.left = parseInt(ssm.left)+num;}
if (NS) {bssm.clip.right+=num;bssm2.clip.right+=num;}}

function makeStatic() {
if (NS||NS6) {winY = window.pageYOffset;}
if (IE) {winY = document.body.scrollTop;}
if (NS6||IE||NS) {
if (winY!=lastY&&winY>YOffset-staticYOffset) {
smooth = .2 * (winY - lastY - YOffset + staticYOffset);}
else if (YOffset-staticYOffset+lastY>YOffset-staticYOffset) {
smooth = .2 * (winY - lastY - (YOffset-(YOffset-winY)));}
else {smooth=0}
if(smooth > 0) smooth = Math.ceil(smooth);
else smooth = Math.floor(smooth);
if (IE) bssm.pixelTop+=smooth;
if (NS6||NS) bssm.top=parseInt(bssm.top)+smooth
lastY = lastY+smooth;
setTimeout('makeStatic()', 1)}}

function buildBar() {
if(barText.indexOf('<IMG')>-1) {tempBar=barText}
else{for (b=0;b<barText.length;b++) {tempBar+=barText.charAt(b)+"<BR>"}}
document.write('<td align="center" rowspan="100" width="'+barWidth+'" bgcolor="'+barBGColor+'" valign="'+barVAlign+'"><p align="center"><font face="'+barFontFamily+'" Size="'+barFontSize+'" COLOR="'+barFontColor+'"><B>'+tempBar+'</B></font></p></TD>')}

function initSlide() {
if (NS6){ssm=document.getElementById("thessm").style;bssm=document.getElementById("basessm").style;
bssm.clip="rect(0 "+document.getElementById("thessm").offsetWidth+" "+document.getElementById("thessm").offsetHeight+" 0)";ssm.visibility="visible";}
else if (IE) {ssm=document.all("thessm").style;bssm=document.all("basessm").style
bssm.clip="rect(0 "+thessm.offsetWidth+" "+thessm.offsetHeight+" 0)";bssm.visibility = "visible";}
else if (NS) {bssm=document.layers["basessm1"];
bssm2=bssm.document.layers["basessm2"];ssm=bssm2.document.layers["thessm"];
bssm2.clip.left=0;ssm.visibility = "show";}
if (menuIsStatic=="yes") makeStatic();}

function buildMenu() {
if (IE||NS6) {document.write('<DIV ID="basessm" style="visibility:hidden;Position : Absolute ;Left : '+XOffset+' ;Top : '+YOffset+' ;Z-Index : 20;width:'+(menuWidth+barWidth+10)+'"><DIV ID="thessm" style="Position : Absolute ;Left : '+(-menuWidth)+' ;Top : 0 ;Z-Index : 20;" onmouseover="moveOut()" onmouseout="moveBack()">')}
if (NS) {document.write('<LAYER name="basessm1" top="'+YOffset+'" LEFT='+XOffset+' visibility="show"><ILAYER name="basessm2"><LAYER visibility="hide" name="thessm" bgcolor="'+menuBGColor+'" left="'+(-menuWidth)+'" onmouseover="moveOut()" onmouseout="moveBack()">')}
if (NS6){document.write('<table border="0" cellpadding="0" cellspacing="0" width="'+(menuWidth+barWidth+2)+'" bgcolor="'+menuBGColor+'"><TR><TD>')}
document.write('<table border="0" cellpadding="0" cellspacing="1" width="'+(menuWidth+barWidth+2)+'" bgcolor="'+menuBGColor+'">');
for(i=0;i<ssmItems.length;i++) {
if(!ssmItems[i][3]){ssmItems[i][3]=menuCols;ssmItems[i][5]=menuWidth-1}
else if(ssmItems[i][3]!=menuCols)ssmItems[i][5]=Math.round(menuWidth*(ssmItems[i][3]/menuCols)-1);
if(ssmItems[i-1]&&ssmItems[i-1][4]!="no"){document.write('<TR>')}
if(!ssmItems[i][1]){
document.write('<td bgcolor="'+hdrBGColor+'" HEIGHT="'+hdrHeight+'" ALIGN="'+hdrAlign+'" VALIGN="'+hdrVAlign+'" WIDTH="'+ssmItems[i][5]+'" COLSPAN="'+ssmItems[i][3]+'">&nbsp;<font face="'+hdrFontFamily+'" Size="'+hdrFontSize+'" COLOR="'+hdrFontColor+'"><b>'+ssmItems[i][0]+'</b></font></td>')}
else {if(!ssmItems[i][2])ssmItems[i][2]=linkTarget;
document.write('<TD BGCOLOR="'+linkBGColor+'" onmouseover="bgColor=\''+linkOverBGColor+'\'" onmouseout="bgColor=\''+linkBGColor+'\'" WIDTH="'+ssmItems[i][5]+'" COLSPAN="'+ssmItems[i][3]+'"><ILAYER><LAYER onmouseover="bgColor=\''+linkOverBGColor+'\'" onmouseout="bgColor=\''+linkBGColor+'\'" WIDTH="100%" ALIGN="'+linkAlign+'"><DIV ALIGN="'+linkAlign+'"><FONT face="'+linkFontFamily+'" Size="'+linkFontSize+'">&nbsp;<A HREF="'+ssmItems[i][1]+'" target="'+ssmItems[i][2]+'" CLASS="ssmItems">'+ssmItems[i][0]+'</DIV></LAYER></ILAYER></TD>')}
if(ssmItems[i][4]!="no"&&barBuilt==0){buildBar();barBuilt=1}
if(ssmItems[i][4]!="no"){document.write('</TR>')}}
document.write('</table>')
if (NS6){document.write('</TD></TR></TABLE>')}
if (IE||NS6) {document.write('</DIV></DIV>')}
if (NS) {document.write('</LAYER></ILAYER></LAYER>')}
theleft=-menuWidth;lastY=0;setTimeout('initSlide();', 1)}

<!--
<!--YOffset=95;設定選單距離從上面算下來的位置-->
YOffset=95; 
<!--XOffset=0;設定選單距離從左邊算過來的位置-->
XOffset=0;
staticYOffset=30; 
slideSpeed=20 
waitTime=100; 
<!--menuBGColor="#000000";設定選單外框顏色-->
menuBGColor="#000000";
menuIsStatic="yes"; 
<!--menuWidth=100;設定選單的寬度-->
menuWidth=100; 
menuCols=2;
<!--hdrFontFamily="verdana";設定連結目錄區域的字型-->
hdrFontFamily="verdana";
<!--hdrFontSize="2";設定連結目錄區域的字體大小-->
hdrFontSize="2";
<!--hdrFontColor="#FFFFFF";設定選單目錄標題文字顏色-->
hdrFontColor="#FFFFFF";
<!--hdrBGColor="#000000";設定選單目錄標題背景顏色-->
hdrBGColor="#000000";
hdrAlign="center";
hdrVAlign="center";
<!--hdrHeight="10";設定選單目錄區域的高度-->
hdrHeight="10";
<!--linkFontFamily="Verdana";設定連結文字區域的字型-->
linkFontFamily="Verdana";
<!--linkFontSize="2";設定連結文字區域的字體大小-->
linkFontSize="2";
<!--linkBGColor="#FFFFFF";;設定選單連結項目區域背景顏色-->
linkBGColor="#FFFFFF";
<!--linkOverBGColor="#cccccc";設定滑鼠指標移至連結區域的背景顏色-->
linkOverBGColor="#cccccc";
<!--linkTarget="_blank";設定視窗開啟的方式，blank表示為開新視窗連結，top表示為在同一視窗開啟-->
linkTarget="_blank";
<!--linkAlign="center";設定連結的文字顯示的區域，center為置中-->
linkAlign="center";
<!--barBGColor="#000000";設定目錄選單的背景顏色-->
barBGColor="#000000";
<!--barFontFamily="Verdana";設定文字的字型-->
barFontFamily="Verdana";
<!--barFontSize="2";設定文字的大小-->
barFontSize="2";
<!--barFontColor="#FFFFFF";設定目錄選單的文字顏色-->
barFontColor="#FFFFFF";
barVAlign="center";
<!--barWidth=20;設定★隱藏式目錄選單★區域的寬度--> 
barWidth=20; 
<!--在barText="隱藏式目錄選單";中修改目錄選單須顯示的文字-->
barText="隱藏式目錄選單"; 
<!--在ssmItems[0]=["+連結目錄+"]中修改顯示連結目錄的文字-->
ssmItems[0]=["+連結目錄+"] 
<!--設定連結網址文字_開始-->
ssmItems[1]=["崑山科大首頁", "http://www.ksu.edu.tw/index.aspx"]
ssmItems[2]=["電算中心", "http://www.ksu.edu.tw/cht/unit/D/A/CC/"]
ssmItems[3]=["數位信封系統", "http://www.ksu.edu.tw/cht/utility/eForm/docList.aspx?Func=NW"]

<!--設定連結網址文字_結束-->
buildMenu();
-->
<!--隱藏式連結選單_結束-->