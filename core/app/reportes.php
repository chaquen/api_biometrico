<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

class reportes extends Model
{
    //

    public function reporte_lista($datos){



          $sql_base_tbl_eventos_G=trim("SELECT eventos.name,eventos.date,COUNT(eventos.id) as cuantos_por_eventos,city FROM `detalle_participantes` INNER join eventos on eventos.id=detalle_participantes.event_id ");
        $sql_base_tbl_eventos=trim("SELECT eventos.name,eventos.date,COUNT(eventos.id) as cuantos_por_eventos,city FROM `detalle_participantes` INNER join eventos on eventos.id=detalle_participantes.event_id WHERE ");

        $sql_base=trim("
                    SELECT participantes.id,participantes.tipo_doc,participantes.documento,participantes.pri_nombre,participantes.seg_nombre,participantes.pri_apellido,participantes.seg_apellido  ,participantes.edad,participantes.genero,participantes.escolaridad,participantes.zona,participantes.dep_nacimiento,participantes.ciud_nacimiento,participantes.municipio,participantes.etnia,participantes.cap_dife FROM participantes 
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
        $sql_base_genero=trim("
                    SELECT COUNT(genero) AS cuentos_por_genero,participantes.genero FROM participantes WHERE participantes.id IN (");
        $sql_base_edad=trim("
                    SELECT COUNT(edad) AS cuentos_por_edad,participantes.edad FROM participantes WHERE participantes.id IN (");
        $sql_base_dep_nacimiento=trim("
                    SELECT COUNT(dep_nacimiento) AS cuantos_por_dep_nacimiento,participantes.dep_nacimiento FROM participantes WHERE participantes.id IN (");
        $sql_base_ciud_nacimiento=trim("
                    SELECT COUNT(ciud_nacimiento) AS cuantos_por_ciud_nacimiento,participantes.ciud_nacimiento FROM participantes WHERE participantes.id IN (");
        $sql_base_cap_dife=trim("
                    SELECT COUNT(cap_dife) AS cuantos_por_cap_dife,participantes.cap_dife FROM participantes WHERE participantes.id IN (");
        $sql_base_etnia=trim("
                    SELECT COUNT(etnia) AS cuantos_por_etnia,participantes.etnia FROM participantes WHERE participantes.id IN ( ");
         $sql_base_sub_etnia=trim("
                    SELECT COUNT(sub_etnia) AS cuantos_por_etnia,participantes.sub_etnia FROM participantes WHERE participantes.id IN (");

        $sql_base_escolaridad=trim("
                    SELECT COUNT(escolaridad) AS cuantos_por_escolaridad,participantes.escolaridad FROM participantes WHERE participantes.id IN (");
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
        $sql_base_doc=trim("
                    SELECT eventos.name,participantes.id,participantes.id,participantes.tipo_doc,participantes.documento,participantes.pri_nombre,participantes.seg_nombre,participantes.pri_apellido,participantes.seg_apellido,participantes.edad,participantes.genero,participantes.escolaridad,participantes.zona,participantes.dep_nacimiento,participantes.ciud_nacimiento,participantes.municipio,detalle_participantes.updated_at,participantes.cap_dife,participantes.etnia FROM participantes 
                        INNER JOIN detalle_participantes ON detalle_participantes.user_id = participantes.documento 
                        INNER JOIN detalle_procesos ON detalle_procesos.id_usuario = participantes.documento
                        INNER JOIN proceso ON proceso.id = detalle_procesos.id_proceso
                        INNER JOIN lineas ON lineas.id = proceso.fk_id_linea
                        INNER JOIN eventos ON detalle_participantes.event_id = eventos.id
                     WHERE ");
         $sql_base_nom=trim("
                    SELECT eventos.id,eventos.name,participantes.id,participantes.id,participantes.tipo_doc,participantes.documento,participantes.pri_nombre,participantes.seg_nombre,participantes.pri_apellido,participantes.seg_apellido,participantes.edad,participantes.genero,participantes.escolaridad,participantes.zona,participantes.dep_nacimiento,participantes.ciud_nacimiento,participantes.municipio,detalle_participantes.updated_at,participantes.cap_dife,participantes.etnia FROM participantes 
                        INNER JOIN detalle_participantes ON detalle_participantes.user_id = participantes.documento 
                        INNER JOIN detalle_procesos ON detalle_procesos.id_usuario = participantes.documento
                        INNER JOIN proceso ON proceso.id = detalle_procesos.id_proceso
                        INNER JOIN lineas ON lineas.id = proceso.fk_id_linea
                        INNER JOIN eventos ON detalle_participantes.event_id = eventos.id
                     WHERE ");
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

                                if($key=="documento"){
                                    $sql.=" documento = '".$value."' AND";
                                    $sqldoc.=" documento = '".$value."'";
                                    //echo $sql_base_doc;
                                    
                                }
                                if($key=="pri_nombre"){
                                    ///echo( $key);
                                    $sql.=" pri_nombre LIKE '".$value."' OR seg_nombre LIKE '".$value."' OR pri_apellido LIKE '".$value."' OR seg_apellido = '".$value."' AND";

                                   $sqlnom.=" pri_nombre LIKE '".$value."' OR seg_nombre LIKE '".$value."' OR pri_apellido LIKE '".$value."' OR seg_apellido = '".$value."'";
                                   

                                }
                                if($key=="lineas.nombre_linea" && $value!=""){
                                    $sql_org.=" AND lineas.nombre_linea = '".$value."'";
                                }
                                if($key != "proceso.nombre_proceso" && $value!=""){
                                    $sql_pro.=" AND proceso.nombre_proceso = '".$value."'";
                                }
                                if($key!="documento" && $key != "id_evento" && $key != "pri_nombre" && $key != "lineas.nombre_linea" && $key != "proceso.nombre_proceso"){
                                    $sql.=" $key ='".$value."' AND";    
                                }
                            }
                    }
                 break;
             
             default:
                
                    $sql=" ";
                    $sql.= " eventos.id = '".$datos->datos->id_evento."' AND (";
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
                                //echo $key."<br>";
                                if($key=="documento"){
                                    $sql.=" documento = '".$value."' AND";
                                    $sqldoc.=" documento = '".$value."' AND eventos.id = '".$datos->datos->id_evento."'";
                                    
                                }
                                if($key=="pri_nombre"){
                                    $sql.=" pri_nombre LIKE '".$value."' OR seg_nombre LIKE '".$value."' OR pri_apellido LIKE '".$value."' OR seg_apellido = '".$value."' AND" ;
                                    
                                    $sqlnom.="(pri_nombre LIKE '".$value."' OR seg_nombre LIKE '".$value."' OR pri_apellido LIKE '".$value."' OR seg_apellido = '".$value."'".") AND eventos.id = '".$datos->datos->id_evento."'";
                                //echo $sql_base_nom.$sql_2 ;   
                                    
                                }
                                if($key=="lineas.nombre_linea" && $value!=""){
                                    $sql_org.=" AND lineas.nombre_linea = '".$value."'";
                                }
                                if($key != "proceso.nombre_proceso" && $value!=""){
                                    $sql_pro.=" AND proceso.nombre_proceso = '".$value."'";
                                }
                                if($key!="documento" && $key != "id_evento" && $key != "pri_nombre" && $key != "lineas.nombre_linea" && $key != "proceso.nombre_proceso"){
                                    $sql.=" $key ='".$value."' AND";    
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

      
        //EJECUTAR SENTENCIA
        switch ($datos->datos->id_evento) {
            case 'G':
                    if($sql!="" && $sql!=" " ){
                        $dagen=DB::select(trim($sql_base_genero.$sql_base_id.$sql.") GROUP BY genero")); 
                        
                        $daedad=DB::select(trim($sql_base_edad.$sql_base_id.$sql.") GROUP BY edad")); 
                        $dadepnac=DB::select(trim($sql_base_dep_nacimiento.$sql_base_id.$sql.") GROUP BY dep_nacimiento"));  
                        $daciunac=DB::select(trim($sql_base_ciud_nacimiento.$sql_base_id.$sql.") GROUP BY ciud_nacimiento")); 
                        $dacapdif=DB::select(trim($sql_base_cap_dife.$sql_base_id.$sql.") GROUP BY cap_dife"));   
                        $daetnia=DB::select(trim($sql_base_etnia.$sql_base_id.$sql.") GROUP BY etnia"));
                        $dasubetnia=DB::select(trim($sql_base_sub_etnia.$sql_base_id.$sql.") GROUP BY sub_etnia"));
                        $daescolaridad=DB::select(trim($sql_base_escolaridad.$sql_base_id.$sql.") GROUP BY escolaridad"));
                        $daorga=DB::select(trim($sql_base_linea_organizacion.$sql_base_id.$sql.")".$sql_org." GROUP BY lineas.id"));
                        $daproc=DB::select(trim($sql_base_proceso.$sql_base_id.$sql.")".$sql_pro." GROUP BY proceso.id")); 
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
                        
                        $daedad=DB::select(trim($sql_base_edad.$sql_base_id.$sql.")) GROUP BY edad")); 
                        $dadepnac=DB::select(trim($sql_base_dep_nacimiento.$sql_base_id.$sql.")) GROUP BY dep_nacimiento"));  
                        $daciunac=DB::select(trim($sql_base_ciud_nacimiento.$sql_base_id.$sql.")) GROUP BY ciud_nacimiento")); 
                        $dacapdif=DB::select(trim($sql_base_cap_dife.$sql_base_id.$sql.")) GROUP BY cap_dife"));   
                        $daetnia=DB::select(trim($sql_base_etnia.$sql_base_id.$sql.")) GROUP BY etnia"));
                        $dasubetnia=DB::select(trim($sql_base_sub_etnia.$sql_base_id.$sql.")) GROUP BY sub_etnia"));
                        $daescolaridad=DB::select(trim($sql_base_escolaridad.$sql_base_id.$sql.")) GROUP BY escolaridad"));
                        $daproc=DB::select(trim($sql_base_proceso.$sql_base_id.$sql.")) ".$sql_pro." GROUP BY proceso.id")); 
                        $datbleventos=DB::select(trim($sql_base_tbl_eventos." eventos.id = '".$datos->datos->id_evento."' GROUP BY eventos.id"));
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
               return array("mensaje"=>"REPORTE ".$datos->datos->id_evento,
                            "datos"=>$res,
                            "datos_genero"=>$dagen,
                            "datos_edaddes"=>$daedad,
                            "datos_dep_nac"=>$dadepnac,
                            "datos_ciu_nac"=>$daciunac,
                            "datos_cap_dife"=>$dacapdif,
                            "datos_etnia"=>$daetnia,
                            "datos_sub_etnia"=>$dasubetnia,
                            "datos_escolaridad"=>$daescolaridad,
                            "datos_organizacion"=>$daorga,
                            "datos_proceso"=>$daproc,
                            "datos_doc"=>$dadoc,
                            "datos_nom"=>$danom,
                            "sql"=>$sql_base_nom.$sqlnom." GROUP BY eventos.id,participantes.id",
                            "respuesta"=>true); 

        }else{
            return array("mensaje"=>"REPORTE ".$datos->datos->id_evento." sin datos que mostrar",
                    "respuesta"=>false); 
        }
    	
    	
    	
    }

    
}