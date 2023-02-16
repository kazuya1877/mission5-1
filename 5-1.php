<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
    <h1>好きな動物を教えてください!</h1>
    <?php
        // DB接続設定
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        //テーブル作成
        $sql = "CREATE TABLE IF NOT EXISTS mission5"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "date TEXT,"
        . "pass TEXT"
        .");";
        $stmt = $pdo->query($sql);
        //フォーム初期値
        $editnum0 = "編集対象番号";
        $editingnum0 = "";
        $name0 = "";
        $str0 = "";
        $pass10 = "";
        //フォームに入力した文字列の代入
        if(isset($_POST['name']) && isset($_POST['str']))
        {
            $name = $_POST["name"];
            $str = $_POST["str"];
            $delnum = $_POST["delnum"];
            $editnum = $_POST["editnum"];
            $editingnum = $_POST["editingnum"];
            $pass1 = $_POST["pass1"];
            $pass2 = $_POST["pass2"];
            $pass3 = $_POST["pass3"];
        }
        //編集ボタンを押し、編集番号が空白でないとき
        if(isset($_POST['edit']) && $editnum !== "")
        {
            //編集番号の投稿を抽出
            $id = $editnum; 
            $sql = 'SELECT * FROM mission5 WHERE id=:id ';
            $stmt = $pdo->prepare($sql);                  
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
            $stmt->execute();                             
            $results = $stmt->fetchAll(); 
            foreach ($results as $row)
            {
                //パスワードが正しいとき
                if($pass3 == $row['pass'])
                {
                    //フォーム初期値を変更
                    $editingnum0 = $row['id'];
                    $editnum0 = $row['id'];
                    $name0 = $row['name'];
                    $str0 = $row['comment'];
                    $pass10 = $row['pass'];
                }
            }
        }
    ?>
        <!--入力フォームの作成-->
    <form action="" method="post">
        [投稿フォーム]<br>
        名前：<br><input type="text" name="name" value="<?php echo $name0; ?>"><br>
        コメント：<br><input type="text" name="str" value="<?php echo $str0; ?>"><br>
        パスワード：<br><input type="password" name="pass1" value="<?php echo $pass10; ?>"><br>
        <input type="submit" name="submit"><br>
        [削除フォーム]<br>
        投稿番号：<br><input type="number" name="delnum" value="削除対象番号"><br>
        パスワード：<br><input type="password" name="pass2"><br>
        <input type="submit" name="del" value="削除"><br>
        [編集フォーム]<br>
        投稿番号：<br><input type="number" name="editnum" value="<?php echo $editnum0; ?>"><br>
        パスワード：<br><input type="password" name="pass3"><br>
        <input type="submit" name="edit" value="編集"><br>
        <input type="hidden" name="editingnum" value="<?php echo $editingnum0; ?>"><br>
    </form>
    <?php
        //編集ボタンを押したとき
        if(isset($_POST['edit']))
        {
            //編集番号が空白でないとき
            if($editnum !== "")
            {
                //編集番号の投稿を抽出
                $id = $editnum; 
                $sql = 'SELECT * FROM mission5 WHERE id=:id ';
                $stmt = $pdo->prepare($sql);                  
                $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
                $stmt->execute();                             
                $results = $stmt->fetchAll(); 
                foreach ($results as $row)
                {
                    //パスワードが正しいとき
                    if($pass3 == $row['pass'])
                    {
                        echo "[動作]<br>";
                        echo "投稿番号".$editnum."を編集中です。<br>";
                    }
                    //パスワードを間違えているとき
                    elseif($pass3 != $row['pass'])
                    {
                        echo "[エラー]<br>";
                        echo "パスワードが間違っています。<br>";
                    }
                }
            }
            //編集番号が空白のとき
            else
            {
                echo "[エラー]<br>";
                echo "編集番号を入力してください。<br>";
            }
            //投稿内容の表示
            echo "[投稿一覧]<br>";
            $sql = 'SELECT * FROM mission5';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row)
            {
                echo $row['id'].',';
                echo $row['name'].',';
                echo $row['comment'].',';
                echo $row['date'].'<br>';
            }
        }
        //変数の定義、date関数による文字列の代入
        $date = date("Y年m月d日 H時i分s秒");
        //送信ボタンを押したとき
        if(isset($_POST['submit']))
        {
            //コメント、名前、パスワードが空白でないとき
            if($str !== "" && $name !== "" && $pass1 != "")
            {
                //編集中のとき
                if($editingnum != $editingnum0)
                {
                    //レコードを編集
                    $id = $editingnum; 
                    $sql = 'UPDATE mission5 SET name=:name,comment=:comment,date=:date,pass=:pass WHERE id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':comment', $str, PDO::PARAM_STR);
                    $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
                    $stmt -> bindParam(':pass', $pass1, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                    //表示
                    echo "[動作]<br>";
                    echo "投稿番号".$editingnum."を編集しました。<br>";
                }
                //新規投稿のとき
                else
                {
                    //レコードの挿入
                    $sql = $pdo -> prepare("INSERT INTO mission5 (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
                    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                    $sql -> bindParam(':comment', $str, PDO::PARAM_STR);
                    $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                    $sql -> bindParam(':pass', $pass1, PDO::PARAM_STR);
                    $sql -> execute();
                    //文字の表示
                    echo "[動作]<br>";
                    echo "書き込みました。<br>";
                }
                //投稿内容の表示
                echo "[投稿一覧]<br>";
                $sql = 'SELECT * FROM mission5';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                foreach ($results as $row)
                {
                    echo $row['id'].',';
                    echo $row['name'].',';
                    echo $row['comment'].',';
                    echo $row['date'].'<br>';
                }
            }
            else
            {
                echo "[エラー]<br>";
                if($name == "")
                {
                    echo "名前を入力してください。<br>";
                }
                if($str == "")
                {
                    echo"コメントを入力してください。<br>";
                }
                if($pass1 == "")
                {
                    echo "パスワードを設定してください。<br>";
                }
                //投稿内容の表示
                echo "[投稿一覧]<br>";
                $sql = 'SELECT * FROM mission5';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                foreach ($results as $row)
                {
                    echo $row['id'].',';
                    echo $row['name'].',';
                    echo $row['comment'].',';
                    echo $row['date'].'<br>';
                }
            }
        }
        //削除ボタンを押したとき
        elseif(isset($_POST['del']))
        {
            //削除番号が空白でないとき
            if($delnum !== "")
            {
                //編集番号の投稿を抽出
                $id = $delnum; 
                $sql = 'SELECT * FROM mission5 WHERE id=:id ';
                $stmt = $pdo->prepare($sql);                  
                $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
                $stmt->execute();                             
                $results = $stmt->fetchAll(); 
                foreach ($results as $row)
                {
                    //パスワードが正しいとき
                    if($pass2 == $row['pass'])
                    {
                        //表示
                        echo "[動作]<br>";
                        echo "投稿番号".$delnum."を削除しました。<br>";
                        //投稿番号のレコードを削除
                        $id = $delnum;
                        $sql = 'delete from mission5 where id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt->execute();
    
                    }
                    //パスワードが間違っているとき
                    elseif($pass2 != $row['pass'])
                    {
                        echo "[エラー]<br>";
                        echo "パスワードが間違っています。<br>";
                    }
                }
            }
            //削除番号が空白のとき
            else
            {
                echo "[エラー]<br>";
                echo "削除番号を入力してください。<br>";
            }
            //投稿内容の表示
            echo "[投稿一覧]<br>";
            $sql = 'SELECT * FROM mission5';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row)
            {
                echo $row['id'].',';
                echo $row['name'].',';
                echo $row['comment'].',';
                echo $row['date'].'<br>';
            }
        }
        //ボタンを押していないとき（初期状態）の表示
        elseif(!isset($_POST['submit']) && !isset($_POST['del']) && !isset($_POST['edit']))
        {
            //投稿内容の表示
            echo "[投稿一覧]<br>";
            $sql = 'SELECT * FROM mission5';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row)
            {
                echo $row['id'].',';
                echo $row['name'].',';
                echo $row['comment'].',';
                echo $row['date'].'<br>';
            }
        }
    ?>
</body>
</html>
</html>