<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>5-1</title>
</head>
<body>
<?php
// DB接続設定　dsnには空白を開けない PDOでMySQLサーバに接続する
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
//CREATE文：データベース内にテーブルを作成
$sql="CREATE TABLE IF NOT EXISTS tb51"
."("
."id INT AUTO_INCREMENT PRIMARY KEY,"
."name char (32),"
."comment TEXT,"
."ptime datetime,"
."password char(32)"
.");";
$stmt=$pdo->query($sql);
//テーブル一覧と現在のテーブルの構成詳細を表示
    $result=$pdo->query("SHOW TABLES");
    foreach ($result as $row){
        echo $row[0]." , ";
    }
    echo "<hr>";
    $sql ='SHOW CREATE TABLE tb51';
    $result = $pdo -> query($sql);
    foreach ($result as $row){
        echo $row[1];
    }
    echo "<hr>";

if(!empty($_POST["editnum"])){$editnum=($_POST["editnum"]);}
if(!empty($_POST["editpass"])){$editpass=($_POST["editpass"]);}

//投稿機能　「送信ボタンが押された場合」
if(isset($_POST["submit1"])){
    if(!empty($_POST["name"]) && !empty($_POST["str"]) && !empty($_POST["password"])){
    //名前・コメント・日時・パスを受け取り
    $name=$_POST["name"];
    $str=$_POST["str"];
    $password=$_POST["password"];
    echo "データ受け取り完了  ";
        //新規投稿　（投稿番号の受け取りはいらない？）
        if(empty($_POST["editnum2"])){
            //データベース書き込み cast(? as datetime)で文字列型からdatetime型に変換する
            $sql=$pdo->prepare("INSERT INTO tb51 (name,comment,ptime,password) values(:name,:comment,cast(now()as datetime),:password)");
            $sql->bindparam(":name",$name,PDO::PARAM_STR);
            $sql->bindParam(":comment",$str,PDO::PARAM_STR);
            $sql->bindParam(":password",$password,PDO::PARAM_STR);
            $sql->execute();
            echo "新規データのファイル書き込み完了";

        //編集
        }else{
            $editnum2=$_POST["editnum2"];
            $sql="UPDATE tb51 SET name=:name, comment=:comment, password=:password WHERE id=:id";
            $stmt=$pdo->prepare($sql);
            $stmt->bindParam(":name",$name,PDO::PARAM_STR);
            $stmt->bindParam(":comment",$str,PDO::PARAM_STR);
            $stmt->bindParam(":password",$password,PDO::PARAM_STR);
            $stmt->bindParam(":id",$editnum2,PDO::PARAM_STR);
            $stmt->execute();
            echo "編集データのファイル書き込み完了";
        }
    }else{
        echo "名前・コメント・パスワードを入力してください";
    }
//削除機能　「削除ボタンが押されたら」　削除行以外のデータを入力
}elseif(isset($_POST["submit2"])){
    //「番号とパスのフォームが埋まっているなら」
    if(!empty($_POST["delnum"]) && !empty($_POST["delpass"])){
        //入力された削除番号の投稿を取り出す
        $delnum=$_POST["delnum"];
        $delpass=$_POST["delpass"];
        echo "削除データ受け取り完了  ";
        $stmt=$pdo->prepare("SELECT * FROM tb51 where id=:id");
        $stmt->bindParam(":id",$delnum,PDO::PARAM_INT);
        $stmt->execute();
        $result=$stmt->fetch();
        //「パスが正しいなら」そのパスをフォームに入力されたパスと比較
        if($result["password"]==$delpass){
            //一致したらその行を削除
            $stmt=$pdo->prepare("DELETE FROM tb51 where id=:id");
            $stmt->bindparam(":id",$delnum,PDO::PARAM_INT);
            $stmt->execute();
            echo $delnum."番の投稿を削除しました";
        }else{
            echo "パスワードが違います";
        }
    }else{
        echo "削除番号とパスワードを入力してください";
    }
}

//投稿編集機能　「編集ボタンが押された時」
elseif(isset($_POST["submit3"])){
    if(!empty($_POST["editnum"]) && !empty($_POST["editpass"])){
        $stmt=$pdo->prepare("SELECT * FROM tb51 where id=:id");
        $stmt->bindParam(":id",$editnum,PDO::PARAM_INT);
        $stmt->execute();
        $result=$stmt->fetch();
        if($result["password"]==$editpass){
            echo "編集モード：".$editnum."番の投稿";
        }else{
            echo "パスワードが違います";
        }
    }else{
        echo "編集番号とパスワードを入力してください";
    }
}
?>

<form action="" method="post">
    <input type="text" name="name" placeholder="名前" value="<?php
    //投稿番号とパスが一致した時、その投稿の名前を取り出す
        if(isset($_POST["submit3"]) && !empty($_POST["editnum"]) && !empty($_POST["editpass"])){
            $stmt=$pdo->prepare("SELECT * FROM tb51 where id=:id");
            $stmt->bindParam(":id",$editnum,PDO::PARAM_INT);
            $stmt->execute();
            $result=$stmt->fetch();

            if($result["password"]==$editpass){
                echo $result["name"];
            }
        }
    ?>"><br>
    <input type="text" name="str" placeholder="コメント" value="<?php
        if(isset($_POST["submit3"]) && !empty($_POST["editnum"]) && !empty($_POST["editpass"])){
            $stmt=$pdo->prepare("SELECT * FROM tb51 where id=:id");
            $stmt->bindParam(":id",$editnum,PDO::PARAM_INT);
            $stmt->execute();
            $result=$stmt->fetch();

            if($result["password"]==$editpass){
                echo $result["comment"];
            }
        }
    ?>"><br>
    <input type="text" name="password" placeholder="パスワード" value="<?php
    //投稿番号とパスが一致した時、その投稿の名前を取り出す
        if(isset($_POST["submit3"]) && !empty($_POST["editnum"]) && !empty($_POST["editpass"])){
            $stmt=$pdo->prepare("SELECT * FROM tb51 where id=:id");
            $stmt->bindParam(":id",$editnum,PDO::PARAM_INT);
            $stmt->execute();
            $result=$stmt->fetch();

            if($result["password"]==$editpass){
                echo $result["password"];
            }
        }
    ?>">
    <input type="hidden" name="editnum2" value="<?php if(isset($_POST["submit3"]) && !empty($editnum)){echo $editnum;} ?>" >
    <input type="submit" name="submit1"> <br>
    <br>
    <input type="number" name="delnum" placeholder="削除対象番号"> <br>
    <input type="text" name="delpass" placeholder="パスワードを入力してください">
    <input type="submit" name="submit2"> <br>
    <br>
    <input type="number" name="editnum" placeholder="編集対象番号"> <br>
    <input type="text" name="editpass" placeholder="パスワードを入力してください">
    <input type="submit" name="submit3"> <br>
</form>

<?php //データベースの中身をブラウザに表示
echo "<hr>";
$stmt=$pdo->query("SELECT * FROM tb51");
$results=$stmt->fetchall();
foreach($results as $row){
    echo $row["id"]."  ".$row["name"]."  ".$row["comment"]."  ".$row["ptime"]."<br>";
}
echo "<hr>";
?>

    </body>
</html>