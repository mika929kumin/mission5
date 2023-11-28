<?php 
    // DB接続設定
    $dsn = 'mysql:dbname=データベース名;host=localhost';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //データベース内にテーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS mission5tb"
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
             $sql = "INSERT INTO mission5tb (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)";
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
      $sql = 'SELECT * FROM mission5tb';
      $stmt = $pdo->query($sql);
      $results = $stmt->fetchAll();
      //パスワードエラー表示のための変数定義
      $message = "パスワードが間違っています。"."<br>";
      foreach ($results as $row){
         //***もしtxtの番号と投稿番号が一致した場合,パスワードも合っていたら                                        
             if($row['id'] == $delnum && $row['pass'] == $delpass){
                //入力したデータレコードの削除
                    $id = $delnum;
                    $sql = 'delete from mission5tb where id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                    $message = "削除成功です！"."<br>";
                        }
         }
    }

    
  
  
//編集番号が押されたら 
   //***編集番号の受信
   if(!empty($_POST["edinum"]) && isset($_POST["edipass"])){  
                  $sql = 'SELECT * FROM mission5tb';
                  $stmt = $pdo->query($sql);           
                  $results = $stmt->fetchAll();
                  $message = "パスワードが間違っています。"."<br>";
                  foreach ($results as $row){                 
                  //***番号とパスワードが正しければ、編集したい行の配列を変数に代入→この後HTMLで表示させる。                           
                  if($row['id']==$_POST["edinum"] && $row['pass']==$_POST["edipass"]){                                 
                  $editnum=$row['id'];
                  $editname=$row['name'];                                                                                            
                  $editcomment=$row['comment'];
                  $message = " ";
              }
            }
          }


//文字が編集されたら 
  //***もし名前・コメント・編集番号（非表示）が記載されていれば 
  if(!empty($_POST["num"]) && !empty($_POST["name"]) && !empty($_POST["comment"])){ 
      $id = $_POST["num"];
      $sql = 'SELECT * FROM mission5tb';
      $stmt = $pdo->query($sql);
      $results = $stmt->fetchAll();
      foreach ($results as $row){
            if($row['id'] == $id){
                //入力されているデータレコードの内容を編集
                $name = $_POST["name"];
                $comment = $_POST["comment"]; 
                $sql= 'UPDATE mission5tb SET name=:name,comment=:comment WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $message = "編集成功です！"."<br>";
  }
}
  }
  
  ?>

<html>
    <head>
       <meta charest="uft-8" />
       <title>mission5tb</title>
    </head>
    <body>
        <form action="" method="post">

            【投稿フォーム】<br>
            <input type="text" name="name" value="<?php if(!empty($_POST["edinum"])){
             echo $editname;} ?>" placeholder="名前"><br>
            <input type="text" name="comment" value="<?php if(!empty($_POST["edinum"])){
             echo $editcomment;} ?>" placeholder="コメント"><br>
            <input type="hidden" name="num" value="<?php  if(!empty($_POST["edinum"])){
             echo $editnum;} ?>" placeholder=" ">
            <input type="text" name="pass" placeholder="パスワード">
            <input type="submit" name="new" value="送信"><br><br>

             【削除フォーム】<br>
            <input type="number" name="delnum" placeholder="削除対象番号"><br>
            <input type="text" name="delpass" placeholder="パスワード">
            <input type="submit" name="del" value="削除"><br><br>

            【編集フォーム】<br>
            <input type="number" name="edinum" placeholder="編集対象番号"><br>
            <input type="text" name="edipass" placeholder="パスワード">
            <input type="submit" name="edi" value="編集"><br><br>

            ---------------------------------------------<br>
            <?php if(isset($_POST["new"]) && empty($_POST["num"])){
              if(empty($_POST["name"])){
                echo "エラー：名前が入力されていません。"."<br>";
                echo "---------------------------------------------"."<br>"."<br>";
              }elseif(empty($_POST["comment"])){
                echo "エラー：コメントが入力されていません。"."<br>";
                echo "---------------------------------------------"."<br>"."<br>";
              }elseif(empty($_POST["pass"])){
                echo "エラー：パスワードが入力されていません。"."<br>";
                echo "---------------------------------------------"."<br>"."<br>";
              }
            }
 

             if(isset($_POST["del"])){
              if(empty($_POST["delnum"])){
                echo "エラー：削除番号が入力されていません。"."<br>";
                echo "---------------------------------------------"."<br>"."<br>";
               }elseif(empty($_POST["delpass"])){
                echo "エラー：パスワードが入力されていません。"."<br>";
                echo "---------------------------------------------"."<br>"."<br>";
               }else{
                echo $message;
                echo "---------------------------------------------"."<br>"."<br>";
               }
             }

             if(isset($_POST["edi"])){
              if(empty($_POST["edinum"])){
                echo "エラー：編集番号が入力されていません。"."<br>";
                echo "---------------------------------------------"."<br>"."<br>";
               }elseif(empty($_POST["edipass"])){
                echo "エラー：パスワードが入力されていません。"."<br>";
                echo "---------------------------------------------"."<br>"."<br>";
               }else{
                echo $message;
                echo "---------------------------------------------"."<br>"."<br>";
               }
             }

             if(isset($_POST["new"]) && !empty($_POST["num"])){
              echo $message;
              echo "---------------------------------------------"."<br>"."<br>";
             }
 
            ?>
            【投稿一覧】<br>
        </form>
    </body>
</html>

<?php



      //入力したデータレコードを抽出、表示
      $sql = 'SELECT * FROM mission5tb';
      $stmt = $pdo->query($sql);
      $results = $stmt->fetchAll();
      foreach ($results as $row){
       //$rowの中にはテーブルのカラム名が入る
       echo $row['id'].',';
       echo $row['name'].',';
       echo $row['comment'].'<br>';
       echo "<hr>";
      }

  ?>
