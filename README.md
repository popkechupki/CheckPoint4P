# CheckPoint4P
アスレチックのチェックポイントを作成したい時に便利なプラグインです。  
※チェックポイントは再起動後に削除されます。

## 使い方
### チェックポイント作成
コンフィグで設定されたブロックをタッチする。（初期設定はエメラルドブロック）  
あるいはコマンド/addcpを実行する。（初期設定ではOPのみ可能）
### チェックポイントを削除
コマンド/delcpを実行する。
### チェックポイントに移動
コマンド/cpを実行する。

## 設定項目
コンフィグファイルより以下の設定が可能です。

|キー| 選択肢        | 説明                            |
|---|------------|-------------------------------|
|CheckPointBlock| ブロックID(数値) | タッチでチェックポイントを作成する際のブロック       |
|AllowAddCheckPointToAll| true/false | trueの場合はコマンドで誰でもチェックポイントが作成可能 |
|AllowDeleteCheckPointToAll| true/false | trueの場合はコマンドで誰でもチェックポイントを削除可能 |