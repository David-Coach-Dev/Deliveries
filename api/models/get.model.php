<?php
  require_once "connection.php";
  class GetModel{
    /*******************************
    ** Petición Get sin filtro.
    ********************************/
      static public function getData($table, $select, $orderBy,$orderMode, $startAt, $endAt){
        /***********************************
        ** validar exigencia de la tabla
        ************************************/
        $selectArray = explode(",", $select);
        if (empty(Connection::getColumnsData($table, $selectArray))) {
          return null;
        }
        /*******************************
        *? Sin orden sin limites
        ********************************/
        $sql= "SELECT $select FROM $table";
        /*********************************
        *? Con orden sin limites
        **********************************/
        if($orderBy!=null && $orderMode!=null && $startAt==null && $endAt==null){
          $sql = "SELECT $select FROM $table ORDER BY $orderBy $orderMode";
        }
        /*********************************
        *? Sin orden con limites
        **********************************/
        if ($orderBy==null && $orderMode==null && $startAt!=null && $endAt!=null) {
          $sql = "SELECT $select FROM $table LIMIT $startAt, $endAt";
        }
        /*******************************
        *? Con orden con limites
        ********************************/
        if ($orderBy!=null && $orderMode!=null && $startAt!=null && $endAt!=null) {
          $sql = "SELECT $select FROM $table ORDER BY $orderBy $orderMode LIMIT $startAt, $endAt";
        }
        $stmt = Connection::connect()->prepare($sql);
        /*******************************
        *? Ejecutar sentencia sql.
        ********************************/
        try {
          $stmt->execute();
        } catch (PDOException $Exception) {
          return null;
        }
          return $stmt->fetchAll(PDO::FETCH_CLASS);
      }
    /*******************************
    ** Petición Get con filtro.
    ********************************/
      static public function getDataFilter($table, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt){
        /***********************************
         *? validar exigencia de la tabla
        ************************************/
        $linkToArray = explode(",",$linkTo);
        $selectArray = explode(",", $select);
        foreach($linkToArray as $key => $value){
          array_push($selectArray, $value);
        }
        $selectArray=array_unique($selectArray);
        if (empty(Connection::getColumnsData($table, $selectArray))) {
          return null;
        }
        $equalToArray = explode("_",$equalTo);
        $linkToText="";
        if(count($linkToArray)>1){
          foreach ($linkToArray as $key => $value) {
            if($key >0){
              $linkToText.="AND ".$value." = :".$value." ";
            }
          }
        }
        // echo'<pre>';print_r($linkToArray); echo'</pre>';
        // echo'<pre>';print_r($equalToArray); echo'</pre>';
        // echo'<pre>';print_r($linkToText); echo'</pre>';
        // $sql="SELECT $select FROM $table WHERE $linkToArray[0] = :$linkToArray[0] $linkToText";
        // $stmt = Connection::connect()->prepare($sql);
        // foreach ($linkToArray as $key => $value) {
        //   $stmt -> bindParam(":".$value,$equalToArray[$key],PDO::PARAM_STR);
        // }
        // echo'<pre>';print_r($sql); echo'</pre>';
        // echo'<pre>';print_r($stmt); echo'</pre>';
        // return;
        /**************************************
        *? Con filtro sin orden sin limites
        ***************************************/
        if ($orderBy==null && $orderMode==null && $startAt==null && $endAt==null){
          $sql="SELECT $select FROM $table WHERE $linkToArray[0] = :$linkToArray[0] $linkToText";
        }
        /***************************************
        *? Con filtro con orden
        ****************************************/
        if ($orderBy!=null && $orderMode!=null && $startAt==null && $endAt==null){
          $sql="SELECT $select FROM $table WHERE $linkToArray[0] = :$linkToArray[0] $linkToText ORDER BY $orderBy $orderMode";
        }
        /*******************************************
         *? Con filtro con limites
        *******************************************/
        if ($orderBy==null && $orderMode==null && $startAt!=null && $endAt!=null) {
          $sql="SELECT $select FROM $table WHERE $linkToArray[0] = :$linkToArray[0] $linkToText LIMIT $startAt, $endAt";
        }
        /*******************************************
         *? Con filtro con orden con limites
        *******************************************/
        if ($orderBy!=null && $orderMode!=null && $startAt!=null && $endAt!=null) {
          $sql="SELECT $select FROM $table WHERE $linkToArray[0]=:$linkToArray[0] $linkToText ORDER BY $orderBy $orderMode LIMIT $startAt, $endAt";
        }
        $stmt = Connection::connect()->prepare($sql);
        foreach ($linkToArray as $key => $value){
          $stmt -> bindParam(":".$value, $equalToArray[$key], PDO::PARAM_STR);
        }
        try{
          $stmt->execute();
        }catch(PDOException $Exception){
          return null;
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS);
      }
    /************************************************************
     ** Peticiones Get sin filtros entre tablas relacionadas.
    *************************************************************/
    static public function getRelData($rel, $type, $select, $orderBy, $orderMode, $startAt, $endAt){
      $relToArray = explode(",", $rel);
      $typeToArray = explode(",", $type);
      $innerJoinToText="";
      if (count($relToArray) > 1) {
        foreach ($relToArray as $key => $value) {
          if ($key > 0) {
            $innerJoinToText.="INNER JOIN ".$value." ON ".$relToArray[0].".id_".$typeToArray[$key]."_".$typeToArray[0]." = ".$value.".id_".$typeToArray[$key]." ";
          }
        }
        /***********************************
        *? Sin limitar ni ordenar datos
        **********************************/
        $sql="SELECT $select FROM $relToArray[0] $innerJoinToText";
        /*********************************
        *? Ordenar datos sin limitar
        **********************************/
        if($orderBy!=null && $orderMode!=null && $startAt==null && $endAt==null){
          $sql="SELECT $select FROM $relToArray[0] $innerJoinToText ORDER BY $orderBy $orderMode";
        }
        /*********************************
        *? Ordenar y limitar datos
        **********************************/
        if ($orderBy!=null && $orderMode!=null && $startAt!=null && $endAt!=null) {
          $sql="SELECT $select FROM $relToArray[0] $innerJoinToText ORDER BY $orderBy $orderMode LIMIT $startAt, $endAt";
        }
        /*******************************
        *? limitar datos sin ordenar
        ********************************/
        if ($orderBy==null && $orderMode==null && $startAt!=null && $endAt!=null) {
          $sql="SELECT $select FROM $relToArray[0] $innerJoinToText LIMIT $startAt, $endAt";
        }
        $stmt = Connection::connect()->prepare($sql);
      try {
        $stmt->execute();
      } catch (PDOException $Exception) {
        return null;
      }
        return $stmt->fetchAll(PDO::FETCH_CLASS);
      }else{
        return null;
      }
    }
    /************************************************************
     ** Peticiones Get con filtros en tablas relacionadas.
    *************************************************************/
    static public function getRelDataFilter($rel, $type, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt){
      /*******************************
      *? Organización de relaciones
      ********************************/
      $relToArray = explode(",", $rel);
      $typeToArray = explode(",", $type);
      $innerJoinToText="";
      if (count($relToArray) > 1) {
        foreach ($relToArray as $key => $value) {
          /***********************************
           ** validar exigencia de la tabla
          ************************************/
          if (empty(Connection::getColumnsData($value))) {
            return null;
          }
          if ($key > 0) {
            $innerJoinToText.="INNER JOIN ".$value." ON ".$relToArray[0].".id_".$typeToArray[$key]."_".$typeToArray[0]." = ".$value.".id_".$typeToArray[$key]." ";
          }
        }
        /*******************************
        *? Organización de filtros
        ********************************/
        $linkToArray = explode(",", $linkTo);
        $equalToArray = explode("_", $equalTo);
        $filterToText = "";
        if (count($linkToArray) > 1) {
          foreach ($linkToArray as $key => $value) {
            if ($key > 0) {
              $filterToText.="AND ".$value."=:".$value." ";
            }
          }
        }
        /***********************************
        *? Sin limitar ni ordenar datos
        **********************************/
        $sql="SELECT $select FROM $relToArray[0] $innerJoinToText WHERE $linkToArray[0]=:$linkToArray[0] $filterToText";
        /*********************************
        *? Ordenar datos sin limitar
        **********************************/
        if($orderBy!=null && $orderMode!=null && $startAt==null && $endAt==null){
          $sql="SELECT $select FROM $relToArray[0] $innerJoinToText WHERE $linkToArray[0]=:$linkToArray[0] $filterToText ORDER BY $orderBy $orderMode";
        }
        /*********************************
        *? Ordenar y limitar datos
        **********************************/
        if ($orderBy!=null && $orderMode!=null && $startAt!=null && $endAt!=null) {
          $sql="SELECT $select FROM $relToArray[0] $innerJoinToText WHERE $linkToArray[0]=:$linkToArray[0] $filterToText ORDER BY $orderBy $orderMode LIMIT $startAt, $endAt";
        }
        /*******************************
        *? limitar datos sin ordenar
        ********************************/
        if ($orderBy==null && $orderMode==null && $startAt!=null && $endAt!=null) {
          $sql="SELECT $select FROM $relToArray[0] $innerJoinToText WHERE $linkToArray[0]=:$linkToArray[0] $filterToText LIMIT $startAt, $endAt";
        }
        $stmt = Connection::connect()->prepare($sql);
        foreach ($linkToArray as $key => $value){
          $stmt->bindParam(":".$value,$equalToArray[$key],PDO::PARAM_STR);
        }
      try {
        $stmt->execute();
      } catch (PDOException $Exception) {
        return null;
      }
        return $stmt->fetchAll(PDO::FETCH_CLASS);
      }else{
        return null;
      }
    }
    /*******************************************************
    ** Peticiones Get para buscadores sin relaciones.
    ********************************************************/
    static public function getDataSearch($table, $select, $linkTo, $searchTo, $orderBy, $orderMode, $startAt, $endAt){
      /*************************************************
       ** validar exigencia de la tabla y columnas
      **************************************************/
      $selectArray=explode(",", $select);
      if (empty(Connection::getColumnsData($table, $selectArray))) {
        return null;
      }
      $linkToArray = explode(",", $linkTo);
      $searchToArray = explode("_", $searchTo);
      $searchToText = "";
      if (count($linkToArray) > 1) {
        foreach ($linkToArray as $key => $value) {
          if ($key > 0) {
            $searchToText .= "AND " . $value . "=:" . $value . " ";
          }
        }
      }
      /*******************************
       *? Sin limitar ni ordenar datos
        ********************************/
      $sql = "SELECT $select FROM $table WHERE $linkToArray[0] LIKE '%$searchToArray[0]%' $searchToText";
      /*********************************
       *? Ordenar datos sin limitar
        **********************************/
      if ($orderBy != null && $orderMode != null && $startAt == null && $endAt == null) {
        $sql = "SELECT $select FROM $table  WHERE $linkToArray[0] LIKE '%$searchToArray[0]%' $searchToText ORDER BY $orderBy $orderMode";
      }
      /*********************************
       *? Ordenar y limitar datos
        **********************************/
      if ($orderBy != null && $orderMode != null && $startAt != null && $endAt != null) {
        $sql = "SELECT $select FROM $table  WHERE $linkToArray[0] LIKE '%$searchToArray[0]%' $searchToText ORDER BY $orderBy $orderMode LIMIT $startAt, $endAt";
      }
      /*******************************
       *? limitar datos sin ordenar
        ********************************/
      if ($orderBy == null && $orderMode == null && $startAt != null && $endAt != null) {
        $sql = "SELECT $select FROM $table  WHERE $linkToArray[0] LIKE '%$searchToArray[0]%' $searchToText LIMIT $startAt, $endAt";
      }
      $stmt = Connection::connect()->prepare($sql);
      foreach ($linkToArray as $key => $value) {
        if($key>0){
          $stmt->bindParam(":".$value, $searchToArray[$key], PDO::PARAM_STR);
        }
      }
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_CLASS);
    }
    /************************************************************
     ** Peticiones Get para buscadores en tablas relacionadas.
    *************************************************************/
    static public function getRelDataSearch($rel, $type, $select, $linkTo, $searchTo, $orderBy, $orderMode, $startAt, $endAt){
      /*******************************
      *? Organización de relaciones
      ********************************/
      $relToArray = explode(",", $rel);
      $typeToArray = explode(",", $type);
      $innerJoinToText = "";
      if (count($relToArray) > 1) {
        foreach ($relToArray as $key => $value) {
          /***********************************
           ** validar exigencia de la tabla
          ************************************/
          if (empty(Connection::getColumnsData($value))) {
            return null;
          }
          if ($key > 0) {
            $innerJoinToText .= "INNER JOIN ".$value." ON ".$relToArray[0].".id_".$typeToArray[$key]."_".$typeToArray[0]." = ".$value.".id_".$typeToArray[$key]." ";
          }
        }
        /*******************************
        *? Organización de búsqueda
        ********************************/
        $linkToArray = explode(",", $linkTo);
        $searchToArray = explode("_", $searchTo);
        $searchToText = "";
        if (count($linkToArray) > 1) {
          foreach ($linkToArray as $key => $value) {
            if ($key > 0) {
              $searchToText.="AND ".$value."=:".$value." ";
            }
          }
        }
        /***********************************
         *? Sin limitar ni ordenar datos
        **********************************/
        $sql = "SELECT $select FROM $relToArray[0] $innerJoinToText WHERE $linkToArray[0] LIKE '%$searchToArray[0]%' $searchToText";
        /*********************************
         *? Ordenar datos sin limitar
        **********************************/
        if ($orderBy != null && $orderMode != null && $startAt == null && $endAt == null) {
          $sql="SELECT $select FROM $relToArray[0] $innerJoinToText WHERE $linkToArray[0] LIKE '%$searchToArray[0]%' $searchToText ORDER BY $orderBy $orderMode";
        }
        /*********************************
         *? Ordenar y limitar datos
        **********************************/
        if ($orderBy != null && $orderMode != null && $startAt != null && $endAt != null) {
          $sql = "SELECT $select FROM $relToArray[0] $innerJoinToText WHERE $linkToArray[0] LIKE '%$searchToArray[0]%' $searchToText ORDER BY $orderBy $orderMode LIMIT $startAt, $endAt";
        }
        /*******************************
         *? limitar datos sin ordenar
        ********************************/
        if ($orderBy == null && $orderMode == null && $startAt != null && $endAt != null) {
          $sql = "SELECT $select FROM $relToArray[0] $innerJoinToText WHERE $linkToArray[0] LIKE '%$searchToArray[0]%' $searchToText LIMIT $startAt, $endAt";
        }
        $stmt = Connection::connect()->prepare($sql);
        foreach ($linkToArray as $key => $value) {
          if($key>0){
            $stmt->bindParam(":".$value, $searchToArray[$key], PDO::PARAM_STR);
          }
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS);
      } else {
        return null;
      }
    }
    /************************************************************
     ** Peticiones Get  para selección de rangos.
    *************************************************************/
    static public function getDataRange($table, $select, $linkTo, $betweenIn, $betweenOut, $orderBy, $orderMode, $startAt,$endAt, $filterTo, $inTo){
      /***********************************
       ** validar exigencia de la tabla
      ************************************/
      if (empty(Connection::getColumnsData($table))) {
        return null;
      }
      $filToText="";
      if($filterTo!=null && $inTo!=null){
        $filToText='AND '.$filterTo.' IN ('.$inTo.')';
      }
      /*******************************
       *? Sin limitar ni ordenar datos
      ********************************/
      $sql = "SELECT $select FROM $table WHERE $linkTo BETWEEN '$betweenIn' AND '$betweenOut' $filToText";
      /*********************************
       *? Ordenar datos sin limitar
      **********************************/
      if ($orderBy != null && $orderMode != null && $startAt == null && $endAt == null) {
        $sql = "SELECT $select FROM $table WHERE $linkTo BETWEEN '$betweenIn' AND '$betweenOut' $filToText ORDER BY $orderBy $orderMode";
      }
      /*********************************
       *? Ordenar y limitar datos
      **********************************/
      if ($orderBy != null && $orderMode != null && $startAt != null && $endAt != null) {
        $sql = "SELECT $select FROM $table WHERE $linkTo BETWEEN '$betweenIn' AND '$betweenOut' $filToText ORDER BY $orderBy $orderMode LIMIT $startAt, $endAt";
      }
      /*******************************
       *? limitar datos sin ordenar
      ********************************/
      if ($orderBy == null && $orderMode == null && $startAt != null && $endAt != null) {
        $sql = "SELECT $select FROM $table WHERE $linkTo BETWEEN '$betweenIn' AND '$betweenOut' $filToText LIMIT $startAt, $endAt";
      }
      $stmt = Connection::connect()->prepare($sql);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_CLASS);
    }
    /************************************************************
     ** Peticiones Get  para selección de rangos.
    *************************************************************/
    static public function getRelDataRange($rel, $type, $select, $linkTo, $betweenIn, $betweenOut, $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo){
      /*******************************
       *? Organización de relaciones
       ********************************/
      $relToArray = explode(",", $rel);
      $typeToArray = explode(",", $type);
      $innerJoinToText = "";
      if (count($relToArray) > 1) {
        foreach ($relToArray as $key => $value) {
          /***********************************
           ** validar exigencia de la tabla
          ************************************/
          if (empty(Connection::getColumnsData($value))){
            return null;
          }
          if ($key > 0) {
            $innerJoinToText.="INNER JOIN ".$value." ON ".$relToArray[0].".id_".$typeToArray[$key]."_".$typeToArray[0]." = ".$value.".id_".$typeToArray[$key]." ";
          }
        }
        /*******************************
         *? filtro del Between
         ********************************/
          $filToText = "";
          if ($filterTo != null && $inTo != null) {
            $filToText = 'AND ' . $filterTo . ' IN (' . $inTo . ')';
          }
        /*******************************
         *? Sin limitar ni ordenar datos
        ********************************/
        $sql = "SELECT $select FROM $relToArray[0] $innerJoinToText WHERE $linkTo BETWEEN '$betweenIn' AND '$betweenOut' $filToText";
        /*********************************
         *? Ordenar datos sin limitar
        **********************************/
        if ($orderBy != null && $orderMode != null && $startAt == null && $endAt == null) {
          $sql = "SELECT $select FROM $relToArray[0] $innerJoinToText WHERE $linkTo BETWEEN '$betweenIn' AND '$betweenOut' $filToText ORDER BY $orderBy $orderMode";
        }
        /*********************************
         *? Ordenar y limitar datos
        **********************************/
        if ($orderBy != null && $orderMode != null && $startAt != null && $endAt != null) {
          $sql = "SELECT $select FROM $relToArray[0] $innerJoinToText WHERE $linkTo BETWEEN '$betweenIn' AND '$betweenOut' $filToText ORDER BY $orderBy $orderMode LIMIT $startAt, $endAt";
        }
        /*******************************
         *? limitar datos sin ordenar
        ********************************/
        if ($orderBy == null && $orderMode == null && $startAt != null && $endAt != null) {
          $sql = "SELECT $select FROM $relToArray[0] $innerJoinToText WHERE $linkTo BETWEEN '$betweenIn' AND '$betweenOut' $filToText LIMIT $startAt, $endAt";
        }
        $stmt = Connection::connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS);
      }else{
        return null;
      }
    }
  }
?>