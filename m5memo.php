<?php
// DB接続設定
    $dsn = 'mysql:dbname=tb250462db;host=localhost';
    $user = 'tb-250462';
    $password = 'AMWuBaWpk6';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //データベース内にテーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS tbtest"
        ." ("
        //id ・自動で登録されているナンバリング
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        //name ・名前を入れる。文字列、半角英数で32文字
        . "name CHAR(32),"
        //comment ・コメントを入れる。文字列、長めの文章も入る
        . "comment TEXT"
        .");";
    $stmt = $pdo->query($sql);
    
    //データベース内にあるテーブル一覧を表示
    $sql ='SHOW TABLES';
    $result = $pdo -> query($sql);
    foreach ($result as $row){
        echo $row[0];
        echo '<br>';
    }
    echo "<hr>"; 

    //作成したテーブルの内容・構成詳細を確認
    $sql = 'SHOW CREATE TABLE tbtest';
    $result = $pdo -> query($sql);
    foreach ($result as $row){
     echo $row[1];
    }
    echo "<hr>";

    //データを入力
    $name = 'りま';
    $comment = 'NiziU3';
 
    $sql = "INSERT INTO tbtest (name, comment) VALUES (:name, :comment)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->execute();
 
    //入力したデータレコードを抽出子、表示
    $sql = 'SELECT * FROM tbtest';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
     //$rowの中にはテーブルのカラム名が入る
     echo $row['id'].',';
     echo $row['name'].',';
     echo $row['comment'].'<br>';
     echo "<hr>";
    }

    //入力されているデータレコードの内容を編集
    $id = 1; //変更する投稿番号
    $name = "みいひ";
    $comment = "NiziU"; //変更したい名前、変更したいコメントは自分で決める
    $sql= 'UPDATE tbtest SET name=:name,comment=:comment WHERE id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    //表示
    $sql = 'SELECT * FROM tbtest';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
      echo $row['id'].',';
      echo $row['name'].',';
      echo $row['comment'].'<br>';
      echo "<hr>";
    }

    //入力したデータレコードの削除
    $id = 2;
    $sql = 'delete from tbtest where id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    //表示
    $sql = 'SELECT * FROM tbtest';
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