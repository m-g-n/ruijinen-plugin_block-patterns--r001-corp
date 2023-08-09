[![Create Release](https://github.com/m-g-n/ruijinen-plugin_block-patterns--r002-corp/actions/workflows/release.yml/badge.svg)](https://github.com/m-g-n/ruijinen-plugin_block-patterns--r002-corp/actions/workflows/release.yml)

# 類人猿ブロックパターン：企業サイト向けパターン集
WordPressテーマ Snow Monkeyを拡張する「類人猿ブロックパターンプラグイン」のコーポレートサイト向けパターン集アドオンです

# SCSSのコンパイル方法
- npm ci でpackegeをインストール
- npm run watch でSCSSファイルの修正を常時監視（SCSSを修正したら即時CSSにコンパイルしてくれる）
- npm run build でCSSにコンパイル（コマンド走ったときだけCSSをコンパイル）

# comporserの準備
- composer install でパッケージをインストール

# wp-envによる開発環境の立ち上げ・終了
- `npm run start`で開発環境が立ち上がります
- `npm run stop`でテーマ・SQLをエクスポートし、開発環境が止まります
- なにかトラブルがあって、前終了した状態に戻したい場合は`npm run import`を実行ください

## その他
- 初期状態ではSnow Monkeyテーマは入っていません。手動でインストールください
- wp-envのことについては公式ハンドブックをご覧ください
https://ja.wordpress.org/team/handbook/block-editor/reference-guides/packages/packages-env/

# 変更履歴
## 3.2.2
- 一部CSS記述の調整
- yarnからnpmへ変更
## 3.2.1
- バージョン番号の書き方で不備があり更新がされたのに更新通知がくる問題の対応
## 3.2.0
- 最新のSnow Monkey Blocksに合わせた各パターンの調整
## 3.1.3
- Snow Monkey Blocks v19の仕様変更に伴い「沿革」「1カラムの項目」パターンのスタイルを調整
- 1カラムの項目ブロック専用の編集用CSSが読み込まれてない不具合の修正
## 3.1.2
- Snow Monkey Blocks v18.2.0の仕様変更に伴い「アコーディオン」パターンのスタイルを調整
## 3.1.1
- 「シンプルな項目」「流れ」パターンにてブロックの設定で指定した色で表示されない不具合の対応
- 編集画面で「シンプルな項目」パターンのボタンのhoverで動きが実行されないように調整
- 「沿革」パターンの月表示のラベル縦位置を中央にした場合に丸のアクセントも中央になるように調整
## 3.1.0
- Snow Monkey v19・Snow Monkey Blocks v18の対応
## 3.0.0
- Snow Monkeyテーマ必須のパターンについて読み込みの条件処理を追加
## 2.0.1
- 無効化機能の不具合対応など
## 2.0.0
- 各パターンを無効化できる機能を実装
- yarnをv1からv3にアップグレード
- 各種パッケージをアップグレード
- yarn run watchの調整
- README.mdの誤字を修正
## 1.9.0
- シンプルな項目パターンの構造HTMLの修正
- 背景画像あり項目パターンの構造HTMLの修正
- 更新アラートボックスに任意のメッセージを付与できるような機能を追加

## 1.8.0
- 画像をdist/img直ではなくsrc/imgから出力する形に変更
- Snow Monkey依存スタイル名取得用のプロパティ名の変更
- GitHub ActionsについてPRのマージなしのcloseは動かないように調整
- Nodeパッケージの更新
## 1.7.1
- 一部node のパッケージのインストール漏れがあったので追加
- zip化するときに不要なファイルを除去するように修正

## 1.7.0
- 開発環境をLocalアプリからwp-envに変更
- 機械チェックの導入（一旦BackstopJS）

## 1.6.0
- アクセスMAPパターンのHTMLについて親divをコードからグループブロックへ変更
- 沿革パターンのアイテム追加ボタンを非表示にし、追加は複製から行う警告メッセージを表示する形に変更

## 1.5.0
- バージョン番号を2桁繰り上げし、冒頭の番号を1に変更
- composer.jsonのパッケージ名の変更（installエラーの対応）

## 0.0.0.4
- シンプルな項目のダミーテキストを変更

## 0.0.0.3
- サンプル画像の差し替え、パターンのコードの一部修正

## 0.0.0.2
- 無駄なコードがパターン内にあったため削除

## 0.0.0.1
- 製品版リリース
