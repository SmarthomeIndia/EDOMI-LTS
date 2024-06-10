###[DEF]###
[name	=Diagramm]

[folderid=161]
[xsize	=250]
[ysize	=200]

[var1	=0 #root=130]
[var2	=0]
[var3	=3]
[var4	=7]
[var5	=3]
[var6	=70]
[var7	=0]

[flagText		=1]
[flagKo1		=1]	
[flagKo2		=0]
[flagKo3		=1]
[flagPage		=1]
[flagCmd		=1]
[flagDesign		=1]
[flagDynDesign	=1]

[captionText	=Diagrammintervall]
###[/DEF]###


###[PROPERTIES]###
[columns=50,50]
[row]
	[var1 = root,2,'Diagramm',130]

[row=Darstellung]
	[var6 = select,2,'Opazität der Beschriftungen','100#100%|90#90%|80#80%|70#70%|60#60%|50#50%|40#40%|30#30%|20#20%|10#10%|0#unsichtbar (volle Visuelementgröße)']

[row]
	[var3 = select,2,'Beschriftung der Titelzeile','0#ohne|1#Hauptintervall|2#Diagrammtitel|3#Hauptintervall und Diagrammtitel']

[row]
	[var4 = select,1,'Beschriftung der X-Achse','0#ohne|1#Einheit|2#Ticks|4#Trennlinien|3#Einheit und Ticks|5#Einheit und Trennlinien|6#Ticks und Trennlinien|7#Einheit, Ticks und Trennlinien']
	[var5 = select,1,'Ticks auf der X-Achse','0#ohne|1#Ticks|2#Trennlinien|3#Ticks und Trennlinien']

[row=Aktualisierung]
	[var7 = check,2,'','Aktualisierung per KO']

[row]
	[var2 = select,2,'Aktualisierung per Intervall','0#deaktiviert|1#jede Sekunde|2#alle 2 Sekunden|3#alle 3 Sekunden|4#alle 4 Sekunden|5#alle 5 Sekunden|10#alle 10 Sekunden|15#alle 15 Sekunden|20#alle 20 Sekunden|30#alle 30 Sekunden|60#jede Minute|120#alle 2 Minuten|180#alle 3 Minuten|300#alle 5 Minuten|600#alle 10 Minuten|900#alle 15 Minuten|1800#alle 30 Minuten|3600#jede Stunde']
###[/PROPERTIES]###


###[EDITOR.PHP]###
<?
$tmp=sql_getValues('edomiProject.editChart','name,titel','id='.$item['var1']);
if ($tmp!==false) {
	$property[0]=$tmp['name']; 	
	$property[1]=$tmp['titel'];
} else {
	$property[0]='';
	$property[1]='';
}
?>
###[/EDITOR.PHP]###


###[EDITOR.JS]###
VSE_VSEID=function(elementId,obj,meta,property,isPreview,koValue) {
	var n="<table cellpadding='0' cellspacing='0' border='0' style='left:0; top:0; width:100%; height:100%;'>";
		if (obj.dataset.var3&2) {n+="<tr height='1%'><td style='padding-top:5px; padding-bottom:5px; border-bottom:1px dotted;'>"+property[1]+"</td></tr>";}
		n+="<tr><td>"+((isPreview)?"":"<span class='app2_pseudoElement'>DIAGRAMM</span><br>")+"<span style='font-size:10px; text-shadow:none; text-decoration:none; font-weight:normal; font-style:normal;'>"+property[0]+"</span></td></tr>";
	n+="</table>";

	//Text immer zentrieren, kein Padding
	obj.style.textAlign="center";
	obj.style.padding="0";

	obj.innerHTML=n;
	return property[0];
}

###[/EDITOR.JS]###


###[VISU.PHP]###
<?
function PHP_VSE_VSEID($cmd,$json1,$json2) {
	require(MAIN_PATH."/main/include/php/incl_ga.php");		//nur für parseGAValues()

	if ($cmd=='chartDraw') {
		$cChart=new PHP_VSE_VSEID_classChart();

		$cChart->objId=$json1['elementId'];
		$cChart->chartId=$json1['chartId'];
		$cChart->chartWidth=$json1['width'];
		$cChart->chartHeight=$json1['height'];
		$cChart->chartFontsize=$json1['fontSize'];
		$cChart->chartDateFromTo=$json1['dateRange'];
		$cChart->koValue=$json1['koValue'];
		$cChart->chartParaTitle=$json1['titleMode'];
		$cChart->chartParaXaxis=$json1['xAxisMode'];
		$cChart->chartParaXticks=$json1['xTicksMode'];
		$cChart->chartCaptionOpacity=$json1['captionOpacity'];

		$cChart->drawChart();
		$cChart=null;

		//Refresh-Timer setzen
?>
		VSE_VSEID_callbackSetTimer("<?echo $json1['elementId'];?>");
<?
	}
}

class PHP_VSE_VSEID_classChart {

	public $objId=null;
	public $chartId=0;
	public $chartWidth=0;
	public $chartHeight=0;
	public $chartFontsize=0;
	public $chartDateFromTo=null;
	public $koValue=null;
	public $chartParaTitle=0;
	public $chartParaXaxis=0;
	public $chartParaXticks=0;
	public $chartCaptionOpacity=100;
	
	private $chartMeta=0;
	private $chartInt1=null;
	private $chartInt2=null;
	private $chartMarginX=0;
	private $chartMarginY=0;
	private $visibleYaxis=0;


	public function drawChart() {		
		//Parameter
		$ss1=sql_call("SELECT * FROM edomiLive.chart WHERE (id=".$this->chartId.")");
		if ($this->chartMeta=sql_result($ss1)) {

			$chartDateFrom=parseGAValues($this->chartMeta['datefrom']);
			$chartDateTo=parseGAValues($this->chartMeta['dateto']);
			
			//Rand (links und rechts) zur Zeichenfläche hin
			$this->chartMarginX=intVal(1*$this->chartFontsize);

			//Rand (oben und unten) zur Zeichenfläche hin
			if ($this->chartParaTitle==0 && $this->chartParaXaxis==0) {
				$this->chartMarginY=intVal($this->chartFontsize/2); 	//Sonderfall: Titelzeile und X-Achsenbeschriftung deaktiviert
			} else {
				$this->chartMarginY=intVal(3*$this->chartFontsize); 	//Normalfall
			}

			//Sonderfall: volle Diagrammfläche nutzen
			if ($this->chartCaptionOpacity==0) {
				$this->chartMarginX=0;
				$this->chartMarginY=0;
			}
	
			//Intervall aus Design (bzw. dynamischen Design) holen
			$tmp=explode('**',$this->chartDateFromTo);
			if (count($tmp)==2) {
				$tmpFrom=trim(parseGAValues($tmp[0]));
				$tmpTo=trim(parseGAValues($tmp[1]));
				if (!isEmpty($tmpFrom)) {$chartDateFrom=$tmpFrom;}
				if (!isEmpty($tmpTo)) {$chartDateTo=$tmpTo;}
			}
			
?>
			var cWidth=<?echo $this->chartWidth;?>;
			var cHeight=<?echo $this->chartHeight;?>;
			var obj=document.getElementById("e-<?echo $this->objId;?>");
			var canvas=document.getElementById("e-<?echo $this->objId;?>-canvas");
			var c=canvasScale(canvas,cWidth,cHeight);
			if (c!==false) {
				c.font=obj.style.fontSize+" "+obj.style.fontFamily;
				var fontArgs=c.font.split(' ',1);
				var fontNewSize=obj.style.fontSize;
				c.font=fontNewSize+' '+fontArgs[fontArgs.length-1];
				var fontArgs=c.font.split(' ',1);
				var textHeight=parseInt(fontArgs[0].replace("px",""));
				var yAxisMaxWidth=0;
				c.clearRect(0,0,cWidth,cHeight);
<?
	
				//Zeitbereich berechnen:
				//KO-Archive durchgehen und jeweils Min/Max-Datetime heraussuchen
				$chartCount=0;
				$ss1=sql_call("SELECT archivkoid FROM edomiLive.chartList WHERE (targetid=".$this->chartId.")");
				while ($n=sql_result($ss1)) {
					$ss2=sql_call("SELECT MIN(datetime) AS anz1,MAX(datetime) AS anz2,targetid FROM edomiLive.archivKoData WHERE (targetid=".$n['archivkoid'].")");
					if ($nn=sql_result($ss2)) {
						if ($nn['targetid']>0) { //Existieren überhaupt Daten?
							if (isEmpty($this->chartInt1) || strtotime($nn['anz1'])<strtotime($this->chartInt1)) {$this->chartInt1=$nn['anz1'];}
							if (isEmpty($this->chartInt2) || strtotime($nn['anz2'])>strtotime($this->chartInt2)) {$this->chartInt2=$nn['anz2'];}
							$chartCount++;
						}
					}
				}
				sql_close($ss1);
	
				if (!isEmpty($chartDateFrom)) {$this->chartInt1=date("Y-m-d H:i:s",strtotime($this->parseDate($chartDateFrom)));}
				if (!isEmpty($chartDateTo)) {$this->chartInt2=date("Y-m-d H:i:s",strtotime($this->parseDate($chartDateTo)));}
	
				if (strtotime($this->chartInt2)<strtotime($this->chartInt1)) {
					$tmp=$this->chartInt1;
					$this->chartInt1=$this->chartInt2;
					$this->chartInt2=$tmp;
				}


				//ggf. globales Min/Max über alle gewählten Graphen ermitteln
				if (isEmpty($this->chartMeta['ymin']) || isEmpty($this->chartMeta['ymax'])) {
					$yMin=null;
					$yMax=null;
					$ss1=sql_call("SELECT archivkoid FROM edomiLive.chartList WHERE (targetid=".$this->chartId." AND yscale=1)");
					while ($n=sql_result($ss1)) {
						$ss2=sql_call("SELECT MIN(CAST(gavalue AS DECIMAL(20,4))) AS anz1,MAX(CAST(gavalue AS DECIMAL(20,4))) AS anz2 FROM edomiLive.archivKoData WHERE (targetid=".$n['archivkoid']." AND datetime>='".$this->chartInt1."' AND datetime<='".$this->chartInt2."')");
						if ($nn=sql_result($ss2)) {
							if (is_numeric($nn['anz1']) && (isEmpty($yMin) || $nn['anz1']<$yMin)) {$yMin=$nn['anz1'];}
							if (is_numeric($nn['anz2']) && (isEmpty($yMax) || $nn['anz2']>$yMax)) {$yMax=$nn['anz2'];}
						}
						sql_close($ss2);
					}
					sql_close($ss1);
					if (isEmpty($this->chartMeta['ymin'])) {$this->chartMeta['ymin']=$yMin;}
					if (isEmpty($this->chartMeta['ymax'])) {$this->chartMeta['ymax']=$yMax;}
				}

	
				//Zeichnen:
				$diagDataAvailable=false;
				if ($chartCount>0) {
					$diagDataAvailable=$this->drawYaxis($chartCount);
					$this->drawXaxis($chartCount);
					$this->drawTitel($chartCount);
					$this->drawCharts($chartCount);
				}
	
				//Hinweis wenn keine Daten vorhanden sind:
				if (!$diagDataAvailable) {
?>
					c.clearRect(0,0,cWidth,cHeight);
					c.fillStyle=visuElement_getFgColor(obj,0);	//Hinweis: var(--fgc0) funktioniert bei Canvas nicht
					c.textAlign="center";
					c.textBaseline="middle";
					c.fillText("keine Daten vorhanden",cWidth/2,cHeight/2);
<?
				}
?>
			}
<?
		}
		sql_close($ss1);
	}

	private function drawYaxis($chartCount) {
?>
		var axis=new Array();
<?
		$chartNumber=0;
		$ss1=sql_call("SELECT * FROM edomiLive.chartList WHERE (targetid=".$this->chartId.") ORDER BY sort DESC,id DESC");
		while ($chart=sql_result($ss1)) {
	
			//Farbe holen
			$chart['s1']=sql_getValue('edomiLive.visuFGcol','color',"id='".$chart['s1']."'");
	
			//Min/Max/Anzahl der Werte
			$ss2=sql_call("SELECT COUNT(datetime) AS anz0,MIN(CAST(gavalue AS DECIMAL(20,4))) AS anz1,MAX(CAST(gavalue AS DECIMAL(20,4))) AS anz2 FROM edomiLive.archivKoData WHERE (targetid=".$chart['archivkoid']." AND datetime>='".$this->chartInt1."' AND datetime<='".$this->chartInt2."')");
			if ($nn=sql_result($ss2)) {
				$yAnz=$nn['anz0'];
				$yMin=$nn['anz1'];
				$yMax=$nn['anz2'];
			}
			sql_close($ss2);

			//ggf. globale Y-Einstellungen verwenden
			if ($chart['yscale']==1 && !isEmpty($this->chartMeta['ymin']) && !isEmpty($this->chartMeta['ymax'])) {
				$chart['ymin']=$this->chartMeta['ymin'];
				$chart['ymax']=$this->chartMeta['ymax'];
				$chart['yticks']=$this->chartMeta['yticks'];
				$chart['ynice']=$this->chartMeta['ynice'];
			}

			//wenn ymin und/oder ymax gesetzt sind: die db-Werte nehmen (also ymin/ymax)
			if (!isEmpty($chart['ymin'])) {$yMin=$chart['ymin'];}
			if (!isEmpty($chart['ymax'])) {$yMax=$chart['ymax'];}
	
			if ($chart['yshow']==1 || $yAnz>0) { //gibt es überhaupt Daten für dieses Diagramm (oder erzwingt yshow=1 die Anzeige der Achse)?
				if (isEmpty($chart['yticks']) || $chart['yticks']==0) {
					$chart['yticks']=floor(($this->chartHeight-(2*$this->chartMarginY))/($this->chartFontsize*2));
				}
				$tmp=$this->getInterval($yMin,$yMax,$chart['yticks'],$chart['ynice']);
				if ($yAnz==0) {$tmp=array(0,1,1,1,1);}	//Fake-Intervall, falls keine Daten vorhanden sind aber yshow=1 ist
				if ($chart['ystyle']<2) {
					$currentValue='';
					if ($chart['yshowvalue']==1) {
						$ss2=sql_call("SELECT CAST(gavalue AS DECIMAL(20,4)) AS vavg FROM edomiLive.archivKoData WHERE (targetid=".$chart['archivkoid'].") ORDER BY datetime DESC,ms DESC LIMIT 0,1");
						if ($nn=sql_result($ss2)) {
							$currentValue=$nn['vavg'];
						}
						sql_close($ss2);
					}
?>				
					axis.push({interval:[<?echo $tmp[0];?>,<?echo $tmp[1];?>,<?echo $tmp[2];?>,<?echo $tmp[3];?>,<?echo $tmp[4];?>],ygrid:["<?echo $chart['ygrid1'];?>","<?echo $chart['ygrid2'];?>","<?echo $chart['ygrid3'];?>"],style:[visuElement_parseString("<?echo escapeString($chart['s1'],1);?>","<?echo escapeString($this->koValue,1);?>"),textHeight,"<?echo escapeString(html_entity_decode($chart['titel'],ENT_COMPAT,'UTF-8'));?>","<?echo escapeString($currentValue,1);?>"]});
<?
					$this->visibleYaxis++;
				}
				if ($yAnz>0) {$chartNumber++;}
			}
		}
		sql_close($ss1);
	
		if ($chartNumber>0) {
			//Y-Achsen und Grids zeichnen
?>				
			yAxisMaxWidth=VSE_VSEID_drawYaxis(<?echo $this->objId;?>,<?echo (($this->chartCaptionOpacity==0)?'false':'true');?>,[<?echo $this->chartMarginX;?>,<?echo $this->chartMarginY;?>,<?echo $this->chartWidth;?>,<?echo $this->chartHeight;?>],axis);
<?
			return true;
		} else {
			//es existieren keine Daten für diesen Zeitraum (über alle Diagramme)
			return false;
		}
	}
	
	private function drawXaxis($chartCount) {
		global $global_weekdays;
	
		if ($this->chartMeta['mode']==0) {
			//-------------------------------- keine Kummulation ------------------------------------------
	
			//NÄHERUNG: Pro Y-Achse wird eine Breite von 30px angenommen (die genaue Breite ist nur JS bekannt, nicht jedoch PHP)
			$maxTicksX=truncFloat(($this->chartWidth-($this->visibleYaxis*30))/($this->chartFontsize*2));

			//Abstand in s zwischen 2 Ticks, die einen (annähernd) perfekten Abstand haben
			$diffTick=truncFloat((strtotime($this->chartInt2)-strtotime($this->chartInt1))/$maxTicksX);

			if ($this->chartMeta['xunit']<0) {
				//X-Achse automatisch skalieren
				$d=$this->getDatevaluesFromSeconds($diffTick);
		
				//die "größte" Einheit finden
				$unitTick=-1;
				for ($t=5;$t>=0;$t--) {
					if ($d[$t]>0) {
						$unitTick=$t;
						break;
					}
				}

				if ($unitTick>=0) {
					do {
						$intervalTick=-1;
	
						if ($unitTick==0) { //Sekunden
							$niceInterval=array(1,5,10,15,30);
							$niceStart=array(0,30);
						}
						if ($unitTick==1) { //Minuten
							$niceInterval=array(1,5,10,15,30);
							$niceStart=array(0,30);
						}
						if ($unitTick==2) { //Stunden
							$niceInterval=array(1,2,4,6,12);
							$niceStart=array(0,12);
						}
						if ($unitTick==3) { //Tage (andere Intervalle sind blöd, weil ein Monat ja 28,29,30,31 Tage haben kann...)
							$niceInterval=array(1);
							$niceStart=array();
						}
						if ($unitTick==4) { //Monate
							$niceInterval=array(1,2,3,6);
							$niceStart=array(1,7);
						}
						if ($unitTick==5) { //Jahre
							$niceInterval=array(1,2,3,4,5,6,7,8,9,10,15,20,30,40,50,100,200,300,400,500,1000,2000,10000);
							$niceStart=array();
						}
		
						//nächst größeres niceInterval finden
						for ($t=0;$t<count($niceInterval);$t++) {
							if ($niceInterval[$t]>=intval($d[$unitTick])) {
								$intervalTick=$niceInterval[$t];
								break;
							}
						}
		
						//wenn nicht vorhanden: nächst größere Einheit nehmen...
						if ($intervalTick<0) {
							$unitTick++;
						}
					} while ($intervalTick<0 && $unitTick<6);
		
					//Zeichnen
					$this->drawXaxisInterval($unitTick,$intervalTick,$diffTick);
				}				

			} else {
				//X-Achse manuell skalieren
				//Zeichnen
				$this->drawXaxisInterval($this->chartMeta['xunit'],$this->chartMeta['xinterval'],$diffTick);
			}


		} else {
			//-------------------------------- Kummulation ------------------------------------------
	
			$kum=$this->getKummulationData();
	
			$xCount=abs($kum[1]-$kum[0])+1;
?>
			var points1=new Array();
			var points2=new Array();
			var xfak=(<?echo ($this->chartWidth-(2*$this->chartMarginX));?>-yAxisMaxWidth)/(<?echo $xCount;?>);
			var xfakc=xfak/2;
<?
			for ($x=0;$x<$xCount;$x++) {
				if ($this->chartMeta['mode']==1) {$legend=$x;}
				if ($this->chartMeta['mode']==2) {$legend=$x;}
				if ($this->chartMeta['mode']==3) {$legend=$x;}
				if ($this->chartMeta['mode']==4) {$legend=substr($global_weekdays[$x],0,2);}
				if ($this->chartMeta['mode']==5) {$legend=$x+1;}
				if ($this->chartMeta['mode']==6) {$legend=$x+1;}
				if ($this->chartMeta['mode']==7) {$legend=$x+1;}
				if ($this->chartMeta['mode']==8) {$legend=$x+intval(date('Y',strtotime($this->chartInt1)));}
				if ($this->chartMeta['mode']==9) {$legend='Gesamter Zeitraum';}
?>
				points1.push({x:((xfak*<?echo $x;?>)+xfakc),c:"<?echo escapeString($legend,1);?>"});
<?
			}
?>
			VSE_VSEID_drawXaxis(<?echo $this->objId;?>,[<?echo $this->chartMarginX;?>,<?echo $this->chartMarginY;?>,<?echo $this->chartWidth;?>,<?echo $this->chartHeight;?>],yAxisMaxWidth,textHeight,points1,points2,"<?echo escapeString($kum[2],1);?>",true);
<?
		}
	}

	private function drawXaxisInterval($unitTick,$intervalTick,$diffTick) {
?>
		var points1=new Array();
		var points2=new Array();
		var xfak=(<?echo ($this->chartWidth-(2*$this->chartMarginX));?>-yAxisMaxWidth) / <?echo abs(strtotime($this->chartInt2)-strtotime($this->chartInt1));?>;
<?

		//Ticks
		//-----------------------------------
		$d0=$this->getNiceStart_Date($unitTick,$intervalTick);
		
		//Faktor bestimmen
		$tmp=$this->getNiceStart_Tick1($d0,$unitTick,$intervalTick,1);
		$tickFaktor=truncFloat($diffTick/(strtotime($tmp)-strtotime($d0)));
		if ($tickFaktor<1) {$tickFaktor=1;}

		while (strtotime($d0)<strtotime($this->chartInt2)) {
			if ($unitTick==0) {$legend=intVal(substr($d0,17,2));}
			if ($unitTick==1) {$legend=intVal(substr($d0,14,2));}
			if ($unitTick==2) {$legend=intVal(substr($d0,11,2));}
			if ($unitTick==3) {$legend=intVal(substr($d0,8,2));}
			if ($unitTick==4) {$legend=intVal(substr($d0,5,2));}
			if ($unitTick==5) {$legend=substr($d0,0,4);}
?>
			points1.push({x:(xfak*<?echo (strtotime($d0)-strtotime($this->chartInt1));?>),c:"<?echo escapeString($legend,1);?>"});
<?
			$d0=$this->getNiceStart_Tick1($d0,$unitTick,$intervalTick,$tickFaktor);
		}	


		//Trennlinien
		//-----------------------------------
		$d0=$this->chartInt1;
		$lastTick=-$diffTick;

		//Startdatum nicen
		if ($unitTick==0) {$d0=date("Y-m-d H:i:s", strtotime(substr($d0,0,17).'00 +1 minute'));}			//um xx:xx:00 Uhr
		if ($unitTick==1) {$d0=date("Y-m-d H:i:s", strtotime(substr($d0,0,14).'00:00 +1 hour'));}			//um xx:00:00 Uhr
		if ($unitTick==2) {$d0=date("Y-m-d H:i:s", strtotime(substr($d0,0,10).' 00:00:00 +1 day'));}		//um 00:00:00 Uhr
		if ($unitTick==3) {$d0=date("Y-m-d H:i:s", strtotime(substr($d0,0,7).'-01 00:00:00 +1 month'));}	//am 1. eines Monats
		if ($unitTick==4) {$d0=date("Y-m-d H:i:s", strtotime(substr($d0,0,4).'-01-01 00:00:00 +1 year'));}	//am 1.1. eines Jahres
		if ($unitTick==5) {																					//am 1.1. alle 5 Jahre (Startjahr wird auf 5 aufgerundet)
			$tmp=sprintf("%04d",truncFloat(ceil((truncFloat(substr($d0,0,4))+1)/5)*5));
			$d0=date("Y-m-d H:i:s", strtotime($tmp.'-01-01 00:00:00'));
		}

		//Faktor bestimmen		
		$tmp=$this->getNiceStart_Tick2($d0,$unitTick,1);
		$tickFaktor=truncFloat($diffTick/(strtotime($tmp)-strtotime($d0)));
		if ($tickFaktor<1) {$tickFaktor=1;}

		while (strtotime($d0)<strtotime($this->chartInt2)) {
			if ($unitTick==0) {$legend2=date("d.m.Y",strtotime($d0)).' &bull; '.date("H:i:s",strtotime($d0));}
			if ($unitTick==1) {$legend2=date("d.m.Y",strtotime($d0)).' &bull; '.date("H:i:s",strtotime($d0));}
			if ($unitTick==2) {$legend2=date("d.m.Y",strtotime($d0)).' &bull; '.date("H:i:s",strtotime($d0));}
			if ($unitTick==3) {$legend2=date("d.m.Y",strtotime($d0));}
			if ($unitTick==4) {$legend2=date("m.Y",strtotime($d0));}
			if ($unitTick==5) {$legend2=date("Y",strtotime($d0));}
?>
			points2.push({x:(xfak*<?echo (strtotime($d0)-strtotime($this->chartInt1));?>),c:"<?echo escapeString(html_entity_decode($legend2,ENT_COMPAT,'UTF-8'),1);?>"});
<?
			$d0=$this->getNiceStart_Tick2($d0,$unitTick,$tickFaktor);
		}


		//Zeichnen
		$unitName=array('Sekunde','Minute','Stunde','Tag','Monat','Jahr');
?>
		VSE_VSEID_drawXaxis(<?echo $this->objId;?>,[<?echo $this->chartMarginX;?>,<?echo $this->chartMarginY;?>,<?echo $this->chartWidth;?>,<?echo $this->chartHeight;?>],yAxisMaxWidth,textHeight,points1,points2,"<?echo escapeString($unitName[$unitTick],1);?>",false);
<?
	}

	private function getNiceStart_Date($unitTick,$intervalTick) {
		$d0=$this->chartInt1;
		if ($unitTick==0) {
			$tmp=sprintf("%02d",truncFloat(substr($d0,17,2)/$intervalTick)*$intervalTick);
			$d0=date("Y-m-d H:i:s", strtotime(substr($d0,0,17).$tmp));
			if (strtotime($d0)<strtotime($this->chartInt1)) {$d0=date("Y-m-d H:i:s",strtotime($d0.' +'.$intervalTick.' seconds'));}
		}
		if ($unitTick==1) {
			$tmp=sprintf("%02d",truncFloat(substr($d0,14,2)/$intervalTick)*$intervalTick);
			$d0=date("Y-m-d H:i:s", strtotime(substr($d0,0,14).$tmp.':00'));
			if (strtotime($d0)<strtotime($this->chartInt1)) {$d0=date("Y-m-d H:i:s",strtotime($d0.' +'.$intervalTick.' minutes'));}
		}
		if ($unitTick==2) {
			$tmp=sprintf("%02d",truncFloat(substr($d0,11,2)/$intervalTick)*$intervalTick);
			$d0=date("Y-m-d H:i:s", strtotime(substr($d0,0,11).$tmp.':00:00'));
			if (strtotime($d0)<strtotime($this->chartInt1)) {$d0=date("Y-m-d H:i:s",strtotime($d0.' +'.$intervalTick.' hours'));}
		}
		if ($unitTick==3) {
			$tmp=sprintf("%02d",truncFloat(substr($d0,8,2)/$intervalTick)*$intervalTick);
			$d0=date("Y-m-d H:i:s", strtotime(substr($d0,0,8).$tmp.' 00:00:00'));
			if (strtotime($d0)<strtotime($this->chartInt1)) {$d0=date("Y-m-d H:i:s",strtotime($d0.' +'.$intervalTick.' days'));}
		}
		if ($unitTick==4) {
			$tmp=sprintf("%02d",truncFloat(substr($d0,5,2)/$intervalTick)*$intervalTick);
			$d0=date("Y-m-d H:i:s", strtotime(substr($d0,0,5).$tmp.'-01 00:00:00'));
			if (strtotime($d0)<strtotime($this->chartInt1)) {$d0=date("Y-m-d H:i:s",strtotime($d0.' +'.$intervalTick.' months'));}
		}
		if ($unitTick==5) {
			$tmp=sprintf("%04d",truncFloat(substr($d0,0,4)/$intervalTick)*$intervalTick);
			$d0=date("Y-m-d H:i:s", strtotime($tmp.'-01-01 00:00:00'));
			if (strtotime($d0)<strtotime($this->chartInt1)) {$d0=date("Y-m-d H:i:s",strtotime($d0.' +'.$intervalTick.' years'));}
		}
		return $d0;
	}

	private function getNiceStart_Tick1($d0,$unitTick,$intervalTick,$tickFaktor) {
		if ($unitTick==0) {$d0=date("Y-m-d H:i:s", strtotime($d0.' +'.($intervalTick*$tickFaktor).' seconds'));}
		if ($unitTick==1) {$d0=date("Y-m-d H:i:s", strtotime($d0.' +'.($intervalTick*$tickFaktor).' minutes'));}
		if ($unitTick==2) {$d0=date("Y-m-d H:i:s", strtotime($d0.' +'.($intervalTick*$tickFaktor).' hours'));}
		if ($unitTick==3) {$d0=date("Y-m-d H:i:s", strtotime($d0.' +'.($intervalTick*$tickFaktor).' days'));}
		if ($unitTick==4) {$d0=date("Y-m-d H:i:s", strtotime($d0.' +'.($intervalTick*$tickFaktor).' months'));}
		if ($unitTick==5) {$d0=date("Y-m-d H:i:s", strtotime($d0.' +'.($intervalTick*$tickFaktor).' years'));}
		return $d0;
	}

	private function getNiceStart_Tick2($d0,$unitTick,$tickFaktor) {
		if ($unitTick==0) {$d0=date("Y-m-d H:i:s", strtotime($d0.' +'.(1*$tickFaktor).' minute'));}
		if ($unitTick==1) {$d0=date("Y-m-d H:i:s", strtotime($d0.' +'.(1*$tickFaktor).' hour'));}
		if ($unitTick==2) {$d0=date("Y-m-d H:i:s", strtotime($d0.' +'.(1*$tickFaktor).' day'));}
		if ($unitTick==3) {$d0=date("Y-m-d H:i:s", strtotime($d0.' +'.(1*$tickFaktor).' month'));}
		if ($unitTick==4) {$d0=date("Y-m-d H:i:s", strtotime($d0.' +'.(1*$tickFaktor).' year'));}
		if ($unitTick==5) {$d0=date("Y-m-d H:i:s", strtotime($d0.' +'.(5*$tickFaktor).' years'));}
		return $d0;
	}
	
	private function drawTitel($chartCount) {
?>				
		VSE_VSEID_drawTitel(<?echo $this->objId;?>,[<?echo $this->chartMarginX;?>,<?echo $this->chartMarginY;?>,<?echo $this->chartWidth;?>,<?echo $this->chartHeight;?>],yAxisMaxWidth,"<?echo escapeString(html_entity_decode($this->chartMeta['titel'],ENT_COMPAT,'UTF-8'));?>","<?echo escapeString(date("d.m.Y / H:i:s", strtotime($this->chartInt1)),1);?>","<?echo escapeString(date("d.m.Y / H:i:s", strtotime($this->chartInt2)),1);?>");
<?
	}
	
	private function drawCharts($chartCount) {		
		//Charts zeichnen
		$ss1=sql_call("SELECT * FROM edomiLive.chartList WHERE (targetid=".$this->chartId.") ORDER BY sort DESC,id DESC");
		while ($chart=sql_result($ss1)) {
			$clip=false;

			//Farbe holen
			$chart['s1']=sql_getValue('edomiLive.visuFGcol','color',"id='".$chart['s1']."'");
			$chart['ss1']=sql_getValue('edomiLive.visuFGcol','color',"id='".$chart['ss1']."'");
	
			//Min/Max/Anzahl der Werte
			$ss2=sql_call("SELECT COUNT(datetime) AS anz0,MIN(CAST(gavalue AS DECIMAL(20,4))) AS anz1,MAX(CAST(gavalue AS DECIMAL(20,4))) AS anz2 FROM edomiLive.archivKoData WHERE (targetid=".$chart['archivkoid']." AND datetime>='".$this->chartInt1."' AND datetime<='".$this->chartInt2."')");
			if ($nn=sql_result($ss2)) {
				$yAnz=$nn['anz0'];
				$yMin=$nn['anz1'];
				$yMax=$nn['anz2'];
			}
			sql_close($ss2);

			//ggf. globale Y-Einstellungen verwenden
			if ($chart['yscale']==1 && !isEmpty($this->chartMeta['ymin']) && !isEmpty($this->chartMeta['ymax'])) {
				$chart['ymin']=$this->chartMeta['ymin'];
				$chart['ymax']=$this->chartMeta['ymax'];
				$chart['yticks']=$this->chartMeta['yticks'];
				$chart['ynice']=$this->chartMeta['ynice'];
			}
	
			//wenn ymin und/oder ymax gesetzt sind: die db-Werte nehmen (also ymin/ymax)
			if (!isEmpty($chart['ymin'])) {$yMin=$chart['ymin'];}
			if (!isEmpty($chart['ymax'])) {$yMax=$chart['ymax'];}
	
			if ($yAnz>0) { //gibt es überhaupt Daten für dieses Diagramm (in diesem Zeitraum)?
	
				if (isEmpty($chart['yticks']) || $chart['yticks']==0) {
					$chart['yticks']=floor(($this->chartHeight-(2*$this->chartMarginY))/($this->chartFontsize*2));
				}
	
				$tmp=$this->getInterval($yMin,$yMax,$chart['yticks'],$chart['ynice']);
	
				$yMin=$tmp[0];
				$yMax=$tmp[1];
				$range=$tmp[2];
				$tickCount=$tmp[3];
				$tickSpacing=$tmp[4];
				$yfak=($this->chartHeight-(2*$this->chartMarginY))/$range;
	
				if ($this->chartMeta['mode']==0) {
	
					//-------------------------------- keine Kummulation ------------------------------------------

					$tmp1=($this->chartWidth-(2*$this->chartMarginX));
					$tmp2=abs(strtotime($this->chartInt2)-strtotime($this->chartInt1));
					if ($tmp2==0) {
?>
						var xfak=1;
<?
					} else {
?>
						var xfak=(<?echo $tmp1;?>-yAxisMaxWidth) / <?echo $tmp2;?>;
<?
					}
?>
					var points=new Array();
<?
					if (!isEmpty($chart['xinterval'])) {

						if ($chart['xinterval']==0) {
							//X-Intervall ggf. automatisch vergrößern (Downsampling)
							if (($yAnz/$this->chartWidth)>1) {	//Näherung, da exakte Chartbreite ja nicht bekannt ist (für PHP)
								$chart['xinterval']=round(abs(strtotime($this->chartInt2)-strtotime($this->chartInt1))/$this->chartWidth);
							} else {
								$chart['xinterval']=1;
							}
						}

						if ($chart['xinterval']<1) {$chart['xinterval']=1;}

						//Mittelwertinterval (automatisch oder Nutzerangabe)
						$y=false;
						$ss2=sql_call("SELECT UNIX_TIMESTAMP(datetime) AS anz1,AVG(CAST(gavalue AS DECIMAL(20,4))) AS vavg,MIN(CAST(gavalue AS DECIMAL(20,4))) AS vmin,MAX(CAST(gavalue AS DECIMAL(20,4))) AS vmax FROM edomiLive.archivKoData WHERE (targetid=".$chart['archivkoid']." AND datetime>='".$this->chartInt1."' AND datetime<='".$this->chartInt2."') GROUP BY FLOOR((UNIX_TIMESTAMP(datetime)-".strtotime($this->chartInt1).")/".$chart['xinterval'].") ORDER BY datetime ASC,ms ASC");
						while ($nn=sql_result($ss2)) {
							$y=($this->chartHeight-$this->chartMarginY)-($yfak*(floatval($nn['vavg'])-$yMin));
							$y1=($this->chartHeight-$this->chartMarginY)-($yfak*(floatval($nn['vmin'])-$yMin));
							$y2=($this->chartHeight-$this->chartMarginY)-($yfak*(floatval($nn['vmax'])-$yMin));
	
							//X auf das Intervall einrasten
							$x=truncFloat(($nn["anz1"]-strtotime($this->chartInt1))/$chart['xinterval'])*$chart['xinterval'];
	
							//offsetX: Mitte des X-Intervalls (relativ)							
							$offsetX=$chart['xinterval']/2;
							if (($nn["anz1"]-strtotime($this->chartInt1)+$offsetX)>(strtotime($this->chartInt2)-strtotime($this->chartInt1))) {$offsetX=0;}							


							if ($chart['yminmax']>=1 && ($y!=$y1 || $y!=$y2)) {
								if ($chart['yminmax']==1) {
?>
									points.push({x:(<?echo $this->chartMarginX;?>+(xfak*<?echo $x;?>)),y:<?echo $y;?>,y1:<?echo $y1;?>,y2:<?echo $y2;?>,w:(xfak*<?echo $offsetX;?>)});
<?
								} else if ($chart['yminmax']==2) {
?>
									points.push({x:(<?echo $this->chartMarginX;?>+(xfak*<?echo $x;?>)),y:<?echo $y1;?>,y1:null,y2:null,w:(xfak*<?echo $offsetX;?>)});
									points.push({x:(<?echo $this->chartMarginX;?>+(xfak*<?echo $x;?>)),y:<?echo $y2;?>,y1:null,y2:null,w:(xfak*<?echo $offsetX;?>)});
<?
								} else if ($chart['yminmax']==3) {
?>
									points.push({x:(<?echo $this->chartMarginX;?>+(xfak*<?echo $x;?>)),y:<?echo $y2;?>,y1:null,y2:null,w:(xfak*<?echo $offsetX;?>)});
									points.push({x:(<?echo $this->chartMarginX;?>+(xfak*<?echo $x;?>)),y:<?echo $y1;?>,y1:null,y2:null,w:(xfak*<?echo $offsetX;?>)});
<?
								}
								
							} else {
?>
								points.push({x:(<?echo $this->chartMarginX;?>+(xfak*<?echo $x;?>)),y:<?echo $y;?>,y1:null,y2:null,w:(xfak*<?echo $offsetX;?>)});
<?
							}
						}
						sql_close($ss2);
						
					} else {
					
						//kein Mittelwertinterval
						$y=false;
						$ss2=sql_call("SELECT UNIX_TIMESTAMP(datetime) AS anz1,CAST(gavalue AS DECIMAL(20,4)) AS vcur FROM edomiLive.archivKoData WHERE (targetid=".$chart['archivkoid']." AND datetime>='".$this->chartInt1."' AND datetime<='".$this->chartInt2."') ORDER BY datetime ASC,ms ASC");
						while ($nn=sql_result($ss2)) {
							$y=($this->chartHeight-$this->chartMarginY)-($yfak*(floatval($nn['vcur'])-$yMin));
	
							//X auf Sekunde einrasten
							$x=truncFloat($nn["anz1"]-strtotime($this->chartInt1));
	
							//offsetX: Mitte des X-Intervalls (relativ)							
							$offsetX=1/2;
							if (($nn["anz1"]-strtotime($this->chartInt1)+$offsetX)>(strtotime($this->chartInt2)-strtotime($this->chartInt1))) {$offsetX=0;}							
?>
							points.push({x:(<?echo $this->chartMarginX;?>+(xfak*<?echo $x;?>)),y:<?echo $y;?>,y1:null,y2:null,w:(xfak*<?echo $offsetX;?>)});
<?
						}
						sql_close($ss2);
					}

	
					if ($y!==false) {
	
						//Startpunkt verlängern
						//-------------------------------------------
						if ($chart['extend1']==0) {			//ersten Punkt wiederholen (=eliminieren)
?>
							points.unshift({x:points[0].x,y:points[0].y,y1:null,y2:null,w:points[0].w});
<?
						} else if ($chart['extend1']==1) {	//ersten Punkt bis zum Rand verlängern
?>
							points.unshift({x:<?echo $this->chartMarginX;?>,y:points[0].y,y1:null,y2:null,w:0});
<?
						} else if ($chart['extend1']==2) {	//Vorgänger
							$ss2=sql_call("SELECT UNIX_TIMESTAMP(datetime) AS anz1,CAST(gavalue AS DECIMAL(20,4)) AS vavg FROM edomiLive.archivKoData WHERE (targetid=".$chart['archivkoid']." AND datetime<'".$this->chartInt1."') ORDER BY datetime DESC,ms DESC LIMIT 0,1");
							if ($n=sql_result($ss2)) {
								$clip=true;
?>
								points.unshift({x:<?echo intVal($n["anz1"]-strtotime($this->chartInt1));?>,y:<?echo ($this->chartHeight-$this->chartMarginY)-($yfak*(floatval($n['vavg'])-$yMin));?>,y1:null,y2:null,w:0});
<?
							} else {
?>
								points.unshift({x:<?echo $this->chartMarginX;?>,y:points[0].y,y1:null,y2:null,w:0});
<?
							}
							sql_close($ss2);
						}
	
						//Endpunkt verlängern
						//-------------------------------------------
						if ($chart['extend2']==0) {			//letzen Punkt wiederholen (=eliminieren)
?>
							points.push({x:points[points.length-1].x,y:points[points.length-1].y,y1:null,y2:null,w:points[points.length-1].w});
<?
						} else if ($chart['extend2']==1) {	//letzen Punkt bis zum Rand verlängern
?>
							points.push({x:(<?echo ($this->chartWidth-(1*$this->chartMarginX));?>-yAxisMaxWidth),y:points[points.length-1].y,y1:null,y2:null,w:0});
<?
						} else if ($chart['extend2']==2) {	//Nachfolger
							$ss2=sql_call("SELECT UNIX_TIMESTAMP(datetime) AS anz1,CAST(gavalue AS DECIMAL(20,4)) AS vavg FROM edomiLive.archivKoData WHERE (targetid=".$chart['archivkoid']." AND datetime>'".$this->chartInt2."') ORDER BY datetime ASC,ms ASC LIMIT 0,1");
							if ($n=sql_result($ss2)) {
								$clip=true;
?>
								points.push({x:(<?echo $this->chartMarginX;?>+(xfak*<?echo intVal($n["anz1"]-strtotime($this->chartInt1));?>)),y:<?echo ($this->chartHeight-$this->chartMarginY)-($yfak*(floatval($n['vavg'])-$yMin));?>,y1:null,y2:null,w:0});
<?
							} else {
?>
								points.push({x:(<?echo ($this->chartWidth-(1*$this->chartMarginX));?>-yAxisMaxWidth),y:points[points.length-1].y,y1:null,y2:null,w:0});
<?
							}
							sql_close($ss2);
						}
?>
						VSE_VSEID_drawChart(<?echo (($clip)?"true":"false");?>,[<?echo $this->chartMarginX;?>,<?echo $this->chartMarginY;?>,<?echo $this->chartWidth;?>,<?echo $this->chartHeight;?>,yAxisMaxWidth],<?echo $this->objId;?>,<?echo $chart['charttyp'];?>,false,"<?echo $chart['yminmax'];?>",<?echo intval($this->chartHeight-$this->chartMarginY);?>,<?echo $this->chartMarginY;?>,[visuElement_parseString("<?echo escapeString($chart['s1'],1);?>","<?echo escapeString($this->koValue,1);?>"),"<?echo ($chart['s2']/100);?>","<?echo $chart['s3'];?>","<?echo $chart['s4'];?>"],points);
<?			
						if ($chart['charttyp2']>0) {
?>
							VSE_VSEID_drawChart(<?echo (($clip)?"true":"false");?>,[<?echo $this->chartMarginX;?>,<?echo $this->chartMarginY;?>,<?echo $this->chartWidth;?>,<?echo $this->chartHeight;?>,yAxisMaxWidth],<?echo $this->objId;?>,<?echo $chart['charttyp2'];?>,false,0,<?echo intval($this->chartHeight-$this->chartMarginY);?>,<?echo $this->chartMarginY;?>,[visuElement_parseString("<?echo escapeString($chart['ss1'],1);?>","<?echo escapeString($this->koValue,1);?>"),"<?echo ($chart['ss2']/100);?>","<?echo $chart['ss3'];?>","<?echo $chart['ss4'];?>"],points);
<?
						}
					}						
	
				} else {
	
					//-------------------------------- Kummulation ------------------------------------------
	
					$kum=$this->getKummulationData();
?>
					var points=new Array();
					var xfak=(<?echo ($this->chartWidth-(2*$this->chartMarginX));?>-yAxisMaxWidth)/(<?echo (abs($kum[1]-$kum[0])+1);?>);
					var xfakc=xfak/2;
<?
					$y=false;
					$c=0;
	
					if ($this->chartMeta['mode']==1) {$ss2=sql_call("SELECT SECOND(datetime) AS xpos,AVG(CAST(gavalue AS DECIMAL(20,4))) AS vavg,MIN(CAST(gavalue AS DECIMAL(20,4))) AS vmin,MAX(CAST(gavalue AS DECIMAL(20,4))) AS vmax FROM edomiLive.archivKoData WHERE (targetid=".$chart['archivkoid']." AND datetime>='".$this->chartInt1."' AND datetime<='".$this->chartInt2."') GROUP BY SECOND(datetime) ORDER BY SECOND(datetime) ASC");} //Kummulation: Sekunden
					if ($this->chartMeta['mode']==2) {$ss2=sql_call("SELECT MINUTE(datetime) AS xpos,AVG(CAST(gavalue AS DECIMAL(20,4))) AS vavg,MIN(CAST(gavalue AS DECIMAL(20,4))) AS vmin,MAX(CAST(gavalue AS DECIMAL(20,4))) AS vmax FROM edomiLive.archivKoData WHERE (targetid=".$chart['archivkoid']." AND datetime>='".$this->chartInt1."' AND datetime<='".$this->chartInt2."') GROUP BY MINUTE(datetime) ORDER BY MINUTE(datetime) ASC");} //Kummulation: Minuten
					if ($this->chartMeta['mode']==3) {$ss2=sql_call("SELECT HOUR(datetime) AS xpos,AVG(CAST(gavalue AS DECIMAL(20,4))) AS vavg,MIN(CAST(gavalue AS DECIMAL(20,4))) AS vmin,MAX(CAST(gavalue AS DECIMAL(20,4))) AS vmax FROM edomiLive.archivKoData WHERE (targetid=".$chart['archivkoid']." AND datetime>='".$this->chartInt1."' AND datetime<='".$this->chartInt2."') GROUP BY HOUR(datetime) ORDER BY HOUR(datetime) ASC");} //Kummulation: Stunden
					if ($this->chartMeta['mode']==4) {$ss2=sql_call("SELECT WEEKDAY(datetime) AS xpos,AVG(CAST(gavalue AS DECIMAL(20,4))) AS vavg,MIN(CAST(gavalue AS DECIMAL(20,4))) AS vmin,MAX(CAST(gavalue AS DECIMAL(20,4))) AS vmax FROM edomiLive.archivKoData WHERE (targetid=".$chart['archivkoid']." AND datetime>='".$this->chartInt1."' AND datetime<='".$this->chartInt2."') GROUP BY WEEKDAY(datetime) ORDER BY WEEKDAY(datetime) ASC");} //Kummulation: Wochentage
					if ($this->chartMeta['mode']==5) {$ss2=sql_call("SELECT (DAY(datetime)-1) AS xpos,AVG(CAST(gavalue AS DECIMAL(20,4))) AS vavg,MIN(CAST(gavalue AS DECIMAL(20,4))) AS vmin,MAX(CAST(gavalue AS DECIMAL(20,4))) AS vmax FROM edomiLive.archivKoData WHERE (targetid=".$chart['archivkoid']." AND datetime>='".$this->chartInt1."' AND datetime<='".$this->chartInt2."') GROUP BY (DAY(datetime)-1) ORDER BY (DAY(datetime)-1) ASC");} //Kummulation: Tag
					if ($this->chartMeta['mode']==6) {$ss2=sql_call("SELECT WEEK(datetime,5) AS xpos,AVG(CAST(gavalue AS DECIMAL(20,4))) AS vavg,MIN(CAST(gavalue AS DECIMAL(20,4))) AS vmin,MAX(CAST(gavalue AS DECIMAL(20,4))) AS vmax FROM edomiLive.archivKoData WHERE (targetid=".$chart['archivkoid']." AND datetime>='".$this->chartInt1."' AND datetime<='".$this->chartInt2."') GROUP BY WEEK(datetime,5) ORDER BY WEEK(datetime,5) ASC");} //Kummulation: Tag
					if ($this->chartMeta['mode']==7) {$ss2=sql_call("SELECT (MONTH(datetime)-1) AS xpos,AVG(CAST(gavalue AS DECIMAL(20,4))) AS vavg,MIN(CAST(gavalue AS DECIMAL(20,4))) AS vmin,MAX(CAST(gavalue AS DECIMAL(20,4))) AS vmax FROM edomiLive.archivKoData WHERE (targetid=".$chart['archivkoid']." AND datetime>='".$this->chartInt1."' AND datetime<='".$this->chartInt2."') GROUP BY (MONTH(datetime)-1) ORDER BY (MONTH(datetime)-1) ASC");} //Kummulation: Monat
					if ($this->chartMeta['mode']==8) {$ss2=sql_call("SELECT (YEAR(datetime)-".$kum[0].") AS xpos,AVG(CAST(gavalue AS DECIMAL(20,4))) AS vavg,MIN(CAST(gavalue AS DECIMAL(20,4))) AS vmin,MAX(CAST(gavalue AS DECIMAL(20,4))) AS vmax FROM edomiLive.archivKoData WHERE (targetid=".$chart['archivkoid']." AND datetime>='".$this->chartInt1."' AND datetime<='".$this->chartInt2."') GROUP BY (YEAR(datetime)-".$kum[0].") ORDER BY (YEAR(datetime)-".$kum[0].") ASC");} //Kummulation: Jahr
					if ($this->chartMeta['mode']==9) {$ss2=sql_call("SELECT 0 AS xpos,AVG(CAST(gavalue AS DECIMAL(20,4))) AS vavg,MIN(CAST(gavalue AS DECIMAL(20,4))) AS vmin,MAX(CAST(gavalue AS DECIMAL(20,4))) AS vmax FROM edomiLive.archivKoData WHERE (targetid=".$chart['archivkoid']." AND datetime>='".$this->chartInt1."' AND datetime<='".$this->chartInt2."')");} //Kummulation: ALLES
					while ($nn=sql_result($ss2)) {
						$c++;
						$y=($this->chartHeight-$this->chartMarginY)-($yfak*(floatval($nn['vavg'])-$yMin));
						$y1=($this->chartHeight-$this->chartMarginY)-($yfak*(floatval($nn['vmin'])-$yMin));
						$y2=($this->chartHeight-$this->chartMarginY)-($yfak*(floatval($nn['vmax'])-$yMin));
						if ($chart['yminmax']==1 && ($y!=$y1 || $y!=$y2)) {
?>
							points.push({x:(<?echo $this->chartMarginX;?>+(xfak*<?echo $nn['xpos'];?>))+xfakc,y:<?echo $y;?>,y1:<?echo $y1;?>,y2:<?echo $y2;?>,w:0,w2:xfak});
<?
						} else {
?>
							points.push({x:(<?echo $this->chartMarginX;?>+(xfak*<?echo $nn['xpos'];?>))+xfakc,y:<?echo $y;?>,y1:null,y2:null,w:0,w2:xfak});
<?
						}
					}
					sql_close($ss2);
					
					if ($y!==false) {
						if ($c==1) {
?>
							points.unshift({x:<?echo $this->chartMarginX;?>,y:points[0].y,y1:null,y2:null,w:0});
							points.push({x:(<?echo ($this->chartWidth-(1*$this->chartMarginX));?>-yAxisMaxWidth),y:<?echo $y;?>,y1:null,y2:null,w:0});
<?
						} else {
?>
							points.unshift({x:points[0].x,y:points[0].y,y1:null,y2:null,w:0});
							points.push({x:points[points.length-1].x,y:points[points.length-1].y,y1:null,y2:null,w:0});
<?
						}
?>
						VSE_VSEID_drawChart(false,[],<?echo $this->objId;?>,<?echo $chart['charttyp'];?>,true,"<?echo $chart['yminmax'];?>",<?echo intval($this->chartHeight-$this->chartMarginY);?>,<?echo $this->chartMarginY;?>,[visuElement_parseString("<?echo escapeString($chart['s1'],1);?>","<?echo escapeString($this->koValue,1);?>"),"<?echo ($chart['s2']/100);?>","<?echo $chart['s3'];?>","<?echo $chart['s4'];?>"],points);
<?
						if ($chart['charttyp2']>0) {
?>
							VSE_VSEID_drawChart(false,[],<?echo $this->objId;?>,<?echo $chart['charttyp2'];?>,true,0,<?echo intval($this->chartHeight-$this->chartMarginY);?>,<?echo $this->chartMarginY;?>,[visuElement_parseString("<?echo escapeString($chart['ss1'],1);?>","<?echo escapeString($this->koValue,1);?>"),"<?echo ($chart['ss2']/100);?>","<?echo $chart['ss3'];?>","<?echo $chart['ss4'];?>"],points);
<?
						}
					}
	
				}
	
			}
?>
			c.restore();
<?
		}
		sql_close($ss1);
	}
	
	private function getDatevaluesFromSeconds($s) {
		//liefert ein Array zurück, dass die Sekunden $s in Sekunden/Minuten/Stunden/Tage/Monate/Jahre umrechnet
		$d1 = new DateTime('@0');
		$d2 = $d1->diff(new DateTime('@'.$s));
		$n[0]=$d2->s;
		$n[1]=$d2->i;
		$n[2]=$d2->h;
		$n[3]=$d2->d;
		$n[4]=$d2->m;
		$n[5]=$d2->y;
		return $n;
	}
	
	private function getDatevaluesFromDate($d) {
		//liefert ein Array zurück, dass die einzelnen Teile eines DATETIME-Strings enthält (Sekunden/Minuten/Stunden/Tage/Monate/Jahre)
		$d1=strtotime($d);
		$n[0]=date('s',$d1);
		$n[1]=date('i',$d1);
		$n[2]=date('H',$d1);
		$n[3]=date('d',$d1);
		$n[4]=date('m',$d1);
		$n[5]=date('Y',$d1);
		return $n;
	}
	
	private function getDateFromDatevalues($d) {
		//liefert einen DATETIME-String zurück, der aus den einzelnen Teilen eines Arrays besteht (Sekunden/Minuten/Stunden/Tage/Monate/Jahre)
		return date("Y-m-d H:i:s",strtotime($d[5].'-'.$d[4].'-'.$d[3].' '.$d[2].':'.$d[1].':'.$d[0]));
	}
	
	private function parseDate($n) {
		$n=strtoupper($n);
		$n=str_replace('{SECOND}',date('s'),$n);
		$n=str_replace('{MINUTE}',date('i'),$n);
		$n=str_replace('{HOUR}',date('H'),$n);
		$n=str_replace('{DAY}',date('d'),$n);
		$n=str_replace('{MONTH}',date('m'),$n);
		$n=str_replace('{YEAR}',date('Y'),$n);
		return $n;
	}
	
	private function getInterval($yMin,$yMax,$chartTicksY,$nice) {
		if ($yMin>$yMax) {	//min/max ggf. vertauschen
			$tmp=$yMin;
			$yMin=$yMax;
			$yMax=$tmp;
		}
		
		if ($yMin==$yMax) {	//Sonderfall: min==max => generisches Intervall
			if ($yMin>0) {
				$yMin=0;
			} else if ($yMin<0) {
				$yMax=0;
			} else {
				$yMax=1;
			}
		}

		if ($nice==1) {	//Algorithmus 1
			$yMinRaw=$yMin;
			$yMaxRaw=$yMax;

//### die Schleife ist bislang überflüssig - ymin/ymax werden ggf. um 1x tickspacing erweitert und das sollte (in allen Fällen?) genügen
//			do {
				$range=$this->getNiceInterval(($yMax-$yMin),false);
				$tickSpacing=$this->getNiceInterval(($range/$chartTicksY),true);
	
				$tickCount=truncFloat($range/$tickSpacing);
				$yMax=ceil($yMax/$tickSpacing)*$tickSpacing;
				$yMin=$yMax-($tickSpacing*$tickCount);
	
				if ($yMinRaw>=0 && $yMin<0) {	//Sonderfall: min_nice<0 (aber min>=0) => ins Positive verschieben 
					$yMin=0;
					$yMax=$range;
				}

				if ($yMin>$yMinRaw) {
					$yMin-=$tickSpacing;
					$tickCount++;
				}
	
				if ($yMax<$yMaxRaw) {
					$yMax+=$tickSpacing;
					$tickCount++;
				}

//			} while (($yMin>$yMinRaw || $yMax<$yMaxRaw));			

			$range=($yMax-$yMin);
			return array($yMin,$yMax,$range,$tickCount,$tickSpacing);

		} else {	//ohne nice
			$range=$yMax-$yMin;
			$tickSpacing=$range/$chartTicksY;
			$tickCount=truncFloat($range/$tickSpacing);
			return array($yMin,$yMax,$range,$tickCount,$tickSpacing);
		}
	}
	
	private function getNiceInterval($range,$round) {
		$exp=floor(log10($range));
		$frac=$range/pow(10,$exp);
		if ($round) {
			if ($frac<1.5) {
				$r=1;
			} else if ($frac<3) {
				$r=2;
			} else if ($frac<7) {
				$r=5;
			} else {
				$r=10;
			}
		} else {
			if ($frac<=1) {
				$r=1;
			} else if ($frac<=2) {
				$r=2;
			} else if ($frac<=5) {
				$r=5;
			} else {
				$r=10;
			}
		}
		return $r*pow(10,$exp);
	}
	
	private function getKummulationData() {
		if ($this->chartMeta['mode']==1) {$xFrom=0; $xTo=59;	$unitName='Sekunde';} //Sekunden
		if ($this->chartMeta['mode']==2) {$xFrom=0; $xTo=59;	$unitName='Minute';} //Minuten
		if ($this->chartMeta['mode']==3) {$xFrom=0; $xTo=23;	$unitName='Stunde';} //Stunden
		if ($this->chartMeta['mode']==4) {$xFrom=0; $xTo=6;		$unitName='';	} //Wochentage (selbsterklärend)
		if ($this->chartMeta['mode']==5) {$xFrom=1; $xTo=31;	$unitName='Tag';} //Tage
		if ($this->chartMeta['mode']==6) {$xFrom=0; $xTo=53;	$unitName='Woche';} //Wochen
		if ($this->chartMeta['mode']==7) {$xFrom=1; $xTo=12;	$unitName='Monat';} //Monate
		if ($this->chartMeta['mode']==8) {$xFrom=intval(date('Y',strtotime($this->chartInt1))); $xTo=intval(date('Y',strtotime($this->chartInt2))); $unitName='Jahr';} //Jahre
		if ($this->chartMeta['mode']==9) {$xFrom=0; $xTo=0; $unitName='';} //Gesamter Zeitraum (selbsterklärend)
	
		return array($xFrom,$xTo,$unitName);
	}

}

?>
###[/VISU.PHP]###


###[VISU.JS]###
VSE_VSEID_CONSTRUCT=function(elementId,obj) {
	var n="<canvas id='e-"+elementId+"-canvas' style='position:absolute; left:0; top:0; width:100%; height:100%; box-sizing:border-box;'></canvas>";
	n+="<div id='e-"+elementId+"-reloadanim' class='reloadAnim'></div>";
	obj.innerHTML=n;

	obj.dataset.blocked=0;
	obj.dataset.bufftext=null;	
	obj.dataset.buffkovalue=null;	

	if (visuElement_hasCommands(elementId)) {
		visuElement_onClick(obj,function(veId,objId){visuElement_doCommands(veId);});
	}
}

VSE_VSEID_REFRESH=function(elementId,obj,isInit,isRefresh,isLive,isActive,koValue) {
	//keine Seitensteuerung/Befehle angegeben: VE ist klicktranparent
	if (!visuElement_hasCommands(elementId)) {obj.style.pointerEvents="none";}

	//Text immer zentrieren, kein Padding
	obj.style.textAlign="center";
	obj.style.padding="0";

	if (isInit || (isRefresh && obj.dataset.var7==1)) {
		VSE_VSEID_render(elementId,false);
	}
}

VSE_VSEID_render=function(elementId,fromBuffer) {
	var d=document.getElementById("e-"+elementId);
	if (d) {
	
		var koValue=visuElement_getKoValue(d.dataset.id,1);
		var chartDateFromTo=visuElement_parseString(visuElement_getText(elementId),koValue);
	
		if (d.dataset.blocked==0 || fromBuffer) {	
			document.getElementById("e-"+elementId+"-reloadanim").style.display="inline";
			d.dataset.blocked=1;			
			visuElement_clearTimeout(elementId,1);

			var canvas=document.getElementById("e-"+elementId+"-canvas");	
			visuElement_callPhp("chartDraw",{elementId:elementId,chartId:d.dataset.var1,width:canvas.offsetWidth,height:canvas.offsetHeight,fontSize:(d.style.fontSize.replace("px","")),dateRange:chartDateFromTo,koValue:koValue,titleMode:d.dataset.var3,xAxisMode:d.dataset.var4,xTicksMode:d.dataset.var5,captionOpacity:d.dataset.var6},null);
		} else {
			d.dataset.blocked=2;
			d.dataset.bufftext=chartDateFromTo;	
			d.dataset.buffkovalue=koValue;	
		}
	}
}

VSE_VSEID_callbackSetTimer=function(elementId) {
	var d=document.getElementById("e-"+elementId);
	if (d) {
		document.getElementById("e-"+elementId+"-reloadanim").style.display="none";
		if (d.dataset.blocked==1) {
			d.dataset.blocked=0;
			if (d.dataset.var2>0) {
				visuElement_setTimeout(elementId,1,d.dataset.var2*1000,function(id){VSE_VSEID_render(id,false);});
			}
		} else if (d.dataset.blocked==2) {
			d.dataset.blocked=1;
			VSE_VSEID_render(elementId,d.dataset.bufftext,d.dataset.buffkovalue,true);
		}
	}
}

VSE_VSEID_drawTitel=function(elementId,chart,yAxisMaxWidth,title,int1,int2) {
	var d=document.getElementById("e-"+elementId);
	if (d && d.dataset.var6>0) {
		var canvas=document.getElementById("e-"+elementId+"-canvas");
		if (canvas.getContext) {
			var c=canvas.getContext("2d");

			c.fillStyle=visuElement_getFgColor(d,0);	//Hinweis: var(--fgc0) funktioniert bei Canvas nicht
			c.textBaseline="middle";

			//Start/Enddatum
			if (d.dataset.var3 & 1) {
				c.globalAlpha=d.dataset.var6/100;
	
				c.setLineDash([1,2]);
				c.beginPath();
					c.moveTo(chart[0]+0.5,chart[1]-5);
					c.lineTo(chart[0]+0.5,(chart[3]-chart[1])+5);
					c.moveTo((chart[2]-chart[0])-yAxisMaxWidth+0.5,chart[1]-5);
					c.lineTo((chart[2]-chart[0])-yAxisMaxWidth+0.5,(chart[3]-chart[1])+5);
				c.stroke();
				c.setLineDash([]);
	
				c.textAlign='left';
				c.fillText(int1,chart[0],chart[1]/2);	
				c.textAlign='right';
				c.fillText(int2,(chart[2]-chart[0]),chart[1]/2);
			}

			//Diagramm-Titel
			if ((d.dataset.var3 & 2) && title!="") {
				c.globalAlpha=1;
				c.textAlign="center";
				c.fillText(title,(chart[0]+(chart[2]-chart[0]))/2,chart[1]/2);
			}
		}
	}
}

VSE_VSEID_drawXaxis=function(elementId,chart,yAxisMaxWidth,textHeight,points1,points2,title,invert) {
	var d=document.getElementById("e-"+elementId);
	if (d && d.dataset.var6>0) {
		var canvas=document.getElementById("e-"+elementId+"-canvas");
		if (canvas.getContext) {
			var c=canvas.getContext("2d");

			c.globalAlpha=d.dataset.var6/100;

			c.lineWidth=1;
			c.shadowOffsetX=0;
			c.shadowOffsetY=0;
			c.shadowBlur=0;
			c.shadowColor="rgba(0,0,0,0)";

			c.strokeStyle=visuElement_getFgColor(d,0);	//Hinweis: var(--fgc0) funktioniert bei Canvas nicht
			c.fillStyle=visuElement_getFgColor(d,0);
			c.textBaseline="middle";

			//Beschriftung: Einheit
			if (d.dataset.var4 & 1) {
				c.textAlign='right';
				c.fillText(title,(chart[2]-chart[0]),(chart[3]-chart[1]/2));
			}

			//Tick-Beschriftung
			var compress=((chart[2]-(2*chart[0])-yAxisMaxWidth)/(points1.length))-2;
			if (d.dataset.var4 & 2) {
				c.textAlign="center";
				c.beginPath();
				for (var t=0;t<points1.length;t++) {
					c.fillText(points1[t].c,parseInt(chart[0]+points1[t].x)+0.5,(chart[3]-chart[1]/2),compress);
				}
				c.stroke();
				
				if (invert) {
					c.save();
						c.globalAlpha=1;
						c.globalCompositeOperation="xor";
						c.fillRect(chart[0],(chart[3]-chart[1]/2)-(textHeight/2)-1,(chart[2]-2*chart[0])-yAxisMaxWidth,textHeight+2);
					c.restore();
				}
			}

			//Ticks
			if (d.dataset.var5 & 1) {
				c.setLineDash([1,2]);
				c.beginPath();
				for (var t=0;t<points1.length;t++) {
					c.moveTo(parseInt(chart[0]+points1[t].x)+0.5,(chart[3]-chart[1])-0);
					c.lineTo(parseInt(chart[0]+points1[t].x)+0.5,(chart[3]-chart[1])+5);
				}
				c.stroke();
				c.setLineDash([]);
			}

			//Trennlinien-Beschriftung
			if (d.dataset.var4 & 4) {
				c.textAlign="left";
				for (var t=0;t<points2.length;t++) {
					c.save();
						c.translate(parseInt(chart[0]+points2[t].x)+0.5,(chart[3]-chart[1])-(textHeight/2));
						c.rotate(-90*Math.PI/180);
						c.fillText(points2[t].c,0,0);
					c.restore();
				}
			}
			
			//Trennlinien
			if (d.dataset.var5 & 2) {
				c.textAlign="left";
				c.setLineDash([1,2]);
				c.beginPath();
				for (var t=0;t<points2.length;t++) {
					if (d.dataset.var4 & 4) {
						c.moveTo(parseInt(chart[0]+points2[t].x)+0.5,chart[1]);
						c.lineTo(parseInt(chart[0]+points2[t].x)+0.5,(chart[3]-chart[1])-c.measureText(points2[t].c).width-textHeight);
					} else {
						c.moveTo(parseInt(chart[0]+points2[t].x)+0.5,chart[1]);
						c.lineTo(parseInt(chart[0]+points2[t].x)+0.5,(chart[3]-chart[1])-(textHeight/2));
					}
				}
				c.stroke();
				c.setLineDash([]);
			}

		}
	}
}

VSE_VSEID_drawYaxis=function(elementId,showAxis,chart,axis) {
	var d=document.getElementById("e-"+elementId);
	if (d) {
		var canvas=document.getElementById("e-"+elementId+"-canvas");
		if (canvas.getContext) {
			var c=canvas.getContext("2d");

			var ygrids=new Array();
			var yAxisMaxWidth=0;

			for (var id=0;id<axis.length;id++) {
	
				var yfak=(chart[3]-(2*chart[1]))/axis[id].interval[3];

				c.fillStyle=axis[id].style[0];
				c.strokeStyle=axis[id].style[0];
				c.lineWidth=1;
				c.globalAlpha=1;
				c.shadowOffsetX=0;
				c.shadowOffsetY=0;
				c.shadowBlur=0;
				c.shadowColor="rgba(0,0,0,0)";
				c.lineCap="round";
				c.lineJoin="round";		
				c.textAlign="left";
				c.textBaseline="middle";	

				//Y-Ticks: Textbreite ermitteln und merken
				var maxTextWidth=0;
				if (showAxis) {
					for (var t=0;t<=axis[id].interval[3];t++) {
						var yValue=axis[id].interval[1]-(t*axis[id].interval[4]);
						var x=c.measureText(parseFloat(yValue.toFixed(2))).width;
						if (x>maxTextWidth) {maxTextWidth=x;}
					}
	
					//Y-Ticks beschriften
					var x=chart[2]-chart[0]-yAxisMaxWidth-maxTextWidth;
					for (var t=0;t<=axis[id].interval[3];t++) {
						var yValue=axis[id].interval[1]-(t*axis[id].interval[4]);
						var y=parseInt(chart[1]+(yfak*t));
						c.fillText(parseFloat(yValue.toFixed(2)),x,y);
						c.beginPath();
							c.moveTo(x+0.5-2-1,y+0.5);
							c.lineTo(x+0.5-2,y+0.5);
						c.stroke();
					}
	
					//Y-Achse zeichnen und beschriften
					c.textAlign="right";
					c.textBaseline="top";
					var x=chart[2]-chart[0]-yAxisMaxWidth-maxTextWidth;
					c.beginPath();
						c.moveTo(x+0.5-2,parseInt(chart[3]-chart[1])+0.5);
						c.lineTo(x+0.5-2,parseInt(chart[1])+0.5);
					c.stroke();
					c.save();
						c.translate(x-axis[id].style[1]-3-2,parseInt(chart[1])-(axis[id].style[1]/4));
						c.rotate(-90*Math.PI/180);
						c.fillText(axis[id].style[2],0,0);
					c.restore();
				}

				//Y-Grid zeichnen
				if (axis[id].ygrid[0]>=1) {
					if (axis[id].ygrid[0]==1) {c.strokeStyle=visuElement_getFgColor(d,0);}	//Hinweis: var(--fgc0) funktioniert bei Canvas nicht
					c.globalAlpha=parseFloat(axis[id].ygrid[1]/100);
					c.setLineDash([1,2]);
					c.beginPath();	
					var x=chart[2]-chart[0]-yAxisMaxWidth-maxTextWidth;
					for (var t=0;t<=axis[id].interval[3];t++) {
						var y=parseInt(chart[1]+(yfak*t));
						if (ygrids.indexOf(y)<0 || axis[id].ygrid[2]==1) {
							c.moveTo(parseInt(chart[0]),y+0.5);
							c.lineTo(x+0.5-2,y+0.5);
							ygrids.push(y);
						}
					}
					c.stroke();
					c.setLineDash([]);
				}

				if (showAxis) {
					//ggf. currentValue zeichnen
					if (axis[id].style[3]!='') {
						c.strokeStyle=axis[id].style[0];
						c.lineWidth=2;
						c.globalAlpha=1;
						var x=chart[2]-chart[0]-yAxisMaxWidth-maxTextWidth;
						var yValue=parseFloat(axis[id].style[3]);
						var t=(axis[id].interval[1]-yValue.toFixed(2))/axis[id].interval[4];
						var y=chart[1]+(yfak*t);
						c.beginPath();
							c.moveTo(x+0.5-3-(axis[id].style[1]/3),y-(axis[id].style[1]/4));
							c.lineTo(x+0.5-3,y+0.5);
							c.lineTo(x+0.5-3-(axis[id].style[1]/3),y+(axis[id].style[1]/4));
						c.stroke();
					}

					yAxisMaxWidth+=maxTextWidth+axis[id].style[1]+5+2;
				}
			}

			return yAxisMaxWidth;
		}
	}
	return 0;
}

VSE_VSEID_drawChart=function(clip,chart,elementId,chartTyp,clustering,showMinMax,chartYmin,chartYmax,style,points) {
	var d=document.getElementById("e-"+elementId);
	if (d) {
		var canvas=document.getElementById("e-"+elementId+"-canvas");
		if (canvas.getContext) {
			var c=canvas.getContext("2d");

			c.lineCap="round";
			c.lineJoin="round";
			c.fillStyle=style[0];
			c.strokeStyle=style[0];
			c.globalAlpha=style[1];
			c.lineWidth=style[2];
			c.shadowOffsetX=style[3];
			c.shadowOffsetY=style[3];
			c.shadowBlur=style[3];
			c.shadowColor="rgba(0,0,0,0.6)";

			if (clip) {
				c.save();
				var path1 = new Path2D();
				path1.rect(chart[0],0,(chart[2]-chart[0])-chart[4]-chart[0],chart[3]);
				c.clip(path1);
			}

			var lineWidth=c.lineWidth;
			var tmax=points.length;
		
			if (tmax<3) {return false;}	//mind. 3 Punkte: Fake(t=0),...Datenpunkte...,Fake(t=max)

			//Linienchart
			if (chartTyp==1 || chartTyp==101) {	
				c.beginPath();
				if (chartTyp==101) {
					c.moveTo(points[0].x+points[0].w,chartYmin);
					c.lineTo(points[0].x+points[0].w,points[0].y);
				} else {
					c.moveTo(points[0].x+points[0].w,points[0].y);
				}
				for (var t=0;t<tmax;t++) {
					c.lineTo(points[t].x+points[t].w,points[t].y);
					drawMinMax(t,points[t].w);
				}
				if (chartTyp==101) {
					c.lineTo(points[tmax-1].x+points[tmax-1].w,chartYmin);
					c.lineTo(points[0].x+points[0].w,chartYmin);
					c.fill();
				} else {
					c.stroke();
				}
			}

			//Flankenchart (ohne OffsetX)
			if (chartTyp==2 || chartTyp==102) {	
				c.beginPath();
				if (chartTyp==102) {
					c.moveTo(points[0].x,chartYmin);
					c.lineTo(points[0].x,points[0].y);
				} else {
					c.moveTo(points[0].x,points[0].y);
				}
				for (var t=1;t<tmax;t++) {
					c.lineTo(points[t].x,points[t-1].y);
					c.lineTo(points[t].x,points[t].y);
					drawMinMax(t,points[t].w);
				}
				if (chartTyp==102) {
					c.lineTo(points[tmax-1].x,chartYmin);
					c.lineTo(points[0].x,chartYmin);
					c.fill();
				} else {
					c.stroke();
				}
			}

			//Balkenchart (normal oder Alpha) (ohne OffsetX)
			if (chartTyp==3 || chartTyp==103) {	
				for (var t=1;t<(tmax-1);t++) {
					if (chartTyp==103) {
						c.globalAlpha=((chartYmin-points[t].y)/Math.abs(chartYmax-chartYmin))*style[1];
					}
					if (clustering) {
						//Kummulation
						var xwidth=points[t].w2-0.5;
						if (xwidth<0.5) {xwidth=0.5;}
						c.fillRect(points[t].x-points[t].w2/2,points[t].y,xwidth,chartYmin-points[t].y);
						drawMinMax(t,0);
					} else {
						//Normal
						if (t<(tmax-1)) {var xwidth=(points[t+1].x-points[t].x)-0.5;} else {var xwidth=0.5;}
						if (xwidth<0.5) {xwidth=0.5;}
						c.fillRect(points[t].x,points[t].y,xwidth,chartYmin-points[t].y);
						drawMinMax(t,xwidth/2);
					}
				}
			}

			//Balkenchart (konstante Breite)
			if (chartTyp==9) {	
				for (var t=1;t<(tmax-1);t++) {
					c.fillRect(points[t].x-lineWidth/2,points[t].y,lineWidth,chartYmin-points[t].y);
					drawMinMax(t,0);
				}
			}

			//Bezierchart (mittelwertig)
			if (chartTyp==4 || chartTyp==104) {	
				c.beginPath();
				if (chartTyp==104) {
					c.moveTo(points[0].x+points[0].w,chartYmin);
					c.lineTo(points[0].x+points[0].w,points[0].y);
				} else {
					c.moveTo(points[0].x+points[0].w,points[0].y);
				}
				for (var t=0;t<(tmax-1);t++) {
					c.quadraticCurveTo(points[t].x+points[t].w,points[t].y,(points[t].x+points[t+1].x)/2+points[t].w,(points[t].y+points[t+1].y)/2);
					drawMinMax(t,points[t].w);
				}
				c.lineTo(points[tmax-1].x+points[tmax-1].w,points[tmax-1].y);
				if (chartTyp==104) {
					c.lineTo(points[tmax-1].x+points[tmax-1].w,chartYmin);
					c.lineTo(points[0].x+points[0].w,chartYmin);
					c.fill();
				} else {
					c.stroke();
				}
			}

			//Punktchart: Kreis
			if (chartTyp==5) {	
				c.lineWidth=lineWidth/2;
				for (var t=1;t<(tmax-1);t++) {
					c.beginPath();
					c.arc(points[t].x+points[t].w,points[t].y,lineWidth/2,0,2*Math.PI,false);
					c.stroke();
					drawMinMax(t,points[t].w);
				}
			}

			//Punktchart: Kreuz
			if (chartTyp==6) {	
				c.lineWidth=lineWidth/2;
				c.beginPath();
				for (var t=1;t<(tmax-1);t++) {
					c.moveTo(points[t].x-(lineWidth/2)+points[t].w,points[t].y-(lineWidth/2));
					c.lineTo(points[t].x+(lineWidth/2)+points[t].w,points[t].y+(lineWidth/2));
					c.moveTo(points[t].x-(lineWidth/2)+points[t].w,points[t].y+(lineWidth/2));
					c.lineTo(points[t].x+(lineWidth/2)+points[t].w,points[t].y-(lineWidth/2));
					drawMinMax(t,points[t].w);
				}
				c.stroke();
			}

			//Punktchart: Strich
			if (chartTyp==7) {	
				c.beginPath();
				for (var t=1;t<(tmax-1);t++) {
					c.moveTo(points[t].x-lineWidth+points[t].w,points[t].y);
					c.lineTo(points[t].x+lineWidth+points[t].w,points[t].y);
					drawMinMax(t,points[t].w);
				}
				c.stroke();
			}

			//Punktchart: Punkt
			if (chartTyp==8) {	
				c.beginPath();
				for (var t=1;t<(tmax-1);t++) {
					c.moveTo(points[t].x-0.0001+points[t].w,points[t].y);
					c.lineTo(points[t].x+0.0001+points[t].w,points[t].y);
					drawMinMax(t,points[t].w);
				}
				c.stroke();
			}

			//Bezierchart
			if (chartTyp==10 || chartTyp==110) {	
				c.beginPath();
				if (chartTyp==110) {
					c.moveTo(points[0].x+points[0].w,chartYmin);
					c.lineTo(points[0].x+points[0].w,points[0].y);
				} else {
					c.moveTo(points[0].x+points[0].w,points[0].y);
				}

				c.lineTo(points[1].x+points[1].w,points[1].y);
				drawMinMax(1,points[1].w);

				for (var t=2;t<(tmax-1);t++) {
					var cp1=getControlpoints(points[t-2].x+points[t-2].w,points[t-2].y,points[t-1].x+points[t-1].w,points[t-1].y,points[t].x+points[t].w,points[t].y);
					var cp2=getControlpoints(points[t-1].x+points[t-1].w,points[t-1].y,points[t].x+points[t].w,points[t].y,points[t+1].x+points[t+1].w,points[t+1].y);
					if (cp1!==false && cp2!=false) {c.bezierCurveTo(cp1.x2,cp1.y2,cp2.x1,cp2.y1,points[t].x+points[t].w,points[t].y);}
					drawMinMax(t,points[t].w);
				}

				c.lineTo(points[tmax-1].x+points[tmax-1].w,points[tmax-1].y);
				if (chartTyp==110) {
					c.lineTo(points[tmax-1].x+points[tmax-1].w,chartYmin);
					c.lineTo(points[0].x+points[0].w,chartYmin);
					c.fill();
				} else {
					c.stroke();
				}
			}

			//Alphafläche (ohne OffsetX)
			if (chartTyp==11) {	
				for (var t=1;t<(tmax-1);t++) {
					c.globalAlpha=((chartYmin-points[t].y)/Math.abs(chartYmax-chartYmin))*style[1];
					if (clustering) {
						//Kummulation
						var xwidth=points[t].w2;
						if (xwidth>0) {
							c.fillRect(points[t].x-points[t].w2/2,chartYmax,xwidth,chartYmin-chartYmax);
						}
					} else {
						//Normal
						if (t<(tmax-1)) {var xwidth=(points[t+1].x-points[t].x)-0;} else {var xwidth=0;}
						if (xwidth>0) {
							c.fillRect(points[t].x,chartYmax,xwidth,chartYmin-chartYmax);
						}
					}
				}
			}

			if (clip) {c.restore();}
		}
	}
	
	function drawMinMax(t,offsetX) {
		if (showMinMax==1 && points[t].y1!==null && points[t].y2!==null) {
			c.fillRect(offsetX+points[t].x-0.5,points[t].y1,1,points[t].y2-points[t].y1);
			c.fillRect(offsetX+points[t].x-lineWidth,points[t].y1-1,lineWidth*2,1);
			c.fillRect(offsetX+points[t].x-lineWidth,points[t].y2-1,lineWidth*2,1);
		}
	}

	function getControlpoints(x1,y1,x2,y2,x3,y3) {
		var tension=0.5;
		var d1=Math.sqrt(Math.pow(x1-x2,2)+Math.pow(y1-y2,2));
		var d2=Math.sqrt(Math.pow(x2-x3,2)+Math.pow(y2-y3,2));
		var d0=d1+d2;
		if (d0==0) {
			return false;
		} else {
			return {x1:(x2-(x3-x1)*tension*d1/d0),y1:(y2-(y3-y1)*tension*d1/d0),x2:(x2+(x3-x1)*tension*d2/d0),y2:(y2+(y3-y1)*tension*d2/d0)};    
		}
	}
}
###[/VISU.JS]###


###[HELP]###
Das Visuelement "Diagramm" zeigt die Inhalte von Datenarchiven bzw. konfigurierten <link>Diagrammen***1000-130</link> grafisch an.

<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe: <link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
	<li>Diagramm: Auswahl des <link>konfigurierten Diagramms***1000-130</link>, das angezeigt werden soll</li>

	<li>
		Opazität der Beschriftungen: gibt die Opazität der nachfolgenden Beschriftungen an
		<ul>
			<li>10..100%: Hauptintervall, X-Achsenbeschriftung und X-Achsenticks werden ggf. mit dieser Opazität angezeigt</li>
			<li>unsichtbar: keinerlei Beschriftungen, als Diagrammfläche wird das gesamte Visuelement genutzt (keinerlei Ränder, die Y-Achsen und ggf. der aktuelle Wert werden nicht angezeigt, horizontale Gitterlinien werden ggf. angezeigt)</li>
			<li>Hinweis: Der Diagrammtitel wird stets mit einer Opazität von 100% angezeigt (sofern nicht die Option "unsichtbar" ausgewählt wurde).</li>
		</ul>
	</li>

	<li>Beschriftung der Titelzeile: legt fest, ob der Digrammtitel und das Hauptintervall (Datumsbereich) angezeigt werden sollen</li>

	<li>Beschriftung der X-Achse: legt fest, ob die Einheit (Legende) angezeigt und ob die Ticks beschriftet werden sollen</li>

	<li>
		Ticks auf der X-Achse: legt fest, ob die Ticks auf der X-Achse angezeigt werden sollen
		<ul>
			<li>Hinweis: "Trennlinien" sind vertikale Linien, die ggf. besondere Intervalle (z.B. einen Tageswechsel) kennzeichnen - diese Trennlinien können optional beschriftet werden (s.o.).</li>
		</ul>
	</li>

	<li>
		Aktualisierung per KO: legt fest, ob das Diagramm bei Änderung des KO1-Wertes (s.u.) aktualisiert werden soll
		<ul>
			<li>deaktiviert: das Diagramm wird ggf. ausschließlich per Intervall (s.u.) aktualisiert</li>
			<li>aktiviert: das Diagramm wird bei jeder KO1-Wertänderung aktualisiert (und ggf. zusätzlich per Intervall, s.u.)</li>
		</ul>
	</li>

	<li>
		Aktualisierung per Intervall: legt fest, in welchem Intervall das Diagramm aktualisiert werden soll
		<ul>
			<li>deaktiviert: das Diagramm wird nicht per Intervall aktualisiert (ggf. jedoch per KO, s.o.)</li>
			<li>"alle x Sekunden/Minuten/Stunden": das Diagramm wird in dem ausgewählten Intervall aktualisiert (und ggf. zusätzlich per KO, s.o.)</li>
		</ul>
	</li>
</ul>

<b>Hinweis:</b>
Wenn "Beschriftung der Titelzeile" und "Beschriftung der X-Achse" deaktiviert sind, wird die Diagrammfläche in vertikaler Richtung entsprechend vergrößert.


<h2>Kommunikationsobjekte</h2>
Dieses Visuelement kann (optional) folgende Kommunikationsobjekte (KO) verwalten:

<ul>
	<li>
		KO1: Steuerung
		<ul>
			<li>dieser KO-Wert wird zur <span style="background:#e0ffe0;">Steuerung und Beschriftung</span> verwendet (Designs, Funktionen und Formeln)</li>
			<li>immer wenn das KO auf einen Wert gesetzt wird, wird das Diagramm ggf. aktualisiert (siehe "Aktualisierung per KO")</li>
			<li>Hinweis: dieses KO kann ggf. zur Definition des Zeitintervalls des Diagramms verwendet werden (s.u.)</li>
			<li>Hinweis: wird dieses KO mit dem "KO: Status" eines Datenarchivs verknüpft, wird das Diagramm ggf. automatisch aktualisiert sobald der Inhalt des Datenarchivs verändert wird (siehe "Aktualisierung per KO")
		</ul>
	</li>

	<li>
		KO3: Steuerung des dynamischen Designs
		<ul>
			<li>dieser KO-Wert wird ausschließlich zur Steuerung eines <link>dynamischen Designs***1003</link> verwendet</li>
			<li>wenn dieses KO angegeben wurde, wird ein dynamisches Design durch dieses <i>KO3</i> gesteuert</li>
			<li>wenn dieses KO nicht angegeben wurde, wird ein dynamisches Design durch das <i>KO1</i> gesteuert</li>
		</ul>
	</li>
</ul>


<h2>Besonderheiten</h2>
<ul>
	<li>
		Im Feld "Beschriftung" (auch in dynamischen Designs) kann optional ein Zeitintervall in der Form "Startdatum ** Enddatum" angegeben werden:
		<ul>
			<li>ist eine solche Angabe vorhanden, werden die Einstellungen in der <link>Diagramm-Konfiguration***1000-130</link> ignoriert</li>
			<li>Beispiele:</li>
			<li>"**": zeigt alle verfügbaren Daten an</li>
			<li>"** now": zeigt alle verfügbaren Daten bis zum aktuellen Zeitpunkt an</li>
			<li>"-3 days ** now": zeigt alle verfügbaren Daten der letzten 3 Tage bis zum aktuellen Zeitpunkt an</li>
			<li>"-{#} days ** now": zeigt alle verfügbaren Daten der letzten x Tage bis zum aktuellen Zeitpunkt an, wobei x dem KO-Wert (s.o.) entspricht</li>
			<li>Details hierzu sind der Hilfe zur <link>Diagramm-Konfiguration***1000-130</link> zu entnehmen</li>
		</ul>
	</li>
	
	<li>Designs: Innenabstand und Textausrichtung werden ignoriert</li>
</ul>


<h2>Bedienung in der Visualisierung</h2>
Mit einem Klick auf dieses Visuelement werden alle zugewiesenen Seitensteuerungen/Befehle ausgeführt.
###[/HELP]###


