<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;

use App\reportes;

use App\Util;

class ReportesController extends Controller
{
    //
    public function reportes_edad(Request $request){

    	$datos=json_decode($request->get("datos"));




    	return response()->json();

    }

    public function reportes_lista_general(Request $request){

         $datos=json_decode($request->get("datos"));       
    	
    	//var_dump($par);
        $reportes=new reportes();
        $r=$reportes->reporte_lista($datos);
    	return response()->json(array("mensaje"=>"","datos"=>$r,"respuesta"=>true));
    }

    public function reportes_por_id($id){


    	

    	$par=DB::table("participantes")
    		->join("detalle_participantes","participantes.documento","=","detalle_participantes.user_id")
            ->join("eventos","detalle_participantes.event_id","=","eventos.id")
    		->where("participantes.documento","LIKE",$id)
    		->select("eventos.name","eventos.date","eventos.city","detalle_participantes.updated_at",DB::RAW("CONCAT(pri_nombre,' ',seg_nombre,' ',pri_apellido,' ',seg_apellido) as nombre"))
    		->groupBy("eventos.id")
            ->orderBy("detalle_participantes.updated_at","DESC")
    		->get();
             
    	//var_dump($par);

    	return response()->json(array("mensaje"=>":)","datos"=>$par,"respuesta"=>true));
    }	

   

     public function reporte_general(Request $request){
        $ssql="";
        $datos=json_decode($request->get("datos")); 
        $datos_filtro="participantes.id,";  
        $datos_filtro_doc="eventos.id,eventos.name,";  
        $datos_filtro_nom="eventos.id,eventos.name,";  
        //var_dump($datos->datos->datos->datos_filtro[0]);
        
        if($datos->datos->datos->datos_filtro[0]=="todos_los_datos"){
            $datos_filtro="participantes.id,participantes.tipo_doc,participantes.documento,participantes.pri_nombre,participantes.seg_nombre,participantes.pri_apellido,participantes.seg_apellido,participantes.edad,participantes.genero,participantes.escolaridad,participantes.zona,participantes.dep_nacimiento,participantes.ciud_nacimiento,participantes.municipio,participantes.etnia,participantes.cap_dife,participantes.departamento_ubi,participantes.vereda_ubi,participantes.edad,participantes.anio_ingreso_pdp,participantes.celular,participantes.email,participantes.titulo_obt,participantes.cargo_poblador,detalle_participantes.acepta_terminos,detalle_participantes.acepta_terminos_foto";  

            $datos_filtro_doc="eventos.name,participantes.id,participantes.id,participantes.tipo_doc,participantes.documento,participantes.pri_nombre,participantes.seg_nombre,participantes.pri_apellido,participantes.seg_apellido,participantes.edad,participantes.genero,participantes.escolaridad,participantes.zona,participantes.dep_nacimiento,participantes.ciud_nacimiento,participantes.municipio,detalle_participantes.updated_at,participantes.cap_dife,participantes.etnia,participantes.departamento_ubi,participantes.municipio,participantes.vereda_ubi,participantes.edad,participantes.anio_ingreso_pdp,participantes.celular,participantes.email,participantes.titulo_obt,participantes.cargo_poblador,detalle_participantes.acepta_terminos,detalle_participantes.acepta_terminos_foto";  

            $datos_filtro_nom="eventos.id,eventos.name,participantes.id,participantes.id,participantes.tipo_doc,participantes.documento,participantes.pri_nombre,participantes.seg_nombre,participantes.pri_apellido,participantes.seg_apellido,participantes.edad,participantes.genero,participantes.escolaridad,participantes.zona,participantes.dep_nacimiento,participantes.ciud_nacimiento,participantes.municipio,participantes.vereda_ubi,detalle_participantes.updated_at,participantes.cap_dife,participantes.etnia,participantes.departamento_ubi,participantes.edad,participantes.anio_ingreso_pdp,participantes.celular,participantes.email,participantes.titulo_obt,participantes.cargo_poblador,detalle_participantes.acepta_terminos,detalle_participantes.acepta_terminos_foto";  

        }else{
            $c=count($datos->datos->datos->datos_filtro)-1;

            foreach ($datos->datos->datos->datos_filtro as $key => $value) {
                
                if($value!="" && $key < $c){
                    
                    $datos_filtro.=$value.",";
                    $datos_filtro_doc.=$value.",";
                    $datos_filtro_nom.=$value.",";
                }elseif ($c == $key) {
                    $datos_filtro.=$value.",detalle_participantes.acepta_terminos,detalle_participantes.acepta_terminos_foto";
                    $datos_filtro_doc.=$value.",detalle_participantes.acepta_terminos,detalle_participantes.acepta_terminos_foto";
                    $datos_filtro_nom.=$value.",detalle_participantes.acepta_terminos,detalle_participantes.acepta_terminos_foto";
                    break;
                }
            }


            

        }

        //var_dump($datos_filtro);

        $sql_base=trim("
                    SELECT ".$datos_filtro." FROM participantes 
                        INNER JOIN detalle_participantes ON detalle_participantes.user_id = participantes.documento 
                        INNER JOIN detalle_procesos ON detalle_procesos.id_usuario = participantes.documento
                        INNER JOIN proceso ON proceso.id = detalle_procesos.id_proceso
                        INNER JOIN lineas ON lineas.id = proceso.fk_id_linea
                        INNER JOIN eventos ON detalle_participantes.event_id = eventos.id

                     WHERE ");
        $sql_base_id=trim("
                    SELECT participantes.id FROM participantes 
                        INNER JOIN detalle_participantes ON detalle_participantes.user_id = participantes.documento 
                        INNER JOIN detalle_procesos ON detalle_procesos.id_usuario = participantes.documento
                        INNER JOIN proceso ON proceso.id = detalle_procesos.id_proceso
                        INNER JOIN lineas ON lineas.id = proceso.fk_id_linea
                        INNER JOIN eventos ON detalle_participantes.event_id = eventos.id
                     WHERE ");
         $sql_base_doc=trim("
                    SELECT ".$datos_filtro_doc." FROM participantes 
                        INNER JOIN detalle_participantes ON detalle_participantes.user_id = participantes.documento 
                        INNER JOIN detalle_procesos ON detalle_procesos.id_usuario = participantes.documento
                        INNER JOIN proceso ON proceso.id = detalle_procesos.id_proceso
                        INNER JOIN lineas ON lineas.id = proceso.fk_id_linea
                        INNER JOIN eventos ON detalle_participantes.event_id = eventos.id
                     WHERE ");
        $sql_base_nom=trim("
            SELECT ".$datos_filtro_nom." FROM participantes 
                        INNER JOIN detalle_participantes ON detalle_participantes.user_id = participantes.documento 
                        INNER JOIN detalle_procesos ON detalle_procesos.id_usuario = participantes.documento
                        INNER JOIN proceso ON proceso.id = detalle_procesos.id_proceso
                        INNER JOIN lineas ON lineas.id = proceso.fk_id_linea
                        INNER JOIN eventos ON detalle_participantes.event_id = eventos.id
                     WHERE "

         ); 
        $sql_base_tbl_eventos_G=trim("SELECT eventos.name,eventos.date,COUNT(eventos.id) as cuantos_por_eventos,city FROM `detalle_participantes` INNER join eventos on eventos.id=detalle_participantes.event_id ");
        $sql_base_tbl_eventos=trim("SELECT eventos.name,eventos.date,COUNT(eventos.id) as cuantos_por_eventos,city FROM `detalle_participantes` INNER join eventos on eventos.id=detalle_participantes.event_id WHERE ");
        $sql_base_acepta_terminos=trim("
                     SELECT COUNT(detalle_participantes.user_id) AS cuantos_por_proceso_acepta,detalle_participantes.acepta_terminos FROM detalle_participantes WHERE detalle_participantes.user_id IN (");
         
        $sql_base_genero=trim("
                    SELECT COUNT(genero) AS cuentos_por_genero,participantes.genero FROM participantes WHERE participantes.id IN (");
        $sql_base_sub_genero=trim("
                    SELECT COUNT(sub_genero) AS cuentos_por_sub_genero,participantes.sub_genero FROM participantes WHERE participantes.id IN (");
        $sql_base_edad=trim("
                    SELECT COUNT(edad) AS cuentos_por_edad,participantes.edad FROM participantes WHERE participantes.id IN (");
        $sql_base_dep_nacimiento=trim("
                    SELECT COUNT(dep_nacimiento) AS cuantos_por_dep_nacimiento,participantes.dep_nacimiento FROM participantes WHERE participantes.id IN (");
        $sql_base_ciud_nacimiento=trim("
                    SELECT COUNT(ciud_nacimiento) AS cuantos_por_ciud_nacimiento,participantes.ciud_nacimiento FROM participantes WHERE participantes.id IN (");
        $sql_base_vereda_nacimiento=trim("
                    SELECT COUNT(vereda_nacimiento) AS cuantos_por_vereda_nacimiento,participantes.vereda_nacimiento FROM participantes WHERE participantes.id IN (");

        $sql_base_dep_ubi=trim("
                    SELECT COUNT(departamento_ubi) AS cuantos_por_departamento_ubi,participantes.departamento_ubi FROM participantes WHERE participantes.id IN (");
        $sql_base_ciud_ubi=trim("
                    SELECT COUNT(municipio) AS cuantos_por_ciud_ubi,participantes.municipio FROM participantes WHERE participantes.id IN (");
        $sql_base_vereda_ubi=trim("
                    SELECT COUNT(vereda_ubi) AS cuantos_por_vereda_ubi,participantes.vereda_ubi FROM participantes WHERE participantes.id IN (");        
        $sql_base_cap_dife=trim("
                    SELECT COUNT(cap_dife) AS cuantos_por_cap_dife,participantes.cap_dife FROM participantes WHERE participantes.id IN (");
        $sql_base_etnia=trim("
                    SELECT COUNT(etnia) AS cuantos_por_etnia,participantes.etnia FROM participantes WHERE participantes.id IN ( ");
         $sql_base_sub_etnia=trim("
                    SELECT COUNT(sub_etnia) AS cuantos_por_etnia,participantes.sub_etnia FROM participantes WHERE participantes.id IN (");

        $sql_base_escolaridad=trim("
                    SELECT COUNT(escolaridad) AS cuantos_por_escolaridad,participantes.escolaridad FROM participantes WHERE participantes.id IN (");
        $sql_base_titulo=trim("
                    SELECT COUNT(titulo_obt) AS cuantos_por_titulo,participantes.titulo_obt FROM participantes WHERE participantes.id IN (");
        $sql_base_cargo=trim("
                    SELECT COUNT(cargo_poblador) AS cuantos_por_cargo,participantes.cargo_poblador FROM participantes WHERE participantes.id IN (");
        $sql_base_ingreso_pdp=trim("
                    SELECT COUNT(anio_ingreso_pdp) AS cuantos_por_anio,participantes.anio_ingreso_pdp FROM participantes WHERE participantes.id IN (");
         $sql_base_zona=trim("
                    SELECT COUNT(zona) AS cuantos_por_zona,participantes.zona FROM participantes WHERE participantes.id IN (");
        $sql_base_linea_organizacion=trim("
                    SELECT COUNT(lineas.id) AS cuantos_por_organizacion,lineas.nombre_linea as organizacion FROM participantes 
                    INNER JOIN detalle_procesos ON participantes.documento = detalle_procesos.id_usuario 
                    INNER join proceso ON proceso.id = detalle_procesos.id_proceso
                    INNER JOIN lineas ON lineas.id = proceso.fk_id_linea
                    WHERE participantes.id IN (");
        $sql_base_proceso=trim("
                    SELECT COUNT(proceso.id) AS cuantos_por_proceso,proceso.nombre_proceso as proceso FROM participantes 
                    INNER JOIN detalle_procesos ON participantes.documento = detalle_procesos.id_usuario 
                    INNER join proceso ON proceso.id = detalle_procesos.id_proceso
                    INNER JOIN lineas ON lineas.id = proceso.fk_id_linea
                    WHERE participantes.id IN (");
                    
       
         $dadoc=array();
         $danom=array();
         $sqlnom=" ";
         $sqldoc=" ";
        //COUNSTRUIR SENTENCIA
        switch ($datos->datos->id_evento) {
             case 'G':
                    $sql=" ";
                    foreach ($datos->datos->datos as $key => $value) {
                            if(gettype($value)=="array"){

                                switch ($key) {
                                    case 'tipo_doc':

                                            $sql.=" tipo_doc IN (";

                                            foreach ($value as $k => $v) {
                                                if($k==count($value)-1){
                                                    $sql.="'".$v."') AND";
                                                    break;
                                                }
                                                $sql.="'".$v."',";
                                                
                                            }
                                           
                                        break;
                                    case 'edad':
                                         $sql.="(";
                                            foreach ($value as $k => $v) {
                                                if($k==count($value)-1){
                                                    $sql.=" edad  >= '".explode("a", $v)[0]."' AND edad <= '".explode("a", $v)[1]."') AND ";
                                                    break;
                                                }
                                                $sql.=" edad >= '".explode("a", $v)[0]."' AND  edad <= '".explode("a", $v)[1]."' OR ";
                                                
                                            }

                                        break; 
                                    case 'zonas':
                                     $sql.=" zona IN (";
                                            foreach ($value as $k => $v) {
                                                if($k==count($value)-1){
                                                    $sql.="'".$v."') AND";
                                                    break;
                                                }
                                                $sql.="'".$v."',";
                                                
                                            }
                                        # code...
                                        break; 
                                    case 'genero':
                                         $sql.=" genero IN (";
                                            foreach ($value as $k => $v) {
                                                if($k==count($value)-1){
                                                    $sql.="'".$v."') AND";
                                                    break;
                                                }
                                                $sql.="'".$v."',";
                                                
                                            }
                                        break;  
                                    case 'escolaridad':
                                        $sql.=" escolaridad IN (";
                                            foreach ($value as $k => $v) {
                                                if($k==count($value)-1){
                                                    $sql.="'".$v."') AND";
                                                    break;
                                                }
                                                $sql.="'".$v."',";
                                                
                                            }
                                        break;            
                                    case 'etnia':
                                        $sql.=" etnia IN (";
                                            foreach ($value as $k => $v) {
                                                if($k==count($value)-1){
                                                    $sql.="'".$v."') AND";
                                                    break;
                                                }
                                                $sql.="'".$v."',";
                                                
                                            }
                                        break;
                                    case 'acepta_terminos':
                                        $sql.=" acepta_terminos IN (";
                                            foreach ($value as $k => $v) {
                                                if($k==count($value)-1){
                                                    $sql.="'".$v."') AND";
                                                    break;
                                                }
                                                $sql.="'".$v."',";
                                                
                                            }
                                        break;    
                                    default:
                                        # code...
                                        break;
                                }
                            }
                    }
                    $sql_org=" ";
                    $sql_pro=" ";
                    foreach ($datos->datos->datos as $key => $value) {
                            if(gettype($value)!="array" && $value != "" && $key!="tipo_reporte"){
                                if($key=="dep_nacimiento"){
                                    $value=explode("-", $value)[1];
                                }
                                if($key=="documento"){
                                    $sql.=" documento = '".$value."' AND";
                                    $sqlnom.=" documento = '".$value."'";
                                    //echo $sql_base_doc;
                                    
                                }
                                if($key=="pri_nombre"){
                                    $sql.=" pri_nombre LIKE '".$value."' OR seg_nombre LIKE '".$value."' OR pri_apellido LIKE '".$value."' OR seg_apellido = '".$value."' AND";

                                   $sqldoc.=" pri_nombre LIKE '".$value."' OR seg_nombre LIKE '".$value."' OR pri_apellido LIKE '".$value."' OR seg_apellido = '".$value."'";
                                }
                                if($key=="lineas.nombre_linea" && $value!=""){
                                    $sql_org.=" AND lineas.id = '".explode("-", $value)[0]."'";
                                     $sql.=" lineas.id = '".explode("-", $value)[0]."' AND";  
                                }
                                if($key == "proceso.nombre_proceso" && $value!=""){
                                    $sql_pro.=" AND proceso.id = '".explode("-", $value)[0]."'";
                                     $sql.=" proceso.id = '".explode("-", $value)[0]."' AND";  
                                }
                                if($key!="documento" && $key != "id_evento" && $key != "pri_nombre" && $key != "lineas.nombre_linea" && $key != "proceso.nombre_proceso"){
                                    $sql.=" $key ='".$value."' AND";    
                                }
                            }
                    }
                 break;
             
             default:
                
                    $sql=" ";
                   
                    if(gettype($datos->datos->id_evento)=="array"){
                        $sql.=" eventos.id IN ( ";
                        $fin =count($datos->datos->id_evento)-1; 
                        foreach ($datos->datos->id_evento as $key => $value) {
                            if($key==$fin){
                                $sql.=" '".$value."') ";
                                break;
                            }else{
                                $sql.=" '".$value."', ";
                            }
                           
                        }
                        $sql.= " AND ( ";
                    }else{
                        $sql.= " eventos.id = '".$datos->datos->id_evento."' AND ( ";    
                    }

                    foreach ($datos->datos->datos as $key => $value) {
                            if(gettype($value)=="array"){
                                switch ($key) {
                                    case 'tipo_doc':

                                            $sql.=" tipo_doc IN (";
                                            foreach ($value as $k => $v) {
                                                if($k==count($value)-1){
                                                    $sql.="'".$v."') AND";
                                                    break;
                                                }
                                                $sql.="'".$v."',";
                                                
                                            }
                                            
                                        break;
                                    case 'edad':
                                         $sql.="(";
                                            foreach ($value as $k => $v) {
                                                if($k==count($value)-1){
                                                    $sql.=" edad  >= '".explode("a", $v)[0]."' AND edad <= '".explode("a", $v)[1]."')AND ";
                                                    break;
                                                }
                                                $sql.=" edad >= '".explode("a", $v)[0]."' AND  edad <= '".explode("a", $v)[1]."' OR ";
                                                
                                            }

                                        break; 
                                    case 'zonas':
                                     $sql.=" zona IN (";
                                            foreach ($value as $k => $v) {
                                                if($k==count($value)-1){
                                                    $sql.="'".$v."') AND";
                                                    break;
                                                }
                                                $sql.="'".$v."',";
                                                
                                            }
                                        # code...
                                        break; 
                                    case 'genero':
                                         $sql.=" genero IN (";
                                            foreach ($value as $k => $v) {
                                                if($k==count($value)-1){
                                                    $sql.="'".$v."') AND";
                                                    break;
                                                }
                                                $sql.="'".$v."',";
                                                
                                            }
                                        break;  
                                    case 'escolaridad':
                                        $sql.=" escolaridad IN (";
                                            foreach ($value as $k => $v) {
                                                if($k==count($value)-1){
                                                    $sql.="'".$v."') AND";
                                                    break;
                                                }
                                                $sql.="'".$v."',";
                                                
                                            }
                                        break;   
                                    case 'etnia':
                                        $sql.=" etnia IN (";
                                            foreach ($value as $k => $v) {
                                                if($k==count($value)-1){
                                                    $sql.="'".$v."') AND";
                                                    break;
                                                }
                                                $sql.="'".$v."',";
                                                
                                            }
                                        break;             
                                    case 'acepta_terminos':
                                     $sql.=" acepta_terminos IN (";
                                            foreach ($value as $k => $v) {
                                                if($k==count($value)-1){
                                                    $sql.="'".$v."') AND";
                                                    break;
                                                }
                                                $sql.="'".$v."',";
                                                
                                            }
                                        # code...
                                        break; 
                                    default:
                                        # code...
                                        break;
                                }
                            }
                    }
                    //echo $sql_base_nom;
                    //echo ":(";
                    //echo $sql_base_nom;
                    $sql_org=" ";
                    $sql_pro=" ";
                    foreach ($datos->datos->datos as $key => $value) {
                            if(gettype($value)!="array" && $value != "" && $key!="tipo_reporte" ){
                                if($key=="dep_nacimiento"){
                                    $value=explode("-", $value)[1];
                                }
                                //echo $key."<br>";
                                if($key=="documento"){
                                    $sql.=" documento = '".$value."' AND ";
                                    $fin =count($datos->datos->id_evento)-1; 
                                    $sql_eve_doc=" AND eventos.id IN (";
                                    $sqldoc.=" documento = '".$value."' ";
                                   
                                    foreach($datos->datos->id_evento as $value){
                                        if($fin==$key){
                                            $sql_eve_doc.="'".$value."')";   
                                            break;  
                                        }
                                        $sql_eve_doc.="'".$value."',";     
                                    }
                                    
                                    $sqldoc.=$sql_eve_doc;
                                    
                                    
                                }
                                if($key=="pri_nombre"){
                                    $sql.=" pri_nombre LIKE '".$value."' OR seg_nombre LIKE '".$value."' OR pri_apellido LIKE '".$value."' OR seg_apellido = '".$value."' AND " ;
                                    $fin =count($datos->datos->id_evento)-1; 
                                    $sql_eve_nom=" AND eventos.id IN (";
                                    $sqlnom.="(pri_nombre LIKE '".$value."' OR seg_nombre LIKE '".$value."' OR pri_apellido LIKE '".$value."' OR seg_apellido = '".$value."'".") ";    
                                    foreach($datos->datos->id_evento as $value){
                                        if($fin==$key){
                                            $sql_eve_nom.="'".$value."')";     
                                            break;
                                        }
                                        $sql_eve_nom.="'".$value."',";     
                                    }
                                    
                                    $sqlnom.=$sql_eve_nom;
                                    
                                //echo $sql_base_nom.$sql_2 ;   
                                    
                                }
                                if($key=="lineas.nombre_linea" && $value!=""){
                                    $sql_org.=" AND lineas.id = '".explode("-", $value)[0]."'";
                                     $sql.=" lineas.id = '".explode("-", $value)[0]."' AND ";  
                                }
                                if($key == "proceso.nombre_proceso" && $value!=""){
                                    $sql_pro.=" AND proceso.id = '".explode("-", $value)[0]."'";
                                     $sql.=" proceso.id = '".explode("-", $value)[0]."' AND ";  
                                }
                                if($key!="documento" && $key != "id_evento" && $key != "pri_nombre" && $key != "lineas.nombre_linea" && $key!="proceso.nombre_proceso"){
                                    $sql.=" $key ='".$value."' AND ";    
                                }
                            }
                    }
                    
                    //echo ":=(";
                    //echo $sql;
                 break;
        }
      
        //echo $sql_base_genero.$sql_base.$sql.") GROUP BY genero";
        //var_dump($sqldoc);                
        $i=0;

        
        if($datos->datos->id_evento=="G"){
            $sql=substr($sql,0,-4);
            //echo "::".$sql_base.$sql." GROUP BY id";
            $res=DB::select(trim($sql_base.$sql." GROUP BY id"));
        }else{
            $sql=substr($sql,0,-4);    
            //echo $sql_base.$sql.") GROUP BY id";
            $res=DB::select(trim($sql_base.$sql.") GROUP BY id"));
        }
        $ssql=$sql_base.$sql." GROUP BY id";
        
      
        //EJECUTAR SENTENCIA
        switch ($datos->datos->id_evento) {
            case 'G':
                    if($sql!="" && $sql!=" " ){
                        $dagen=DB::select(trim($sql_base_genero.$sql_base_id.$sql.") GROUP BY genero")); 
                        $dasubgen=DB::select(trim($sql_base_sub_genero.$sql_base_id.$sql." AND sub_genero <> 'NULL' )  GROUP BY sub_genero"));                       
                        $daedad=DB::select(trim($sql_base_edad.$sql_base_id.$sql.") GROUP BY edad")); 
                        $dadepnac=DB::select(trim($sql_base_dep_nacimiento.$sql_base_id.$sql.") GROUP BY dep_nacimiento"));  
                        $daciunac=DB::select(trim($sql_base_ciud_nacimiento.$sql_base_id.$sql.") GROUP BY ciud_nacimiento")); 
                        $davernac=DB::select(trim($sql_base_vereda_nacimiento.$sql_base_id.$sql." AND vereda_nacimiento <> 'NULL' ) GROUP BY vereda_nacimiento")); 
                        $dadepubi=DB::select(trim($sql_base_dep_ubi.$sql_base_id.$sql.") GROUP BY departamento_ubi"));  
                        $daciuubi=DB::select(trim($sql_base_ciud_ubi.$sql_base_id.$sql.") GROUP BY municipio")); 
                        $daverubi=DB::select(trim($sql_base_vereda_ubi.$sql_base_id.$sql." AND vereda_ubi <> 'NULL' ) GROUP BY vereda_ubi")); 
                        $dacapdif=DB::select(trim($sql_base_cap_dife.$sql_base_id.$sql.") GROUP BY cap_dife"));   
                        $daetnia=DB::select(trim($sql_base_etnia.$sql_base_id.$sql.") GROUP BY etnia"));
                        $dasubetnia=DB::select(trim($sql_base_sub_etnia.$sql_base_id.$sql." AND sub_etnia <> 'NULL' ) GROUP BY sub_etnia"));
                        $daescolaridad=DB::select(trim($sql_base_escolaridad.$sql_base_id.$sql.") GROUP BY escolaridad"));
                        $datitulo=DB::select(trim($sql_base_titulo.$sql_base_id.$sql.") GROUP BY titulo_obt"));
                        $daanioingreso=DB::select(trim($sql_base_ingreso_pdp.$sql_base_id.$sql.") GROUP BY anio_ingreso_pdp"));

                        $dacargo=DB::select(trim($sql_base_cargo.$sql_base_id.$sql.") GROUP BY cargo_poblador"));
                        $dazona=DB::select(trim($sql_base_zona.$sql_base_id.$sql.") GROUP BY zona"));

                       $daterminos=DB::select(trim($sql_base_acepta_terminos.$sql_base_id.$sql.") GROUP BY acepta_terminos"));                        

                        if($sql_org!=" "){

                            $daorga=DB::select(trim($sql_base_linea_organizacion.$sql_base_id.$sql." )".$sql_org." GROUP BY lineas.id"));    
                        }else{
                            $daorga=DB::select(trim($sql_base_linea_organizacion.$sql_base_id.$sql." ) GROUP BY lineas.id"));
                        }
                        
                        if($sql_org==" " && $sql_pro == " "){
                            $daproc=DB::select(trim($sql_base_proceso.$sql_base_id.$sql." ) GROUP BY proceso.id"));
                        }
                        if($sql_org!=" " && $sql_pro == " "){
                            $daproc=DB::select(trim($sql_base_proceso.$sql_base_id.$sql." ".$sql_org." )  ".$sql_org." GROUP BY proceso.id"));
                        }
                        //var_dump($sql_pro);
                        //var_dump($sql_org);

                        if($sql_org==" " && $sql_pro != " "){
                            //ECHO trim($sql_base_proceso.$sql_base_id.$sql." )  ".$sql_pro." GROUP BY proceso.id");
                            $daproc=DB::select(trim($sql_base_proceso.$sql_base_id.$sql." )  ".$sql_pro." GROUP BY lineas.id"));
                        }

                        if($sql_org!=" " && $sql_pro != " "){
                            $daproc=DB::select(trim($sql_base_proceso.$sql_base_id.$sql." )  ".$sql_org." GROUP BY proceso.id"));
                        }



                            



                        
                         
                        $datbleventos=DB::select(trim($sql_base_tbl_eventos_G." GROUP BY eventos.id ORDER BY cuantos_por_eventos DESC"));
                        $ssql=trim($sql_base_tbl_eventos_G." GROUP BY eventos.id ORDER BY cuantos_por_eventos DESC");
                    }
                    if($sqldoc!="" && $sqldoc!=" "){
                        $dadoc=DB::select(trim($sql_base_doc.$sqldoc." GROUP BY documento,eventos.id"));  
                    }

                    if($sqlnom!="" && $sqlnom!=" "){
                        
                        $danom=DB::select(trim($sql_base_nom.$sqlnom."  GROUP BY eventos.id,participantes.id ORDER BY eventos.id"));    
                    }


                                        
                    
                       
                break;
            
            default:
                if($sql!="" && $sql!=" " ){
                        $dagen=DB::select(trim($sql_base_genero.$sql_base_id.$sql.")) GROUP BY genero")); 
                        $dasubgen=DB::select(trim($sql_base_sub_genero.$sql_base_id.$sql.") AND sub_genero <> 'NULL' ) GROUP BY sub_genero"));                      
                        $daedad=DB::select(trim($sql_base_edad.$sql_base_id.$sql.")) GROUP BY edad")); 
                        $dadepnac=DB::select(trim($sql_base_dep_nacimiento.$sql_base_id.$sql.")) GROUP BY dep_nacimiento"));  
                        $daciunac=DB::select(trim($sql_base_ciud_nacimiento.$sql_base_id.$sql.")) GROUP BY ciud_nacimiento"));
                        $davernac=DB::select(trim($sql_base_vereda_nacimiento.$sql_base_id.$sql.") AND vereda_nacimiento <> 'NULL') GROUP BY vereda_nacimiento"));  
                        $dadepubi=DB::select(trim($sql_base_dep_ubi.$sql_base_id.$sql.")) GROUP BY departamento_ubi"));  
                        $daciuubi=DB::select(trim($sql_base_ciud_ubi.$sql_base_id.$sql.")) GROUP BY municipio"));
                        $daverubi=DB::select(trim($sql_base_vereda_ubi.$sql_base_id.$sql.") AND vereda_ubi <> 'NULL') GROUP BY vereda_ubi"));  
                        $dacapdif=DB::select(trim($sql_base_cap_dife.$sql_base_id.$sql.")) GROUP BY cap_dife"));   
                        $daetnia=DB::select(trim($sql_base_etnia.$sql_base_id.$sql.")) GROUP BY etnia"));
                        $dasubetnia=DB::select(trim($sql_base_sub_etnia.$sql_base_id.$sql.") AND sub_etnia <> 'NULL' ) GROUP BY sub_etnia"));
                        $daescolaridad=DB::select(trim($sql_base_escolaridad.$sql_base_id.$sql.")) GROUP BY escolaridad"));
                        $datitulo=DB::select(trim($sql_base_titulo.$sql_base_id.$sql.")) GROUP BY titulo_obt"));
                        $daanioingreso=DB::select(trim($sql_base_ingreso_pdp.$sql_base_id.$sql.")) GROUP BY anio_ingreso_pdp"));
                        $dacargo=DB::select(trim($sql_base_cargo.$sql_base_id.$sql.")) GROUP BY cargo_poblador")); 
                        $dazona=DB::select(trim($sql_base_zona.$sql_base_id.$sql.")) GROUP BY zona"));
                        //$daorga=DB::select(trim($sql_base_linea_organizacion.$sql_base_id.$sql.")) ".$sql_org." GROUP BY lineas.id"));
                        //$daproc=DB::select(trim($sql_base_proceso.$sql_base_id.$sql.")) ".$sql_pro." GROUP BY proceso.id")); 
                        $daterminos=DB::select(trim($sql_base_acepta_terminos.$sql_base_id.$sql.")) GROUP BY acepta_terminos"));                        


                        if($sql_org!=" "){

                            $daorga=DB::select(trim($sql_base_linea_organizacion.$sql_base_id.$sql." ".$sql_org." ))".$sql_org." GROUP BY lineas.id"));    
                        }else{
                            $daorga=DB::select(trim($sql_base_linea_organizacion.$sql_base_id.$sql." )) GROUP BY lineas.id"));
                        }
                        
                        if($sql_org==" " && $sql_pro == " "){
                            $daproc=DB::select(trim($sql_base_proceso.$sql_base_id.$sql." )) GROUP BY proceso.id"));
                        }
                        if($sql_org!=" " && $sql_pro == " "){
                            $daproc=DB::select(trim($sql_base_proceso.$sql_base_id.$sql." ".$sql_org." ))  ".$sql_org." GROUP BY proceso.id"));
                        }
                        //var_dump($sql_pro);
                        //var_dump($sql_org);

                        if($sql_org==" " && $sql_pro != " "){
                            //$ssql= trim($sql_base_proceso.$sql_base_id.$sql." ))  ".$sql_pro." GROUP BY proceso.id");
                            $daproc=DB::select(trim($sql_base_proceso.$sql_base_id.$sql." ))  ".$sql_pro." GROUP BY proceso.id"));
                        }

                        if($sql_org!=" " && $sql_pro != " "){
                            $daproc=DB::select(trim($sql_base_proceso.$sql_base_id.$sql." ))  ".$sql_org." GROUP BY proceso.id"));
                        }






                       


                        if(gettype($datos->datos->id_evento)=="array"){
                            
                            $sql_eve=" eventos.id IN ( ";
                            $fin =count($datos->datos->id_evento)-1; 
                            foreach ($datos->datos->id_evento as $key => $value) {
                                if($key==$fin){
                                    $sql_eve.= " '".$value."') ";
                                    break;
                                }else{
                                    $sql_eve.=" '".$value."', ";
                                }
                            }
                           

                           

                             $datbleventos=DB::select(trim($sql_base_tbl_eventos.$sql_eve." GROUP BY eventos.id"));
                        }else{
                             $datbleventos=DB::select(trim($sql_base_tbl_eventos." eventos.id = '".$datos->datos->id_evento."' GROUP BY eventos.id"));
                        }


                    }
                    if($sqldoc!="" && $sqldoc!=" "){
                        $dadoc=DB::select(trim($sql_base_doc.$sqldoc." GROUP BY documento,eventos.id"));  
                    }

                    if($sqlnom!="" && $sqlnom!=" "){
                        
                        $danom=DB::select(trim($sql_base_nom.$sqlnom." GROUP BY eventos.id,participantes.id"));        
                    }
                    
                   
                    

                break;
        }
        
        if(count($res)>0){
               return response()->json(array("mensaje"=>"REPORTE ",
                    "datos"=>$res,
                    "datos_genero"=>$dagen,
                    "datos_sub_genero"=>$dasubgen,
                    "datos_edaddes"=>$daedad,
                    "datos_dep_nac"=>$dadepnac,
                    "datos_ciu_nac"=>$daciunac,
                    "datos_ver_nac"=>$davernac,
                    "datos_dep_ubi"=>$dadepubi,
                    "datos_ciu_ubi"=>$daciuubi,
                    "datos_ver_ubi"=>$daverubi,
                    "datos_cap_dife"=>$dacapdif,
                    "datos_etnia"=>$daetnia,
                    "datos_sub_etnia"=>$dasubetnia,
                    "datos_escolaridad"=>$daescolaridad,
                    "titulo_obt"=>$datitulo,
                    "datos_organizacion"=>$daorga,
                    "datos_proceso"=>$daproc,
                    "documento"=>$dadoc,
                    "nombre"=>$danom,
                    "eventos"=>$datbleventos,
                    "anio_ingreso_pdp"=>$daanioingreso,
                    "cargo"=>$dacargo,
                    "zona"=>$dazona,
                    "terminos"=>$daterminos,
                    "respuesta"=>true,
                    "sql"=>$ssql
                )); 

        }else{
            return response()->json(array("mensaje"=>"REPORTE  sin datos que mostrar",
                    "datos"=>$res,
                    "datos_genero"=>$dagen,
                    "datos_sub_genero"=>$dasubgen,
                    "datos_edaddes"=>$daedad,
                    "datos_dep_nac"=>$dadepnac,
                    "datos_ciu_nac"=>$daciunac,
                    "datos_ver_nac"=>$davernac,
                    "datos_dep_ubi"=>$dadepubi,
                    "datos_ciu_ubi"=>$daciuubi,
                    "datos_ver_ubi"=>$daverubi,
                    "datos_cap_dife"=>$dacapdif,
                    "datos_etnia"=>$daetnia,
                    "datos_sub_etnia"=>$dasubetnia,
                    "datos_escolaridad"=>$daescolaridad,
                    "titulo_obt"=>$datitulo,
                    "datos_organizacion"=>$daorga,
                    "datos_proceso"=>$daproc,
                    "documento"=>$dadoc,
                    "nombre"=>$danom,
                    "eventos"=>$datbleventos,
                    "anio_ingreso_pdp"=>$daanioingreso,
                    "cargo"=>$dacargo,
                    "zona"=>$dazona,
                    "terminos"=>$daterminos,
                    "respuesta"=>false,
                    "sql"=>trim($sql_base_zona.$sql_base_id.$sql.")) GROUP BY zona")
                )); 
        }
            
            
            

                
    }
    
    public function repo_eventos($id){

        if($id=="G"){
            $cap_dife=DB::table("participantes")
                ->join("detalle_participantes","detalle_participantes.user_id","=","participantes.documento")
                ->join("eventos","detalle_participantes.event_id","=","eventos.id")
                ->select("cap_dife",DB::RAW("COUNT(cap_dife) as cuantos_por_cap_dife"))
                ->groupBy("cap_dife")
                ->get();
            $etnia=DB::table("participantes")
                ->join("detalle_participantes","detalle_participantes.user_id","=","participantes.documento")
                ->join("eventos","detalle_participantes.event_id","=","eventos.id")
                ->select("etnia",DB::RAW("COUNT(etnia) as cuantos_por_etnia"))
                ->groupBy("etnia")
                ->get();    
            $dep_nacimiento=DB::table("participantes")
                ->join("detalle_participantes","detalle_participantes.user_id","=","participantes.documento")
                ->join("eventos","detalle_participantes.event_id","=","eventos.id")
                ->select("dep_nacimiento",DB::RAW("COUNT(dep_nacimiento) as cuantos_por_dep_nacimiento"))
                ->groupBy("dep_nacimiento")
                ->get();      
            $ciud_nacimiento=DB::table("participantes")
                ->join("detalle_participantes","detalle_participantes.user_id","=","participantes.documento")
                ->join("eventos","detalle_participantes.event_id","=","eventos.id")
                ->select("ciud_nacimiento",DB::RAW("COUNT(ciud_nacimiento) as cuantos_ciud_nacimiento"))
                ->groupBy("ciud_nacimiento")
                ->get();    
            $vereda_nacimiento=DB::table("participantes")
                ->join("detalle_participantes","detalle_participantes.user_id","=","participantes.documento")
                ->join("eventos","detalle_participantes.event_id","=","eventos.id")
                ->where("vereda_nacimiento","<>","NULL")
                ->select("vereda_nacimiento",DB::RAW("COUNT(vereda_nacimiento) as cuantos_vereda_nacimiento"))
                ->groupBy("vereda_nacimiento")
                ->get();                    
            $departamento_ubi=DB::table("participantes")
                ->join("detalle_participantes","detalle_participantes.user_id","=","participantes.documento")
                ->join("eventos","detalle_participantes.event_id","=","eventos.id")
                ->select("departamento_ubi",DB::RAW("COUNT(departamento_ubi) as cuantos_por_departamento_ubi"))
                ->groupBy("departamento_ubi")
                ->get(); 
                             
            $municipio=DB::table("participantes")
                ->join("detalle_participantes","detalle_participantes.user_id","=","participantes.documento")
                ->join("eventos","detalle_participantes.event_id","=","eventos.id")
                ->select("municipio",DB::RAW("COUNT(municipio) as cuantos_por_municipio"))
                ->groupBy("municipio")
                ->get();     
            $vereda_ubi=DB::table("participantes")
                ->join("detalle_participantes","detalle_participantes.user_id","=","participantes.documento")
                ->join("eventos","detalle_participantes.event_id","=","eventos.id")
                ->where("vereda_ubi","<>","NULL")
                ->select("vereda_ubi",DB::RAW("COUNT(vereda_ubi) as cuantos_por_vereda_ubi"))
                ->groupBy("vereda_ubi")
                ->get();           
            $proceso=DB::table("participantes")
                ->join("detalle_participantes","detalle_participantes.user_id","=","participantes.documento")
                ->join("detalle_procesos","detalle_procesos.id_usuario","=","participantes.documento")
                ->join("proceso","proceso.id","=","detalle_procesos.id_proceso")
                ->join("eventos","detalle_participantes.event_id","=","eventos.id")
                ->select(DB::RAW("CONCAT(proceso.id,'-',proceso.nombre_proceso) as proceso"),DB::RAW("COUNT(proceso.id) as cuantos_por_proceso"))
                ->groupBy("proceso.id")
                ->get();  
            $organizacion=DB::table("participantes")
                ->join("detalle_participantes","detalle_participantes.user_id","=","participantes.documento")
                 ->join("detalle_procesos","detalle_procesos.id_usuario","=","participantes.documento")
                ->join("proceso","proceso.id","=","detalle_procesos.id_proceso")
                ->join("lineas","lineas.id","=","proceso.fk_id_linea")
                ->join("eventos","detalle_participantes.event_id","=","eventos.id")
                ->select(DB::RAW("CONCAT(lineas.id,'-',lineas.nombre_linea) as organizacion"),DB::RAW("COUNT(lineas.id) as cuantos_por_organizacion"))
                ->groupBy("lineas.id")
                ->get();      
            $eventos=DB::table("eventos")->get();          
        }else{
            $cap_dife=DB::table("participantes")
                ->join("detalle_participantes","detalle_participantes.user_id","=","participantes.documento")
                ->join("eventos","detalle_participantes.event_id","=","eventos.id")
                ->where("eventos.id",$id)
                ->select("cap_dife",DB::RAW("COUNT(cap_dife) as cuantos_por_cap_dife"))
                ->groupBy("cap_dife")
                ->get();
            $etnia=DB::table("participantes")
                ->join("detalle_participantes","detalle_participantes.user_id","=","participantes.documento")
                ->join("eventos","detalle_participantes.event_id","=","eventos.id")
                 ->where("eventos.id",$id)
                ->select("etnia",DB::RAW("COUNT(etnia) as cuantos_por_etnia"))
                ->groupBy("etnia")
                ->get();      
            $dep_nacimiento=DB::table("participantes")
                ->join("detalle_participantes","detalle_participantes.user_id","=","participantes.documento")
                ->join("eventos","detalle_participantes.event_id","=","eventos.id")
                 ->where("eventos.id",$id)
                ->select("dep_nacimiento",DB::RAW("COUNT(dep_nacimiento) as cuantos_por_dep_nacimiento"))
                ->groupBy("dep_nacimiento")
                ->get();     
            $ciud_nacimiento=DB::table("participantes")
                ->join("detalle_participantes","detalle_participantes.user_id","=","participantes.documento")
                ->join("eventos","detalle_participantes.event_id","=","eventos.id")
                 ->where("eventos.id",$id)
                ->select("ciud_nacimiento",DB::RAW("COUNT(ciud_nacimiento) as cuantos_por_ciud_nacimiento"))
                ->groupBy("ciud_nacimiento")
                ->get(); 
             $vereda_nacimiento=DB::table("participantes")
                ->join("detalle_participantes","detalle_participantes.user_id","=","participantes.documento")
                ->join("eventos","detalle_participantes.event_id","=","eventos.id")
                ->where([["eventos.id",$id],["vereda_nacimiento","<>","NULL"]])
                ->select("vereda_nacimiento",DB::RAW("COUNT(vereda_nacimiento) as cuantos_vereda_nacimiento"))
                ->groupBy("vereda_nacimiento")
                ->get();    
            $departamento_ubi=DB::table("participantes")
                ->join("detalle_participantes","detalle_participantes.user_id","=","participantes.documento")
                ->join("eventos","detalle_participantes.event_id","=","eventos.id")
                 ->where("eventos.id",$id)
                ->select("departamento_ubi",DB::RAW("COUNT(departamento_ubi) as cuantos_por_departamento_ubi"))
                ->groupBy("departamento_ubi")
                ->get(); 
                      
            $municipio=DB::table("participantes")
                ->join("detalle_participantes","detalle_participantes.user_id","=","participantes.documento")
                ->join("eventos","detalle_participantes.event_id","=","eventos.id")
                ->where("eventos.id",$id)
                ->select("municipio",DB::RAW("COUNT(municipio) as cuantos_por_municipio"))
                ->groupBy("municipio")
                ->get(); 
            $vereda_ubi=DB::table("participantes")
                ->join("detalle_participantes","detalle_participantes.user_id","=","participantes.documento")
                ->join("eventos","detalle_participantes.event_id","=","eventos.id")
                ->where([["eventos.id",$id],["vereda_ubi","<>","NULL"]])
                ->select("vereda_ubi",DB::RAW("COUNT(vereda_ubi) as cuantos_por_vereda_ubi"))
                ->groupBy("vereda_ubi")
                ->get();     
            $proceso=DB::table("participantes")
                ->join("detalle_participantes","detalle_participantes.user_id","=","participantes.documento")
                 ->join("detalle_procesos","detalle_procesos.id_usuario","=","participantes.documento")
                ->join("proceso","proceso.id","=","detalle_procesos.id_proceso")
                ->join("eventos","detalle_participantes.event_id","=","eventos.id")
                ->where("eventos.id",$id)
                ->select(DB::RAW("CONCAT(proceso.id,'-',proceso.nombre_proceso) as proceso"),DB::RAW("COUNT(proceso.id) as cuantos_por_proceso"))
                ->groupBy("proceso.id")
                ->get();    
            $organizacion=DB::table("participantes")
                ->join("detalle_participantes","detalle_participantes.user_id","=","participantes.documento")
                ->join("detalle_procesos","detalle_procesos.id_usuario","=","participantes.documento")
                ->join("proceso","proceso.id","=","detalle_procesos.id_proceso")
                ->join("lineas","lineas.id","=","proceso.fk_id_linea")
                ->join("eventos","detalle_participantes.event_id","=","eventos.id")
                ->where("eventos.id",$id)
                ->select(DB::RAW("CONCAT(lineas.id,'-',lineas.nombre_linea) as organizacion"),DB::RAW("COUNT(lineas.id) as cuantos_por_organizacion"))
                ->groupBy("lineas.id")
                ->get();   
            $eventos=DB::table("eventos")->where("id",$id)->get();    
                    
        }
                        

        return response()->json(array("mensaje"=>"",
                                    "cap_dife"=>$cap_dife,
                                    "etnia"=>$etnia,
                                    "dep_nacimiento"=>$dep_nacimiento,
                                    "ciud_nacimiento"=>$ciud_nacimiento,
                                    "vereda_nacimiento"=>$vereda_nacimiento,
                                    "municipio"=>$municipio,
                                    "departamento_ubi"=>$departamento_ubi,
                                    "vereda_ubi"=>$vereda_ubi,
                                    "proceso"=>$proceso,
                                    "organizacion"=>$organizacion,
                                    "eventos"=>$eventos,
                                    "respuesta"=>true));
    }
}
