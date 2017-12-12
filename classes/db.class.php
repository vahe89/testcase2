<?php
if(!function_exists('hex2bin')){
    function hex2bin($data)
    {
        $bin    = "";
        $i      = 0;
        do {
            $bin    .= chr(hexdec($data{$i}.$data{($i + 1)}));
            $i      += 2;
        } while ($i < strlen($data));

        return $bin;
    }
}


class db
{
public $db_prefix="";

private $link;
private $cursor;
private $last_errno;
private $last_errno_arr;
private $last_error;
private $last_error_arr;
private $last_error_q;
private $last_error_q_arr;
private $last_dbg_q;
private $init_vals;

public $adb_hist;
public $adb_tbl_hist;
public $adb_rdt;
public $adb_ndt;

  function __construct($host="",$login="",$pass="",$db="",$charset="utf8",$db_pref="")
  {
		$this->link=false;
		$this->adb_hist=array();
		$this->adb_tbl_hist=array();
		$this->adb_rdt=array();
		$this->adb_ndt=array();

		$this->cursor=array("def"=>false);
		$this->last_error_arr=array();
		$this->last_errno_arr=array();
		$this->last_error_q_arr=array();
		$this->init_vals=array("host"=>$host,"login"=>$login,"pass"=>$pass,"db"=>$db,"charset"=>$charset,"db_pref"=>$db_pref);
    if($host!="")
      return $this->connect($host,$login,$pass,$db,$charset,$db_pref);
    
  }

  function __destruct()
  {
    if($this->link!==false)
      mysql_close($this->link);
  }
  
  function getLastError($res=false)
	{
		$te=$this->last_error;
		$tq=$this->last_error_q;
		if($res!=false && isset($this->last_error_arr[$res]))
		{
		$te=$this->last_error_arr[$res];
		$tq=$this->last_error_q_arr[$res];
		}

   return ($te!=false?$te." '".$tq."'":"");
  }

	function getLastErrno($res=false)
	{
		if($res!=false && isset($this->last_errno_arr[$res]))
		 return $this->last_errno_arr[$res];

   return $this->last_errno;
  }

	function getLastQuery()
  {
   return $this->last_dbg_q;
  }

	function getErrors(){
		$errs=array();
		foreach($this->last_errno_arr as $k=>$v)
			$errs[$k]="Res:'$k',ErrNo:{$this->last_errno_arr[$k]},Err:'{$this->last_error_arr[$k]}',Q:'{$this->last_error_q_arr[$k]}'";

			return $errs;
	}
  
  function connect($host,$login,$pass,$db,$charset="utf8",$db_pref="")
  {
    $this->link=mysql_connect($host,$login,$pass);
    if($this->link===false){
      $this->last_error=mysql_error();
      return false;
    }
		
		mysql_query("SET NAMES '$charset'",$this->link);
		mysql_query("SET CHARACTER SET '$charset'",$this->link);
		mysql_query("SET character_set_client = '$charset'",$this->link);
    mysql_query("SET character_set_connection = '$charset'",$this->link);
    mysql_query("SET character_set_database = '$charset'",$this->link);
    mysql_query("SET character_set_results = '$charset'",$this->link);
    mysql_query("SET character_set_server = '$charset'",$this->link);
    mysql_query("SET character_set_system = '$charset'",$this->link);
		
		$r=mysql_select_db($db,$this->link);
    if($r===false){
      $this->last_error=mysql_error();
      return false;
    }
		$this->db_prefix=$db_pref;
    return true;
	}

	function reset_db(){
		return $this->change_db($this->init_vals['db']);
	}
	function change_db($db){
		if($this->link==false){
			$this->last_error="Link to db invalid!";
      return false;
		}
		$r=mysql_select_db($db,$this->link);
    if($r===false){
      $this->last_error=mysql_error();
      return false;
		}
	return true;
	
	}

	function getVar($query_res, $newdb = false)
	{
		$res=false;
		if (isset($this->cursor[$query_res]))
			$res = $query_res;
		if($res===false){
			$res="_sys_var_q".time();
			$this->query($query_res, $res, $newdb);
		}
		$rr=$this->next($res);
		if(is_array($rr) && count($rr)>0)
			return reset($rr);
		return false;
	}

	function getCol($col, $query_res = false, $newdb = false)
	{
		$res=false;
		if (isset($this->cursor[$query_res]))
			$res = $query_res;
		if($res===false){
			$res = "_sys_var_q" . time();
			$this->query($query_res, $res, $newdb);
		}
		$rr = $this->next($res);
		if (is_array($rr) && isset($rr[$col]))
			return $rr[$col];
		return false;
	}

	function getRow($query_res, $newdb = false)
	{
		$res = false;
		if (isset($this->cursor[$query_res]))
			$res = $query_res;
		if ($res === false) {
			$res="_sys_row_q".time();
			$this->query($query_res, $res, $newdb);
		}
		$rr=$this->next($res);
		if(is_array($rr) && count($rr)>0)
			return $rr;
		return false;
	}
  
  function query($query,$res="def",$newdb=false)
  {
	  if($this->link===false)
      return false;
		if($newdb!=false)
			$this->change_db($newdb);

		$this->last_dbg_q=$query;
    $this->cursor[$res]=mysql_query($query,$this->link);
		if($newdb!=false)
			$this->reset_db();

    if($this->cursor[$res]===false){
      $this->last_error_arr[$res]=$this->last_error=mysql_error();
			$this->last_error_q_arr[$res]=$this->last_error_q=$query;
      $this->last_errno_arr[$res]=$this->last_errno=mysql_errno();
			
      return false;
    }
    else
      return true;
  }
	function qnext($q,$res){
		if(!isset($this->cursor[$res]) || $this->cursor[$res]===false)
			$this->query($q,$res);
		return $this->next($res);

	}

  function next($res="def")
  {
    if($this->link===false || $this->cursor[$res]===false)
      return false;
    $r=mysql_fetch_assoc($this->cursor[$res]);
    if($r===false)
      $this->cursor[$res]=false;
    
    
    return $r;
    
  }
  
  function close()
  {
    if($this->link!==false)
      return mysql_close($this->link);
    else
      return false;

  }
  
  function numRows($res="def")
  {
    if($this->link===false)
      return false;
    
    if($this->cursor[$res]===true)
      return mysql_affected_rows($this->link);
    else if($this->cursor[$res]!==false)
      return mysql_num_rows($this->cursor[$res]);
    else
      return false;
  
  }
  
  function lastInsertId()
  {
    if($this->link===false)
      return false;
      
    return mysql_insert_id($this->link);
  
  }
  
  function escape($str,$force=false)
	{
		if($this->link===false)
			return false;
		if(ini_get("magic_quotes_gpc")==0 || $force==true){
			if(is_array($str)){
				foreach($str as $k=>$v)
					$str[$k]=$this->escape($v);
				return $str;
			}else
				return mysql_real_escape_string($str,$this->link);
		}
		else
			return $str;
	}

	function escape_force($str) {
		return $this->escape($str,true);
	}
	function req_escape($str) 
	{
		if($this->link===false)
			return false;

		if(is_array($str)){
			foreach($str as $k=>$v)
				$str[$k]=$this->req_escape($v);
			return $str;
		}else
			return mysql_real_escape_string($str,$this->link);
	}

	function oact($act,$o,$data){
		$ret=array('pid'=>false,"msg"=>false,"rdata"=>false);
		$ret['pid']=$this->act($act,$o->tbl,$data,$ret['msg'],$ret['rdata'],$o);
		return $ret;
	}

	function act($act,$table,$data,&$msg,&$rdata,$o=false)
	{

		$ins=array('f'=>array(),'v'=>array());
		$upd=array();
		$where="";
		$ret="";
		$rdata=array();
		$rawdata=array();
		
		$d="";

		foreach($data as $n=>$v)
		{
			$uv=$v;
			if(is_array($v))
				$v=serialize($v);
			if(ini_get("magic_quotes_gpc")==0)
			$v=$this->escape($v);
			$x=explode(".",$n);
			if(count($x)>1 && $x[0]=="r" && $v===""){
				$msg="Field {$x[1]} is required!";
				return false;
			}
			else if(count($x)>1 && ($x[0]=='c' || $x[0]=='r')){
/*			$ins['f'][]=$x[1];
			$ins['v'][]="'$v'";
$upd[]="{$x[1]}='$v'";*/
			$rawdata[$x[1]]=$uv;
			}
			else if(count($x)>1 && ($x[0]=='w' || $x[0]=='n' || $x[0]=='nl' || $x[0]=='l' || $x[0]=='o' || $x[0]=='ret')){
				$rd="and";
				if($x[0]=='ret' && count($x)>2){
					array_shift($x);
					$ret=$v;
				}
				if($x[0]=='o' && count($x)>2){
					array_shift($x);
					$d===""?$rd="or":$d="or";
				}
				$sl="";
				switch($x[0])
				{
				case "w":
					$sl="{$x[1]}='$v'";
					break;
				case "n":
					$sl="{$x[1]}!='$v'";
					break;
				case "l":
					$sl="{$x[1]} like '$v'";
					break;
				case "nl":
					$sl="{$x[1]} not like '$v'";
					break;
				}
				$where.="$d $sl ";
				$d=$rd;
			}else if(count($x)==1){
				$rawdata[$x[0]]=$uv;
			}
		}
		$ins['f']=implode(",",$ins['f']);
		$ins['v']=implode(",",$ins['v']);
		$upd=implode(",",$upd);

	
	switch($act)
	{
	case "i":
		if(is_object($o)){
			$rawdata=$o->run_triggers('pre','i',false,array('none'=>$rawdata));
			$rawdata=$rawdata['none'];
		}
		$ins['f']=implode(",",array_keys($rawdata));
		$ins['v']="'".implode("','",$rawdata)."'";

			$this->query("insert into {$this->db_prefix}$table ({$ins['f']}) values({$ins['v']})");
			$msg=$this->getLastError();
			$insId=$this->lastInsertId();
			$rawdata['id']=$insId;
			$rdata[]=$rawdata;
			$this->adb_hist[]=array('id'=>$insId,'a'=>$act,'t'=>$table);
			$this->adb_ndt[$insId]=$rawdata;
			$tbl=$table;
			if(is_object($o))
				$tbl=$o->oname;

			if(!isset($this->adb_tbl_hist[$tbl]))
				$this->adb_tbl_hist[$tbl]=array();
			if(!isset($this->adb_tbl_hist[$tbl][$act]))
				$this->adb_tbl_hist[$tbl][$act]=array();
			$this->adb_tbl_hist[$tbl][$act][]=array('id'=>$insId,'a'=>$act,'t'=>$table);
	
			if(is_object($o)){
				$o->run_triggers('post','i',false,$this->adb_ndt);
		}
			
			return $insId;
			break;
		case "d";
			if($where!="" || $where=="ALL_RECORDS"){
				$this->query("select * from {$this->db_prefix}$table where $where",'db_class_own_predelete_query');
	
				$tbl=$table;
				if(is_object($o))
					$tbl=$o->oname;
				if(!isset($this->adb_tbl_hist[$tbl]))
					$this->adb_tbl_hist[$tbl]=array();
				if(!isset($this->adb_tbl_hist[$tbl][$act]))
					$this->adb_tbl_hist[$tbl][$act]=array();

				while($row=$this->next('db_class_own_predelete_query')){
					if(isset($row['id'])){
						$this->adb_hist[]=array('id'=>$row['id'],'a'=>$act,'t'=>$table);
						$this->adb_rdt[$row['id']]=$row;
						$this->adb_tbl_hist[$tbl][$act][]=array('id'=>$row['id'],'a'=>$act,'t'=>$table);
					}
					$rdata[]=$row;
				}
		if(is_object($o)){
			$o->run_triggers('pre','d',$this->adb_rdt,false);
		}

			$this->query("delete from {$this->db_prefix}$table where $where");
			$msg=$this->getLastError();
		if(is_object($o)){
			$o->run_triggers('post','d',$this->adb_rdt,false);
		}
			return $ret===""?true:$ret;
			}
			else{
				$msg="Invalid 'where'!";
					return false;
			}
			break;
		case "u";
			if($where!="" || $where=="ALL_RECORDS"){
				$tbl=$table;
				if(is_object($o))
					$tbl=$o->oname;
				if(!isset($this->adb_tbl_hist[$tbl]))
					$this->adb_tbl_hist[$tbl]=array();
				if(!isset($this->adb_tbl_hist[$tbl][$act]))
					$this->adb_tbl_hist[$tbl][$act]=array();

				$this->query("select * from {$this->db_prefix}$table where $where",'db_class_own_predelete_query');
				while($row=$this->next('db_class_own_predelete_query')){
					if(isset($row['id'])){
						$this->adb_hist[]=array('id'=>$row['id'],'a'=>$act,'t'=>$table);
						$this->adb_rdt[$row['id']]=$row;
						$this->adb_tbl_hist[$tbl][$act][]=array('id'=>$row['id'],'a'=>$act,'t'=>$table);
					}
					$rdata[]=$row;
				}

		if(is_object($o)){
			$rawdata=$o->run_triggers('pre','u',$this->adb_rdt,array('none'=>$rawdata));
			$rawdata=$rawdata['none'];
		}
				$upd=array();
				foreach($rawdata as $dk=>$dv)
					$upd[]="$dk='$dv'";
				$upd=implode(",",$upd);
				$this->query("update {$this->db_prefix}$table set $upd where $where");
	
				foreach($rdata as $rv)
					$this->adb_ndt[$rv['id']]=array_merge($rv,$rawdata);

			$msg=$this->getLastError();
				if(is_object($o)){
					$rawdata=$o->run_triggers('post','u',$this->adb_rdt,$this->adb_ndt);
				}
			return $ret===""?true:$ret;
			}
			else{
				$msg="Invalid 'where'!";
					return false;
			}
			break;
		$msg='Invalid action!';
		return false;
	}

	}

}
?>
