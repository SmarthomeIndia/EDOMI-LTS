###[DEF]###
[name    = ohne Name]

[xsize    =100]
[ysize    =50    ]

[var1    =0    ]
[var2    =    ]
[var3    =0    ]
[var4    =0    ]

[flagText        =1]
[flagKo1        =1]
[flagKo2        =0]
[flagPage        =1]
[flagCmd        =1]
[flagDesign        =1]
[flagDynDesign    =1]

[captionKo1        =KO]
###[/DEF]###


###[PROPERTIES]###
[columns=50,50]
[row]
[var1=select,1,'Eigenschaft 1','1#Dummy1|2#Dummy2|3#Dummy3']
[var2=text,1,'Eigenschaft 2','Wert']
[row]
[var3=check,1,'Eigenschaft 3','0/1']
[var4=checkmulti,1,'Eigenschaft 4','0|1|20|300']
###[/PROPERTIES]###


###[ACTIVATION.PHP]###
<?
?>
###[/ACTIVATION.PHP]###


###[SHARED.JS]###
###[/SHARED.JS]###


###[EDITOR.JS]###
function VSE_VSEID(elementId,obj,meta,property,isPreview,koValue) {
var n="";
n+="
<table cellpadding='0' cellspacing='0' border='0' style='left:0; top:0; width:100%; height:100%;'>";
    n+="
    <tr>
        <td>"+meta.itemText+"</td>
    </tr>
    ";
    n+="
</table>";

obj.innerHTML=n;

return true;
}

###[/EDITOR.JS]###


###[EDITOR.PHP]###
<?
?>
###[/EDITOR.PHP]###


###[VISU.JS]###
function VSE_VSEID_CONSTRUCT(elementId,obj) {
var n="";
n+="
<table cellpadding='0' cellspacing='0' border='0' style='width:100%; height:100%;'>";
    n+="
    <tr>
        <td><span id='e-"+elementId+"-text'></span></td>
    </tr>
    ";
    n+="
</table>";
obj.innerHTML=n;

//falls Seitensteuerung/Befehle vorhanden sind: Klick-Event zuweisen und ggf. Seitensteuerung/Befehle ausführen
if (visuElement_hasCommands(elementId)) {
visuElement_onClick(obj,function(veId,objId){visuElement_doCommands(veId);});
}
}

function VSE_VSEID_REFRESH(elementId,obj,isInit,isRefresh,isLive,isActive,koValue) {
document.getElementById("e-"+elementId+"-text").innerHTML=visuElement_parseString(visuElement_getText(elementId),koValue);

//keine Seitensteuerung/Befehle angegeben: VE ist klicktranparent
if (!visuElement_hasCommands(elementId)) {
obj.style.pointerEvents="none";
}
}
###[/VISU.JS]###


###[VISU.PHP]###
<?
?>
###[/VISU.PHP]###


###[HELP]###
Vorlage: Standard-VSE
###[/HELP]###

