###[DEF]###
[name			=	Pool: Chlorierung]

[e#1 			=	Status: Chlorpumpe							]
[e#2 			=	Status: Pegel								]
[e#3 			=	Status: Temperatur							]
[e#4	TRIGGER	=	Tank: Inhalt (ml)							]
[e#5 	OPTION	=	Tank: Warnung (ml)				#init=3000	]
[e#6 	OPTION	=	Tank: Leer (ml)					#init=500	]
[e#7	OPTION	=	Chlorierung: Menge (ml/24h) 	#init=100	]
[e#8	OPTION	=	Chlorierung: Schwellenwert (%)	#init=80	]
[e#9 	OPTION	=	Chlorierung: Temperaturkompensation #init=0	]
[e#10 	OPTION	=	Chlorierung: Pegelkompensation (ml/Pegeleinheit)	#init=0]
[e#11 	OPTION	=	Chlorpumpe (ml/Minute) 			#init=40	]
[e#12	TRIGGER	=	Chlorierung: Manueller Zuschlag (ml)		]


[a#1			=	Status								]
[a#2			=	Ist-Wert							]
[a#3			=	Soll-Wert							]
[a#4			=	Tank: Inhalt						]
[a#5			=	Tank: Warnung						]
[a#6			=	Tank: Leer							]


[v#1 REMANENT	=0						]
[v#2 REMANENT	=0						]
[v#3 REMANENT	=-1						]
[v#4 REMANENT	=						]
[v#5 REMANENT	=0						]
###[/DEF]###


###[HELP]###
Dieser Baustein regelt die (Pool-)Chlorierung <i>ohne</i> den Einsatz einer entsprechenden Messsonde.

Grundlage der Regelung ist die Annahme, dass eine bestimmte Menge zugefügten Chlors innerhalb von 24 Stunden "verbraucht" wird: E7 legt die Menge Chlor fest (ml), die innerhalb von 24 Stunden verbraucht wird. Diese Angabe hängt u.a. von dem Wasservolumen und dem eingesetzten Chlorprodukt ab.

Sobald E1 auf einen Wert &ne;0 gesetzt wird, erfasst der Baustein dies als "Chlorzugabe": E1 sollte daher mit dem Status der Chlorpumpe verbunden sein, d.h. sobald die Chlorpumpe arbeitet sollte E1 &ne;0 gesetzt werden. E11 legt dabei fest, welche Menge Chlor die Chlorpumpe fördert (ml/Minute).

Die aktuelle Menge des "unverbrauchten" Chlors im Wasser wird ständig berechnet (A2), d.h. der Baustein ist stets aktiv und kann nicht angehalten/deaktiviert werden.

Sobald dieser Ist-Wert (A2) den Schwellenwert an E8 (prozentual, d.h. relativ zu E7) unterschreitet, wird A1 auf 1 gesetzt: Dies sollte zu einer Chlorzugabe führen, mittels E3 des <link>LBS 17900010***lbs_17900010</link> kann A1 direkt ausgewertet werden.  
Dieser Umstand alleine führt jedoch <i>nicht</i> zu einer Erfassung der zugegebenen Chlormenge - dies erfolgt erst, wenn E1 auf einen Wert &ne;0 gesetzt wird (s.o.)!

E8 legt den Schwellenwert zur Chlorierung fest: z.B. führt E8=80 dazu, dass der Ist-Wert kleiner als 80% des Soll-Wertes sein muss damit die Chlorierung startet.


<h3>Temperaturkompensation</h3>
Optional kann eine Temperaturkompensation (E9&ne;0) der Chlor-Soll-Menge aktiviert werden.
Dabei wird der Soll-Wert (E7) linear an die Wassertemperatur (E3) angepasst. Der Soll-Wert (E7) bezieht sich dabei stets auf eine Referenztemperatur von 20 Grad, d.h. bei 20 Grad Wassertemperatur entspricht der Soll-Wert exakt der Vorgabe an E7.
Pro Grad Abweichung der Wassertemperatur von diesen 20 Grad wird der Soll-Wert intern um den Wert an E9 erhöht bzw. erniedrigt. Ist z.B. E7=100 und E9=2, wird sich bei einer Wassertemperatur von 30 Grad ein Soll-Wert von 120 ergeben. Wird an E9 bzw. E3 kein Wert angegeben, wird keine Temperaturkompensation erfolgen.


<h3>Pegelkompensation</h3>
Optional kann eine Pegelkompensation (E10&ne;0) der Chlor-Soll-Menge aktiviert werden. An E2 wird dabei der aktuelle Pegelstand erwartet, ein hoher Wert entspricht einem hohen Pegelstand.
Dabei wird der Soll-Wert (E7) entsprechend erhöht, wenn der Wasserpegel seit der letzten Chlorierung gestiegen ist: Der <i>geringste</i> Pegelstand wird fortlaufend intern gespeichert und bei der nächsten Chlorierung ausgewertet.
Ist der (bei der Chlorierung aktuelle) Pegelstand höher als der bis dahin ermittelte geringste Pegelstand, wird der Soll-Wert entsprechend erhöht: E10 legt fest, um wieviel Milliliter der Soll-Wert pro Pegeleinheit (die Differenz aus dem geringsten Pegelstand und dem aktuellen Pegelstand) erhöht werden soll. 
Sobald der Chlorierung erfolgt, wird der geringste Pegelstand intern zurückgesetzt (die zugeführte Wassermenge wurde zusätzlich chloriert).
Somit ist sowohl bei einer geplanten Nachfüllung als auch z.B. bei Regen gewährleistet, dass der Soll-Wert und somit die Chlormenge entsprechend angepasst wird.


<h3>Manuelle Chlorzugabe</h3>
Bei Bedarf kann mittels E12&gt;0 <i>einmalig</i> eine beliebige Menge Chlor zugegeben werden. Wird E12 auf einen Wert &gt;0 gesetzt, wird dieser Chlormenge auf den Soll-Wert aufgeschlagen und somit bei der nächsten Chlorierung <i>einmalig</i> zugegeben.
Nach einer erfolgten Chlorierung wird dieser Zuschlag intern wieder zurückgesetzt (mit E12=0 kann der Zuschlag bei Bedarf vorzeitig zurückgesetzt werden).


<h3>Chlortank</h3>
Der Baustein setzt voraus, dass eine bestimmte Menge Chlor zu Verfügung steht (Tank). Bei ersten Start muss daher E4 auf die verfügbare Chlormenge (ml) gesetzt werden.
Bei jeder Chlorierung wird der Tankinhalt intern entsprechend der zugegebenen Menge verringert und an A4 ausgegeben.
Unterschreitet der Tankinhalt den Wert an E5, wird A5 auf 1 gesetzt (andernfalls auf 0). Dies kann als Warnhinweis genutzt werden, um einen Tankwechsel vorzubereiten.
Unterschreitet der Tankinhalt den Wert an E6, wird A6 auf 1 gesetzt (andernfalls auf 0) und die Chlorierung wird sofort beendet (bzw. nicht mehr gestartet), bis mittels E4 ein Tankwechsel signalisiert worden ist.


<b>Wichtig:</b>
Dieser Baustein verhält sich vollständig remanent, d.h. nach einem Neustart bleiben sämtliche Parameter und Werte erhalten.


<h3>Ein- und Ausgänge</h3>
E1: tatsächlicher Status der Chlorierung (i.d.R. Status-KO der Chlorpumpe), &ne;0 = Chlorierung erfolgt gerade
E2: ggf. Pegelstand, erforderlich für Pegelkompensation (s.o.)
E3: ggf. Wassertemperatur, erforderlich für Temperaturkompensation (s.o.)
E4: &ne;0 = Tankinhalt zurücksetzen (s.o.)
E5: Schwellenwert für Warnung bei geringem Tankinhalt (s.o.)
E6: Schwellenwert für leeren Tank (s.o.)
E7: Chlor-Soll-Wert in Milliliter pro 24 Stunden (s.o.)
E8: Schwellenwert (prozentual bezogen auf den Soll-Wert) für erforderliche Chlorierung (s.o.)
E9: Temperaturkompensation (s.o.), 0=deaktiviert
E10: Pegelkompensation (s.o.), 0=deaktiviert
E11: Leistung der Chlorpumpe in Millilitern pro Minute
E12: manuelle Chlorzugabge in Millilitern (s.o.)

A1: 0=keine Chlorierung erforderlich, 1=Chlorierung erforderlich
A2: aktueller Ist-Wert (Chlor)
A3: aktueller Soll-Wert (Chlor)
A4: aktueller Chlortank-Inhalt (ml)
A5: 1=Tankinhalt zu gering, 0=Tankinhalt ist noch ausreichend
A6: 1=Tank ist leer, 0=Tank ist nicht leer

<b>Hinweis:</b>
Alle Ausgänge werden nur bei einer Änderung gesetzt (SBC).

###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {	
	if (($E=logic_getInputs($id)) && ($V=logic_getVars($id))) {
	
		//Chlorierung: allererster LBS-Start (remanent!)
		if ($V[3]<0) {
			$V[1]=0;
			$V[2]=0;
			$V[3]=getMicrotime();
			logic_setVar($id,1,$V[1]);
			logic_setVar($id,2,$V[2]);
			logic_setVar($id,3,$V[3]);
		}

		//Tankinhalt: Reset
		if ($E[4]['refresh']==1 && !isEmpty($E[4]['value'])) {
			$V[2]=$E[4]['value'];
			logic_setVar($id,2,$V[2]);
			logic_setOutput($id,4,$E[4]['value']);
		}

		//Boost
		if ($E[12]['refresh']==1 && !isEmpty($E[12]['value'])) {
			$V[5]=intval($E[12]['value']);
			logic_setVar($id,5,$V[5]);
		}

		//EXEC ggf. starten
		if (logic_getStateExec($id)==0) {
			logic_callExec(LBSID,$id);
		}
	}
}
?>
###[/LBS]###


###[EXEC]###
<?
require(dirname(__FILE__)."/../../../../main/include/php/incl_lbsexec.php");
set_time_limit(0);

restore_error_handler();
error_reporting(0);

sql_connect();
$void=new LB_LBSID_chlor($id);
sql_disconnect();		


class LB_LBSID_chlor {
	private $id,$E,$V;
	private $outSBC=array(false,null,null,null,null,null,null,null,null,null,null,null,null);	//SBC-Puffer für A1..A12
	private $chlorMode=false;
	private $chlorTank=false;


	public function __construct($id) {
		$this->id=$id;
		$this->proc_run();
	}
		
	private function proc_run() {
		while (logic_getEdomiState()==1) {

			if ($this->getE() && $this->getV()) {

				//Tank: Warnung
				if ($this->V[2]>$this->E[5]['value']) {
					$this->out(5,0,true);
				} else {
					$this->out(5,1,true);
				}

				//Tank: Stopp
				if ($this->V[2]>$this->E[6]['value']) {
					$this->chlorTank=true;
					$this->out(6,0,true);
				} else {
					$this->chlorTank=false;
					$this->out(6,1,true);
				}

				//Pegel-Minimum merken für Chlorierung
				if (!isEmpty($this->E[2]['value']) && ($this->E[2]['value']<$this->V[4] || isEmpty($this->V[4]))) {
					$this->setV(4,$this->E[2]['value']);
				}

				if ($this->E[7]['value']>0) {
					//Sollwert mit Temperaturkompensation
					if (!is_numeric($this->E[9]['value'])) {$this->E[9]['value']=0;}
					if (!is_numeric($this->E[3]['value'])) {$this->E[3]['value']=20;}
					$soll=($this->E[3]['value']-20)*$this->E[9]['value']+$this->E[7]['value'];

					//Istwert (abhängig vom Sollwert, denn dieser definiert zugleich den "chemischen Verbrauch")
					$ist=$this->V[1]-(getMicrotime()-$this->V[3])/(86400/$soll);
					if ($ist<0) {$ist=0;}

					//Zuschlag: Pegel
					if (!isEmpty($this->V[4]) && !isEmpty($this->E[2]['value']) && $this->E[2]['value']>$this->V[4] && $this->E[10]['value']>0) {
						$soll+=($this->E[2]['value']-$this->V[4])*$this->E[10]['value'];
					}
					
					//Zuschlag: Boost
					if ($this->V[5]>0) {
						$soll+=$this->V[5];
					}

					//Abgabemenge erfassen
					if ($this->chlorTank && $this->E[1]['value']!=0) {
						$ml=$this->E[11]['value']/60*(getMicrotime()-$this->V[3]);
						$this->setV(2,$this->V[2]-$ml);
						$ist+=$ml;
					}

					//Chlorierung
					if ($this->chlorTank && $soll>0) {
						if ($ist<($soll/100*$this->E[8]['value'])) {
							$this->chlorMode=true;
						}

						if ($ist>=$soll) {
							if ($this->chlorMode) {
								$this->setV(4,'');
								$this->setV(5,0);
							}
							$this->chlorMode=false;
						}

					} else {	
						$this->chlorMode=false;
					}

					$this->setV(1,$ist);
					$this->out(2,intval($ist),true);
					$this->out(3,intval($soll),true);
				}

				$this->setV(3,getMicrotime());
				$this->out(1,(($this->chlorMode)?1:0),true);
				$this->out(4,intval($this->V[2]),true);
			}

			sleep(1);
		}
		sql_disconnect();		
		exit();
	}

	private function getE() {
		if ($this->E=logic_getInputs($this->id)) {return true;}
		return false;
	}
	
	private function getV() {
		if ($this->V=logic_getVars($this->id)) {return true;}
		return false;
	}

	private function setV($id,$value) {
		$this->V[$id]=$value;
		logic_setVar($this->id,$id,$value);
	}

	private function out($id,$value,$sbc=false) {
		if (is_null($this->outSBC[$id]) || $this->outSBC[$id]!=$value || !$sbc) {
			logic_setOutput($this->id,$id,$value);
		}
		$this->outSBC[$id]=$value;
	}
}

?>
###[/EXEC]###
