<?php 
    // DB接続設定
    $dsn = 'mysql:dbname=tb250462db;host=localhost';
    $user = 'tb-250462';
    $password = 'AMWuBaWpk6';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //データベース内にテーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS mission51"
        ." ("
        //id ・自動で登録されているナンバリング
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        //name ・名前を入れる。文字列、半角英数で32文字
        . "name CHAR(32),"
        //comment ・コメントを入れる。文字列、長めの文章も入る
        . "comment TEXT,"
        //date
        . "date DATETIME,"
        //pass
        . "pass CHAR(8)"
        .");";
    $stmt = $pdo->query($sql);
    
      //作成したテーブルの内容・構成詳細を確認
       $sql = 'SHOW CREATE TABLE tbtest';
       $result = $pdo -> query($sql);

       
//新規投稿
  //もしも「名前」と「コメント」両方に投稿がある&編集番号はない場合【新規投稿】
  if(!empty($_POST["name"]) && !empty($_POST["comment"]) && empty($_POST["num"]) && !empty($_POST["pass"])){
      $name=$_POST["name"];                                                     
      $comment=$_POST["comment"];
      $date = date ( "Y-m-d H:i:s" ); 
      $pass=$_POST["pass"];
              //データを入力
             $sql = "INSERT INTO mission51 (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)";
             $stmt = $pdo->prepare($sql);
            //  $stmt->bindParam(':id', $id, PDO::PARAM_STR);
             $stmt->bindParam(':name', $name, PDO::PARAM_STR);
             $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
             $stmt->bindParam(':date', $date, PDO::PARAM_STR);
             $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
             $stmt->execute();
    }
                 

//削除処理
  //***もし削除番号を受信したら
  if(!empty($_POST["delnum"]) && isset($_POST["delpass"])){ 
    //投稿番号・パスワードを変数にする                                                
      $delnum=$_POST["delnum"];                                                 
      $delpass=$_POST["delpass"]; 
      $sql = 'SELECT * FROM mission51';
      $stmt = $pdo->query($sql);
      $results = $stmt->fetchAll();
      foreach ($results as $row){
         //***もしtxtの番号と投稿番号が一致した場合,パスワードも合っていたら                                        
             if($row['id'] == $delnum && $row['pass'] == $delpass){
                //入力したデータレコードの削除
                    $id = $delnum;
                    $sql = 'delete from mission51 where id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                         }
         }
    }
    
  
  
//編集番号が押されたら 
   //***編集番号の受信
   if(!empty($_POST["edinum"]) && isset($_POST["edipass"])){                                             
                  $sql = 'SELECT * FROM mission51';
                  $stmt = $pdo->query($sql);           
                  $results = $stmt->fetchAll();
                  foreach ($results as $row){                 
                  //***番号とパスワードが正しければ、編集したい行の配列を変数に代入→この後HTMLで表示させる。                           
                  if($row['id']==$_POST["edinum"] && $row['pass']==$_POST["edipass"]){                                 
                  $editnum=$row['id'];
                  $editname=$row['name'];                                                                                            
                  $editcomment=$row['comment'];
               }
      }  
  }


//文字が編集されたら 
  //***もし名前・コメント・編集番号（非表示）が記載されていれば 
  if(!empty($_POST["num"]) && !empty($_POST["name"]) && !empty($_POST["comment"])){ 
     $id = $_POST["num"];
      $sql = 'SELECT * FROM mission51';
      $stmt = $pdo->query($sql);
      $results = $stmt->fetchAll();
      foreach ($results as $row){
            if($row['id'] == $id){
                //入力されているデータレコードの内容を編集
                $name = $_POST["name"];
                $comment = $_POST["comment"]; //変更したい名前、変更したいコメントは自分で決める
                $sql= 'UPDATE mission51 SET name=:name,comment=:comment WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
  }
}
  }
  
  ?>


<html>
    <head>
       <meta charest="uft-8" />
       <title>mission3-5</title>
    </head>
    <body>
        <form action="" method="post">
            <input type="text" name="name" value="<?php if(!empty($_POST["edinum"])){
             echo $editname;} ?>" placeholder="名前"><br>
            <input type="text" name="comment" value="<?php if(!empty($_POST["edinum"])){
             echo $editcomment;} ?>" placeholder="コメント"><br>
            <input type="hidden" name="num" value="<?php  if(!empty($_POST["edinum"])){
             echo $editnum;} ?>" placeholder=" ">
            <input type="text" name="pass" placeholder="パスワード">
            <input type="submit" name="submit" value="送信"><br><br>
            
            <input type="number" name="delnum" placeholder="削除対象番号"><br>
            <input type="text" name="delpass" placeholder="パスワード">
            <input type="submit" name="submit" value="削除"><br><br>
            
            <input type="number" name="edinum" placeholder="編集対象番号"><br>
            <input type="text" name="edipass" placeholder="パスワード">
            <input type="submit" name="submit" value="編集"><br><br>
        </form>
    </body>
</html>


<?php

//ファイル内の文字をブラウザに表示(常)→DB内のテーブル一覧表示
  //データベース内にあるテーブル一覧を表示
  $sql ='SHOW TABLES';
  $result = $pdo -> query($sql);
  foreach ($result as $row){
      echo $row[0];
      echo '<br>';
  }
  echo "<hr>"; 

      //入力したデータレコードを抽出、表示
      $sql = 'SELECT * FROM mission51';
      $stmt = $pdo->query($sql);
      $results = $stmt->fetchAll();
      foreach ($results as $row){
       //$rowの中にはテーブルのカラム名が入る
       echo $row['id'].',';
       echo $row['name'].',';
       echo $row['comment'].'<br>';
       //echo $row['date'].',';
       //echo $row['pass'].'<br>';
       echo "<hr>";
      }

  ?>
