/*$('head').append('<link href="css/sunny/sunny.css" rel="stylesheet" type="text/css"> \
<script type="text/javascript" src="js/jquery-ui-1.8.12.custom.min.js"></script> \
<script type="text/javascript" src="../js/jup.1.0.1.js"></script>');*/


function trim_plugin_jup_form()
{
$("#trim_plugin_form").jup({
onComplete : function(response, formId){
//window.location.reload(true);
if(response.iSel!='')
{
$(response.iSel).attr('src',response.file)
/*var c=$(response.iSel).clone();
c.attr('src',response.img);
$(response.iSel).replaceWith(c);*/
}
if(response.tSel!='')
{
$(response.tSel).attr('src',response.tfile)
/*var c=$(response.tSel).clone();
c.attr('src',response.img);
$(response.tSel).replaceWith(c);*/
}
$('#trim_plugin_dlg').dialog('destroy');
}
});
}


function trim_plugin(file,nw,nh,tw,th,imgSel,thumbSel)
{
$.ajax({
url: 'trim.plugin.php',
data:'file='+file+'&nw='+nw+'&nh='+nh+'&tw='+tw+'&th='+th+'&ri='+imgSel+'&rt='+thumbSel,
type:'POST',
success: function(d,m,x){
if(d!='Error'){
$('#trim_plugin_content').html(d);
$('#trim_plugin_dlg').dialog({modal:true,width:($(window).width()-50),height:($(window).height()-50)});
				$('#trim_plugin_content #quality').slider({value:100,slide: function(event, ui) {$('#q').html(ui.value); }});
				$('#trim_plugin_content #sel').draggable({ containment: "#container", scroll: false,drag:function(event,ui){setVals();},stop:function(event,ui){setVals();}});
//				$('#sel').draggable({ containment: "#container", scroll: false,drag:function(event,ui){mask();},stop:function(event,ui){mask();}});
//				$('#sel').draggable({ containment: "#container", scroll: false});


				$('#timg').load(function(){
					tci_w=$('#timg').width();
					tci_h=$('#timg').height();
					taspR=tci_w/tci_h;

					if(nw==false){
						if(nh==false)
					ci_w=tci_w;
						else
					ci_w=nh*taspR;
					}else
					ci_w=nw;


				if(nh==false){
					if(nw==false)
					ci_h=tci_h;
					else
					ci_h=nw/taspR;
				}else
					ci_h=nh;

				$('#trim_plugin_content #sel').resizable({aspectRatio:ci_w/ci_h, containment: "#container",resize:function(event,ui){setVals();},stop:function(event,ui){setVals();} });
				setVals();
				});

				trim_plugin_jup_form();

				return true;
				}
				}
});
}

$(document).ready(function(){
		$('body').append("<div id='trim_plugin_dlg' style='display:none;'><div id='trim_plugin_content'></div></div>");
		
		});

