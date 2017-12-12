<?php

class sys_links {

	private $db;
	static public $sdb;

	public $tbl;
	public $oname="";

	public $p;

	private $links;

	private $dataArr;
	private $db_res;
	public $uslug=false;

	static $addJS=false;



	function __construct(&$db,$icfg,$p)
	{
		global $sys_use_slugs;
		$this->db=$db;
		$this->p=$p;
		if(isset($sys_use_slugs))
		$this->uslug=$sys_use_slugs;
		$this->tbl=$p->tbl;
		$this->oname=$p->oname;
		
		$this->links=array();

		$this->dataArr=false;

		$this->db_res="sys_links_{$this->oname}";

		/*	$dl=array("types"=>array('url','articles'.. other db objects),
		 *	"num"=>false|int|true, //false=1,int=fixed num, true = 1 and more
		 *	"t"=>"Link", //Title
		 *	"afterFld"=>false); true=first|field name=after field|false=last
		 */

		$dl=array("types"=>array('url'),"num"=>false,"t"=>"Link",'d'=>'',"afterFld"=>false,"name"=>false,"target"=>array("_self"=>"Self","_blank"=>"New page","_parent"=>"Parent"));

		foreach($icfg as $k=>$v){
			$this->links[$k]=array_merge($dl,$v);
			if(!is_array($this->links[$k]['types']))
				$this->links[$k]['types']=array($this->links[$k]['types']);
		}


	}

	function autoCtrlChkArr($fn)
	{
		foreach($this->links as $ln=>$lv){
			if($lv['afterFld']===$fn)
				return true;
		}
		return false;
	}

	function autoCtrlArr($fn)
	{
		$ret=array();
		foreach($this->links as $ln=>$lv){
			if($lv['afterFld']==$fn)
			{
				$ret["sys_links_{$ln}"]=array("c"=>"sys_links","t"=>$lv['t'],"d"=>$lv['d'],"ln"=>$ln);
			}
		}
		return $ret;
	}


	function hC($pref,$ln=false,$ci=false){
		$ret="sys_links_$pref";
		$ret.=($ln!=false ? "_".$ln : "" );
		$ret.=($ci!=false ? "_".$ci : "" );

		$ret=$this->p->hC($ret);

		return $ret;
	}

	function hID($pref,$ln=false,$ci=false)
	{
		$ret=$this->p->hID("sys_links_$pref");
		$ret.=($ln!=false ? "_".$ln : "" );
		$ret.=($ci!=false ? "_".$ci : "" );

		return $ret;
	}


	

	function drawAdminCtrl($ln,$ajax=0,$lang=false)
	{
		$this->p->setCopts(array("echo_INS"=>false,"echo"=>false));

		if($lang==false)
			$lang=$this->p->def_lang;

		foreach($this->links[$ln]['types'] as $v)
		{
			if($v!="url" && (!isset($this->p->p->t[$v]) || !is_object($this->p->p->t[$v])))
				unset($this->links[$ln]['types'][array_search($v,$this->links[$ln]['types'])]);
		}
		if(count($this->links[$ln]['types'])==0){
			echo "Non of link types tables exists!";
			return false;
		}

		$hurl=false;
		$tops="";
		$tto=array();
		$tls=$this->links[$ln];

		$cs=false;
		$jsr="";

		$tsels=array();
		if($ajax==0 && isset($this->p->cD['id']) && $this->p->cD['id']!=false){
			$this->db->query("select * from {$this->p->p->db_prefix}sys_links where tbl='{$this->tbl}' and tid={$this->p->cD['id']} and link='$ln'","{$this->db_res}_drawACtrlSel");
			$cs=array();
			while($row=$this->db->next("{$this->db_res}_drawACtrlSel")){
				$cs[$ln][$row['linkn']]=$row;
				$sttype="url";
				$tsels[$row['linkn']]="disabled='true'";
				if($row['type']!=0)
					$sttype=$row['rtbl'];
				if(count($this->links[$ln]['types'])>1){
					$jsr.="$('#".$this->hID("sel",$ln,$row['linkn'])."').val('{$sttype}'); \n";
				}				
				if($row['type']!=0){
					$jsr.="sys_links_ccss=$('#".$this->hID("sp_sel",$ln,$row['linkn'])."_{$sttype}').removeAttr('disabled').val('{$row['rid']}').get(0); \n\n";
					$jsr.=" $('#".$this->hID("link_name",$ln,$row['linkn'])."_{$sttype}').removeAttr('disabled').val(sys_links_ccss.options[sys_links_ccss.selectedIndex].text); \n\n";			
				}else
					$jsr.=" $('#".$this->hID("link_name",$ln,$row['linkn'])."_{$sttype}').removeAttr('disabled'); \n\n";			

				$jsr.=" $('#".$this->hID("sp_targ",$ln,$row['linkn'])."_{$sttype}').removeAttr('disabled').val('{$row['target']}'); \n\n";


				$jsr.=" $('#".$this->hID("sp",$ln,$row['linkn'])."_{$sttype}').show(); \n\n";
			}

		}



		if(in_array("url",$this->links[$ln]['types']))	
		{
			$hurl=true;
			unset($tls['types'][array_search("url",$tls['types'])]);					
		}

		$addonp=(!is_numeric($tls['num']) ? true : false);
		if($addonp){
			if(count($cs[$ln])>0)
				$lnum=count($cs[$ln]);
			else
				$lnum=($tls['num'] ? 1 : 0);
		}
		else
			$lnum=$tls['num'];
		
		$targets="";
		foreach($tls['target'] as $target_id=>$target_name)
			$targets.="<option value='{$target_id}'>{$target_name}</option>";			


		$hurl_dis=""		;
		if(count($tls['types'])>0)
				$hurl_dis="disabled='true'";

		foreach($tls['types'] as $t){
			if(!is_object($this->p->p->t[$t]))
				continue;
			$tto[$t]=$this->p->p->t[$t]->sel(false,array("echo"=>false));

				$tops.="<option value='$t'>$t</option>";
			if($tto[$t]==false)
				$tto[$t]="<option value='err'>$t is empty (add some)</option>";
	
		}
		if($hurl)
			$tops.="<option value='url'>URL</option>";
		$sbr="";
		$sbrn="";


		if($ajax==0)
			$ret.="<div id='".$this->hID("ndiv",$ln)."'>";

		$cistart=1;
		$ajaxstyle="";
		if($ajax>0){
			$lnum=$cistart=$ajax;
			$ajaxstyle="style='color:red'";
		}


		for($ci=$cistart;$ci<=$lnum;$ci++){
			$setstyle="";
			if(isset($cs[$ln][$ci]))
				$setstyle="style='display:none'";

			$ret.="<div id='".$this->hID("ctrl",$ln,$ci)."'>";
			if($tls['num']!=1)
				$sbr="<span $ajaxstyle >$ci: </span>";

			if($tls['name'])
				$ret.="Name <input type='text' name='".$this->p->in_a("[links][{$ln}][${ci}][name]",$lang)."' value='".$this->iv($cs,$ln,$ci,"name")."'> ";
			else
				$sbrn=$sbr;

			if(count($this->links[$ln]['types'])>1){
				$ret.="{$sbrn}<select name='".$this->p->in_a("[links][{$ln}][${ci}][type]",$lang)."' class='".$this->hID("sel_class",$ln,$ci)."sys_links sys_links_{$this->oname} sys_links_l_{$ln} sys_links_n_{$ci}' id='".$this->hID("sel",$ln,$ci)."' onchange=\"
					$('.".$this->hID("hide_class",$ln,$ci)."').hide();$('.".$this->hID("hide_class",$ln,$ci)." select, .".$this->hID("name_hide_class",$ln,$ci)."').each(function(){\$(this).attr('disabled',true);});$('#".$this->hID("sp",$ln,$ci)."_'+this.value+' select, #".$this->hID("link_name",$ln,$ci)."_'+this.value).each(function(){\$(this).removeAttr('disabled');});$('#".$this->hID("sp",$ln,$ci)."_'+this.value).show();if(this.value!='url'){\$('#".$this->hID("link_name",$ln,$ci)."_'+this.value).val($('#".$this->hID("sp_sel",$ln,$ci)."_'+this.value+' option:selected').text());} \">";
				$ret.="$tops </select> ";
			}else
				$ret.="{$sbrn}<input type='hidden' name='".$this->p->in_a("[links][{$ln}][${ci}][type]",$lang)."' value='".reset($this->links[$ln]['types'])."'>";

			$afirst=false;
			foreach($tls['types'] as $t)
			{									
				if($tto[$t]){
					if($afirst==false)
						$afirst=$t;
					$ret.="<span id='".$this->hID("sp",$ln,$ci)."_{$t}' class='".$this->hID("hide_class",$ln,$ci)."' $setstyle >
						<select {$tsels[$ci]} name='".$this->p->in_a("[links][{$ln}][${ci}][rid]",$lang)."' class='sys_links sys_links_{$this->oname}_{$t} sys_links_t_{$t} sys_links_l_{$ln} sys_links_n_{$ci}' id='".$this->hID("sp_sel",$ln,$ci)."_{$t}' 
						onchange=\"$('#".$this->hID("link_name",$ln,$ci)."_{$t}').val(this.options[this.selectedIndex].text);\">";
					$ret.="$tto[$t] </select> <select {$tsels[$ci]} name='".$this->p->in_a("[links][{$ln}][${ci}][target]",$lang)."' id='".$this->hID("sp_targ",$ln,$ci)."_{$t}'  >{$targets}</select></span>";
					$setstyle="style='display:none'";
					$ret.="<input {$tsels[$ci]} name='".$this->p->in_a("[links][{$ln}][${ci}][name]",$lang)."' type='hidden' id='".$this->hID("link_name",$ln,$ci)."_{$t}' class='".$this->hID("name_hide_class",$ln,$ci)."'>";
				}
				$tsels[$ci]="disabled='true'";
			}
			if($hurl){
				$ret.="<span id='".$this->hID("sp",$ln,$ci)."_url' class='".$this->hID("hide_class",$ln,$ci)."' $setstyle >";
				if(!isset($tls['noname']) || $tls['noname']!=true)
					$ret.="<input {$hurl_dis} name='".$this->p->in_a("[links][{$ln}][${ci}][name]",$lang)."' value='".$this->iv($cs,$ln,$ci,"name")."' type='text' id='".$this->hID("link_name",$ln,$ci)."_url' class='".$this->hID("name_hide_class",$ln,$ci)."'>";

				$ret.="<input type='text' name='".$this->p->in_a("[links][{$ln}][${ci}][url]",$lang)."' value='".$this->iv($cs,$ln,$ci,"url")."' class='sys_links sys_links_{$this->oname}_url sys_links_t_url sys_links_l_{$ln} sys_links_n_{$ci}'>";
				$ret.="<select {$hurl_dis} name='".$this->p->in_a("[links][{$ln}][${ci}][target]",$lang)."' id='".$this->hID("sp_targ",$ln,$ci)."_url'  >{$targets}</select></span>";
			}
			if($addonp && ($tls['num']==false || $ci>1))
				$ret.="<a href='javascript:' style='color:red' onclick=\"$('#".$this->hID("ctrl",$ln,$ci)."').hide();$('#".$this->hID("ctrlres",$ln,$ci)." span').html('<input type=\'hidden\' name=\'".$this->p->in_a("[links][del][{$ln}][]",$lang)."\' value=\'{$ci}\'/>');$('#".$this->hID("ctrlres",$ln,$ci)."').show();\"> X </a>";

			$ret.="</div>";
			if($addonp && ($tls['num']==false || $ci>1))
				$ret.="<div id='".$this->hID("ctrlres",$ln,$ci)."' style='display:none'><a href='javascript:' style='color:green' onclick=\"$('#".$this->hID("ctrlres",$ln,$ci)." span').html('');$('#".$this->hID("ctrlres",$ln,$ci)."').hide();$('#".$this->hID("ctrl",$ln,$ci)."').show();\">Restore</a><span></span></div>";
		}


		if($ajax>0){
			if($afirst){
				$ret.="<script type='text/javascript'>$('#".$this->hID("link_name",$ln,$ajax)."_{$afirst}').val($('#".$this->hID("sp_sel",$ln,$ajax)."_{$afirst} option:selected').text()); </script>";
			}
			die($ret);
		}

		$ret.="</div>";

		if($addonp)
			$ret.="<input type='button' value='Add Another Link' onclick=\"$.get(".$this->p->aL("index.php","a=dbo_{$this->oname}&f=sys_links_{$ln}&rid=")."+".$this->hID("counter_sys_files",$ln).",function(d){\$('#".$this->hID("ndiv",$ln)."').append(d);".$this->hID("counter_sys_files",$ln)."++;});\">";

		$ret.="<script type='text/javascript'> \n var ".$this->hID("counter_sys_files",$ln)."=".($lnum+1)."; \n $jsr </script>";

		$this->p->resetOpts();

		return $ret;

	}





	function preAdb($act,$data)
	{
		if(isset($data['links']['del']) && is_array($data['links']['del'])){
			foreach($data['links']['del'] as $ln=>$lv){
				foreach($lv as $la) //was  $$la
					unset($data['links'][$ln][$la]);
			}
		}

		$this->dataArr=$data['links'];
	}

	function postAdb($act,$id,$rdt)
	{
		if($id==false)
			return false;
		if($act=="d"){
			foreach($rdt as $v)
				$this->db->query("delete from {$this->p->p->db_prefix}sys_links where tbl='{$this->tbl}' and tid={$v['id']}");

			return true;

		}

		if(!is_array($this->dataArr))
			return false;

		$iou=false;

		$cdel=false;

		if(isset($this->dataArr['del'])){
			$cdel=$this->dataArr['del'];
			unset($this->dataArr['del']);
		}

		if($act=="u")
		{
			foreach($this->dataArr as $ln=>$la)
			{

				$this->db->query($q="select * from {$this->p->p->db_prefix}sys_links where tbl='{$this->tbl}' and tid=$id and link='{$ln}'","{$this->db_res}_postAdbSel");
				while($row=$this->db->next("{$this->db_res}_postAdbSel"))
				{
					if(isset($la[$row['linkn']]))				
					{
						$tname=(isset($la[$row['linkn']]['name'])? $this->db->escape($la[$row['linkn']]['name']) : "" );
						$ttype=($la[$row['linkn']]['type']=='url'? 0 : 1);
						$turl='';
						$rtype='';
						$trid=0;
						
						$ttarg=$la[$row['linkn']]['target'];
						$ltbl='';
						$lslug='';
						
						if($ttype!=0){
						$rtype=$la[$row['linkn']]['type'];
						$trid=$la[$row['linkn']]['rid'];

						$ltbl=$this->p->p->t[$rtype]->tbl;
						$slugreq="";
						if(in_array("slug",$this->p->p->t[$rtype]->flds))
							$slugreq=", ct.slug as slug";

						$this->db->query("select ".$this->p->p->t[$rtype]->gO("sel_titleFld")." as name {$slugreq} from {$this->p->p->db_prefix}{$ltbl} as ct where ct.id={$trid}","{$this->db_res}_lname");
						$trr=$this->db->next("{$this->db_res}_lname");
						$tname=$this->db->req_escape($trr['name']);
						if($slugreq!="")
							$lslug=$trr['slug'];
						
					}else{
						$turl=$la[$row['linkn']]['url'];
					}


						$this->db->query("update {$this->p->p->db_prefix}sys_links set name='{$tname}', type={$ttype},url='{$turl}',
							rtbl='{$ltbl}', object='{$rtype}', rid={$trid},slug='{$lslug}',target='{$ttarg}' where id={$row['id']} ");
						unset($this->dataArr[$ln][$row['linkn']]);
					}
				}
				if(count($this->dataArr[$ln])>0)				
					$iou=true;
			}

		}

		if($act=="i" || $iou)
		{
			foreach($this->dataArr as $ln=>$la)
			{
				foreach($la as $lnn=>$lv){
					$tname=(isset($lv['name'])? $this->db->escape($lv['name']) : "" );
					$ttype=($lv['type']=='url'? 0 : 1);

					$turl='';
					$rtype='';
					$trid=0;
					$ltbl='';
					$lslug='';

					if($lv['type']!="url"){
						$rtype=$lv['type'];
						$trid=$lv['rid'];

						$ltbl=$this->p->p->t[$rtype]->tbl;
						$slugreq="";
						if(in_array("slug",$this->p->p->t[$rtype]->flds))
							$slugreq=", ct.slug as slug";


						$this->db->query("select ".$this->p->p->t[$rtype]->gO("sel_titleFld")." as name {$slugreq} from {$this->p->p->db_prefix}{$ltbl} as ct where ct.id={$trid}","{$this->db_res}_lname");
						$trr=$this->db->next("{$this->db_res}_lname");
						$tname=$this->db->req_escape($trr['name']);
						if($slugreq!="")
							$lslug=$trr['slug'];
					}else{
						$turl=$lv['url'];
					}


					$this->db->query("insert into {$this->p->p->db_prefix}sys_links (tbl,tid,name,type,url,object,rtbl,rid,slug,link,linkn,target,lang,lid)
						values('{$this->tbl}',$id,'{$tname}',$ttype,'{$turl}','$rtype','{$ltbl}',$trid,'{$lslug}','{$ln}',$lnn,'{$lv['target']}','{$this->p->def_lang}',0)");
				}
			}

		}
		if(is_array($cdel)){
			foreach($cdel as $ln=>$lv){
				$this->db->query($dq="delete from {$this->p->p->db_prefix}sys_links where tbl='{$this->tbl}' and link='{$ln}' and linkn in (".implode(",",$lv).")");
				rsort($lv);
				foreach($lv as $la)
					$this->db->query("update {$this->p->p->db_prefix}sys_links set linkn=linkn-1 where tbl='{$this->tbl}' and link='{$ln}' and linkn>{$la} order by linkn asc");
			}
		}

/*		var_dump($dq,"<hr>",$cdel);die("<hr>".$this->db->getLastError());
		die();
 */
	

	}


	function iv($ia,$ln,$lnn,$f)
	{
		if(is_array($ia) && isset($ia[$ln][$lnn][$f]))
			return $ia[$ln][$lnn][$f];
		else
			return "";

	}

	function listCur($ln,$on='id')
	{
		$ret=array();
		$this->db->query($q="select ct.* from {$this->p->p->db_prefix}sys_links ct where ct.tbl='{$this->tbl}' and (ct.tid={$this->p->cD[$on]}) and ct.link='$ln' and ((ct.type=0 and ct.url!='') or (ct.type!=0 and ct.rtbl!='' and ct.rid!=0 ) )","{$this->db_res}sys_links_cur_list");
		if($this->db->numRows("{$this->db_res}sys_links_cur_list")==0)
			return false;

		while($row=$this->db->next("{$this->db_res}sys_links_cur_list"))
			$ret[$row['linkn']]=$row;

			return $ret;
	}

	static function listLinks($cobj,$ln,$tbl,$on='id',$irow=false)
	{
		$ret=array();
		if($irow==false)
			$srow=$cobj->cD;
		else
			$srow=$irow;
		$cobj->db->query($q="select ct.* from {$cobj->p->db_prefix}sys_links ct left join $tbl l on (l.{$on}='{$irow[$on]}' and l.id=ct.tid) where ct.tbl='{$tbl}' and ct.link='$ln' and ((ct.type=0 and ct.url!='') or (ct.type!=0 and ct.rtbl!='' and ct.rid!=0 ) )","sys_links_static_list_{$tbl}");
		if($cobj->db->numRows("sys_links_static_list_{$tbl}")==0)
			return false;

		while($row=$cobj->db->next("sys_links_static_list_{$tbl}"))
			$ret[$row['linkn']]=$row;

			return $ret;
	}

	function linkUrl($ln,$num,&$lrow,$on='id')
	{
		global $sys_links_url;
		$links=$this->listCur($ln,$on);
		$lrow=$links[$num];
		if($links!=false && is_array($links)){
			if($links[$num]['type']=="0")
				return $links[$num]['url'];
			if($this->uslug && $links[$num]['slug']!="")
				return url("/{$links[$num]['object']}/{$links[$num]['slug']}");

			return "{$sys_links_url}?a=dbo_{$links[$num]['object']}&rid={$links[$num]['rid']}";
		}
		else
			return false;

	}

	static function s_linkUrl($cobj,$tbl,$ln,$num,&$lrow,$on='id',$irow)
	{
		global $sys_links_url;
		$links=sys_links::listLinks($cobj,$ln,$tbl,$on,$irow);
		$lrow=$links[$num];

		if($links!=false && is_array($links)){
			if($links[$num]['type']=="0")
				return $links[$num]['url'];
			if($this->uslug && $links[$num]['slug']!="")
				return url("/{$links[$num]['object']}/{$links[$num]['slug']}");
			return "{$sys_links_url}?a=dbo_{$links[$num]['object']}&rid={$links[$num]['rid']}";
		}else 
			return false;

	}

	function linkUrls($ln,$on='id')
	{
		global $sys_links_url;
		$links=$this->listCur($ln,$on);
		$rtbl=$links[$num]['rtbl'];
		$ret=array();
		if(is_array($links) && count($links)>0){
			foreach($links as $k=>$v){
				$ret[$k]['r']=$links[$k];
				if($links[$k]!=false && is_array($links[$k])){
					if($links[$k]['type']=="0")
						$ret[$k]['l']=$links[$k]['url'];
					else if($this->uslug && $links[$k]['slug']!="")
						$ret[$k]['l']=url("/{$links[$k]['object']}/{$links[$k]['slug']}");
					else
						$ret[$k]['l']="{$sys_links_url}?a=dbo_{$links[$k]['object']}&rid={$links[$k]['rid']}";
				}else
					$ret[$k]['l']=false;
			}
		}
		return (count($ret)>0?$ret:false);
	}

	static function s_linkUrls($cobj,$tbl,$ln,$on='id',$irow)
	{
		global $sys_links_url;
		$links=sys_links::listLinks($cobj,$ln,$tbl,$on,$irow);
		$rtbl=$links[$num]['rtbl'];
		$ret=array();
		foreach($links as $k=>$v){
			$ret[$k]['r']=$links[$k];
			if($links[$k]!=false && is_array($links[$k])){
				if($links[$k]['type']=="0")
					$ret[$k]['l']=$links[$k]['url'];
				else if($this->uslug && $links[$k]['slug']!="")
					$ret[$k]['l']=url("/{$links[$k]['object']}/{$links[$k]['slug']}");
				else
					$ret[$k]['l']="{$sys_links_url}?a=dbo_{$links[$k]['object']}&rid={$links[$k]['rid']}";
			}else
				$ret[$k]['l']=false;
		}
		return (count($ret)>0?$ret:false);
	}

}

?>
