<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
ini_set('max_execution_time', '300');
//ini_set('memory_limit', '1024M');
//$_GET['token']="9956ED4CA506F4A28DB501BFB5E7F3F637E00F0D855B7D5A8D0E046EE20E80CB34355CC9FA88A3BF44D835A08275264846F9768FFF3D7BF1AC93F4CF1E2BF645";
set_time_limit(300);
$_GET = filter_input_array(INPUT_GET);

if(!isset($_GET['token']))
 {
    exit();
 }
 if(isset($_GET['xlsx']) && intval($_GET['xlsx'])==1)
 {
	 $xls = 1;
 }else{
	 $xls = 0;
 }	 
 $token = filter_var(trim($_GET['token']),513);
 $sql="select aes_decrypt(unhex('$token'),sha1('cont@fact')) as mval";
 $mval='';
 $lnk = mysqli_connect("localhost","root","cx4zeeg4","powerbi");
 $rs = mysqli_query($lnk,$sql);
 if($rs)
 {
    while($row=mysqli_fetch_assoc($rs))
    {
       $mval = $row['mval'];
    }
 }
 $res = json_decode($mval,true);
 if(!$res)
 {
    exit();
 }
 include_once("../../Classes/xlsxwriter.class.php");
 if($xls==1)
 {
	$writer = new XLSXWriter();
	$filename = "informe.xlsx";
    header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset: utf-8");
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');   
}
 $sql="show tables like 'table_%'";
 $dbase = "pbi_".strtolower($res['nif']);
 $filename="/home/$dbase.txt";
 if(is_file($filename)) unlink($filename);
 $istable = 0;
 $tb = "";
 $sql="select nom,ct,mkey from powerbi.config where nif='".$res['nif']."'";

 $rs = mysqli_query($lnk,$sql);
 $k1=0;
 while($row=mysqli_fetch_assoc($rs))
 {
	 if($xls == 0)
	 {
		 if($k1==0)
		 {
          $line= "<table id='config' name='config' caption='config' border='1px'>";
          file_put_contents("/home/$dbase.txt",$line.PHP_EOL,FILE_APPEND);				 
          $line ="<tr><td>Nom</td><td>Centre</td><td>Codi</td></tr>";
          file_put_contents("/home/$dbase.txt",$line.PHP_EOL,FILE_APPEND);				 
		 }
         $line ="<tr><td>".$row['nom']."</td><td>".$row['ct']."</td><td>".$row['mkey']."</td></tr>";
         file_put_contents("/home/$dbase.txt",$line.PHP_EOL,FILE_APPEND);				 		 
	 }else{
		 if($k1==0)
		 {
		  $header = array('Nom','Centre','Codi');
 		  $writer->writeSheetRow('config', $header);
		 }
		 $writer->writeSheetRow('config', $row);
	 }
	 $k1++;
 }
 
 if(isset($_GET['table']) && $_GET['table']!="")
 {
	 $tb = $_GET['table'];
	 $istable = 1;
	 $sql="SELECT table_name FROM information_schema.tables WHERE table_name='$tb' AND table_schema='$dbase' ORDER BY 0+replace(replace(table_name,'table_',''),'_','.')";
 }else{
$sql="SELECT table_name FROM information_schema.tables WHERE table_name like 'table_%' AND table_schema='$dbase' ORDER BY 0+replace(replace(table_name,'table_',''),'_','.')";

 }	 
 
  $query = mysqli_query($lnk, $sql);
 $tbl = 0;
 while ($row = mysqli_fetch_array($query)) {
    $table = $row[0];
    if($table!="table_0")
    {
	  $tbl = intval(str_replace("_","",str_replace("table_","",$table)));
      $retval=array();
	  switch($tbl)
	  {
		  case 6:
			  $sql = "select `index`,`centre`,`any`,`setmana`,`Codigo`,concat(`Codigo`,' ',`Descripcion`) as Descripcion,`Unidades`,`Kilos`,`Pre.s/IVA`,`Imp.s/IVA`,`Imp.c/IVA`,`Tiquets` from $dbase.$table";
			  break;
		  case 7:
			  $sql = "select `index`,`centre`,`any`,`setmana`,`Codigo`,concat(`Codigo`,' ',`Descripcion`) as Descripcion,`Unidades`,`Kilos`,`Pre.s/IVA`,`Imp.s/IVA`,`Imp.c/IVA`,`Tiquets` from $dbase.$table";
			  break;
		  case 8:
			  $sql = "select `index`,`centre`,`any`,`setmana`,`Codigo`,concat(`Codigo`,' ',`Descripcion`) as Descripcion,`Unidades`,`Kilos`,`Pre.s/IVA`,`Imp.s/IVA`,`Imp.c/IVA`,`Tiquets` from $dbase.$table";
			  break;
		  case 9:
			  $sql = "select `index`,`centre`,`any`,`setmana`,`Codigo`,concat(`Codigo`,' ',`Descripcion`) as Descripcion,`Unidades`,`Kilos`,`Pre.s/IVA`,`Imp.s/IVA`,`Imp.c/IVA`,`Tiquets` from $dbase.$table";
			  break;
		  case 10:
			  $sql = "select `index`,`centre`,`any`,`setmana`,`Codigo`,concat(`Codigo`,' ',`Descripcion`) as Descripcion,`Unidades`,`Pre.s/IVA`,`Imp.s/IVA`,`Imp.c/IVA`,`Tiquets`,`Familia` from $dbase.$table";
			  break;
		  case 11:
			  $sql = "select `index`,`centre`,`any`,`setmana`,`Codigo`,concat(`Codigo`,' ',`Descripcion`) as Descripcion,`Unidades`,`Pre.s/IVA`,`Imp.s/IVA`,`Imp.c/IVA`,`Tiquets`,`Familia` from $dbase.$table";
			  break;
		  default:
			  $sql="select * from $dbase.$table";
			  break;
	  }
      $rs1 = mysqli_query($lnk,$sql);
      $fields = mysqli_fetch_fields($rs1);
	  $header = array();	
	  for($i=0;$i<count($fields);$i++)
	  {
		  array_push($header , iconv('Windows-1252', 'UTF-8',$fields[$i]->name));
	  }
		
	  if($xls == 0)
	  {
          $line= "<table id='table_$tbl' name='table_$tbl' caption='$table' border='1px'>";
          file_put_contents("/home/$dbase.txt",$line.PHP_EOL,FILE_APPEND);				 
		  //echo "<table id='table_$tbl' name='table_$tbl' caption='$table' border='1px'>";
		  //echo "<tr>";
          $line ="<tr>";
		  for($i=0;$i<count($fields);$i++)
		  {
		   //echo "<td>".utf8_encode($fields[$i]->name)."</td>";
             $line .="<td>".utf8_encode($fields[$i]->name)."</td>";
          }   
		  //echo "</tr>";
          $line .="</tr>";
          file_put_contents("/home/$dbase.txt",$line.PHP_EOL,FILE_APPEND);				 
		  while($row=mysqli_fetch_array($rs1,MYSQLI_NUM))
		  {
			 //echo "<tr>";
             $line ="<tr>";
			 for($c=0;$c<count($row);$c++)
			 {
				if(intval($fields[$c]->type)==5)
				{
                 $row[$c]=number_format($row[$c],2,",","");
				 //echo "<td>".number_format($row[$c],2,",","")."</td>"; 
                 $line .="<td>".number_format(floatval($row[$c]),2,",","")."</td>";
				}else{
				 //echo "<td>".($row[$c])."</td>"; 
                 $line .="<td>".$row[$c]."</td>";
				}	
			 }
			 //echo "</tr>";
             $line .="</tr>";
             file_put_contents("/home/$dbase.txt",$line.PHP_EOL,FILE_APPEND);				 
            //$json[] = $row;
		  }
		  //echo "</table>";
          $line .="</table>";
          file_put_contents("/home/$dbase.txt",$line.PHP_EOL,FILE_APPEND);				 
          //$retval[$table]=$json; 
	  }else{
		  $sheet = $table;
		  $writer->writeSheetRow($sheet, $header);
		  while($row=mysqli_fetch_array($rs1,MYSQLI_NUM))
		  {
			  $rd = array();
			 for($c=0;$c<count($row);$c++)
			 {
				if(intval($fields[$c]->type)==5)
				{
					array_push($rd,number_format($row[$c],2,",","")); 
				}else{
					$row[$c] = iconv('Windows-1252', 'UTF-8', ($row[$c]));
					array_push($rd,$row[$c]); 
				}	
			 }
			 $writer->writeSheetRow($sheet, $rd);
		  }
	  }  
    }
    $tbl++;
    //echo json_encode($retval);
    //unset($retval);
 }
 //echo json_encode($retval);
 //echo @readfile($filename); 
 // devoluciones
 if(isset($_GET['table']) && $_GET['table']!="")
 {
	 $tb = $_GET['table'];
	 $istable = 1;
	 $sql="SELECT table_name FROM information_schema.tables WHERE table_name ='$tb' AND table_schema='$dbase' ORDER BY 0+replace(replace(table_name,'dev_table_',''),'_','.')";
 }else{
 $sql="SELECT table_name FROM information_schema.tables WHERE table_name like 'dev_table_%' AND table_schema='$dbase' ORDER BY 0+replace(replace(table_name,'dev_table_',''),'_','.')";
 }	 
 $query = mysqli_query($lnk, $sql);
 $tbl = 0;
 while ($row = mysqli_fetch_array($query)) {
    if($tbl>0)
    {
      $table = $row[0];
      $sql="select * from $dbase.$table";
      $rs1 = mysqli_query($lnk,$sql);
      $fields = mysqli_fetch_fields($rs1);
	  $header = array();	
	  for($i=0;$i<count($fields);$i++)
	  {
		  array_push($header , iconv('Windows-1252', 'UTF-8',$fields[$i]->name));
	  }				 
	  if($xls == 0)
	  {
		  $line ="<table id='dev_table_$tbl' name='dev_table_$tbl' caption='$table' border='1px'>";
          file_put_contents("/home/$dbase.txt",$line.PHP_EOL,FILE_APPEND);
		  $line = "<tr>";
		  for($i=0;$i<count($fields);$i++)
		  {
			 $line.= "<td>".utf8_encode($fields[$i]->name)."</td>";
		  }
		  $line .="</tr>";
          file_put_contents("/home/$dbase.txt",$line.PHP_EOL,FILE_APPEND);  
		  while($row=mysqli_fetch_array($rs1,MYSQLI_NUM))
		  {
			 $line="<tr>";
			 for($c=0;$c<count($row);$c++)
			 {
				if(intval($fields[$c]->type)==5)
					{
					$line.= "<td>".number_format($row[$c],2,",","")."</td>"; 
				}else{
					$line.= "<td>".($row[$c])."</td>"; 
				}	
			 }
			 $line.="</tr>";
             file_put_contents("/home/$dbase.txt",$line.PHP_EOL,FILE_APPEND);
		  }
		  $line= "</table>";
          file_put_contents("/home/$dbase.txt",$line.PHP_EOL,FILE_APPEND);
	  }else{
		  $sheet = $table;
		  $writer->writeSheetRow($sheet, $header);
		  while($row=mysqli_fetch_array($rs1,MYSQLI_NUM))
		  {
			  $rd = array();
			 for($c=0;$c<count($row);$c++)
			 {
				if(intval($fields[$c]->type)==5)
				{
					array_push($rd,number_format($row[$c],2,",","")); 
				}else{
					$row[$c] = iconv('Windows-1252', 'UTF-8', ($row[$c]));
					array_push($rd,$row[$c]); 
				}	
			 }
			 $writer->writeSheetRow($sheet, $rd);
		  }
	  }  
    }
    $tbl++;
 }
 
// diaris compta
 if($istable == 0 || $tb=="diario")
 {

 $an = date('Y');
 $a1=intval($an)-1; 
 $a2=intval($an)-2;
 $sql="select '$a2' as ejercicio,date_format(fecha,'%d/%m/%Y') as fecha,nc as asiento,subcta,contra,valdebe as debe,valhaber as haber from ".$res['nif'].".diario$a2 where (tip<>'Z' and tip<>'R') or (tip='Z' and subcta like '129%') union all select '$a1' as ejercicio,date_format(fecha,'%d/%m/%Y') as fecha,nc as asiento,subcta,contra,valdebe as debe,valhaber as haber from ".$res['nif'].".diario$a1 where (tip<>'Z' and tip<>'R') or (tip='Z' and subcta like '129%') union all select '$an' as ejercicio,date_format(fecha,'%d/%m/%Y') as fecha,nc as asiento,subcta,contra,valdebe as debe,valhaber as haber from ".$res['nif'].".diario$an where (tip<>'Z' and tip<>'R') or (tip='Z' and subcta like '129%')";
 $query = mysqli_query($lnk, $sql);
 if($xls == 0)
 {
 echo "<table id='table_diari' name='table_diari' caption='Diario' border='1px'>";
 echo "<tr><td>Ejercicio</td><td>Fecha</td><td>Asiento</td><td>Subcta</td><td>Contra</td><td>Debe</td><td>Haber</td></tr>";
 while($row=mysqli_fetch_assoc($query))
 {
 echo "<tr><td>".$row['ejercicio']."</td><td>".$row['fecha']."</td><td>".$row['asiento']."</td><td>".$row['subcta']."</td><td>".$row['contra']."</td><td>".$row['debe']."</td><td>".$row['haber']."</td></tr>";
 }	 
 echo "</table>";
 }else{
	 $sheet = "diario";
	 $header = array("Ejercicio","Fecha","Asiento","Partida","Contrapartida","Debe","Haber");
	 	
		  $writer->writeSheetRow($sheet, $header);
		  while($row=mysqli_fetch_array($query,MYSQLI_NUM))
		  {
			  $rd = array();
			 for($c=0;$c<count($row);$c++)
			 {
				if(intval($fields[$c]->type)==5)
				{
					array_push($rd,number_format($row[$c],2,",","")); 
				}else{
					$row[$c] = iconv('Windows-1252', 'UTF-8', ($row[$c]));
					array_push($rd,$row[$c]); 
				}	
			 }
			 $writer->writeSheetRow($sheet, $rd);
		  }

 }
 }	 

 if($xls == 1)
 {
	$writer->writeToStdOut(); 
 }
 mysqli_close($lnk);
 $handle=fopen($filename,'r');
 if($handle)
 {
  while(!feof($handle))
   echo fgets($handle);
 }
 fclose($handle);
 exit();
?>