# APIドキュメント

楽天 : https://webservice.rakuten.co.jp/api/ichibaitemsearch/

# 作業メモ

- 出力リンクをアフィリンク化
- 設定画面作り込み
- Yahoo・もしもの機能も作成
- フロント側、その場検索したやつも表示できるように。
- 設定側、各項目を上書きもできるように。
- 情報再取得・リンク切れなどの処理
- 有料化機能は分離
- 更新などを連続でされないように、数分間のキャッシュを行う？


# コード中のメモ書き

「yyi: 」 -> 元のソースに対するメモ
「memo: 」


# どうする？

- 国際化については完全に捨てる？ -> 変数を定数に変えれたり、割り切れることがでてくる
- 商品管理の「投稿タイトル」と、表側で「表示する商品名」、今は一致させてるけど、個別に情報保存して分けれるようにする？
- Amazon検索でKindle のとき、 リンカーは「Kindle」と「Amazon」の2つのボタンが出てくるが、リンク先は同じ。（Kindle版ページ）
  これどうする？ <- 補足:「商品詳細ページを表示する」設定の時はかぶるが、「検索結果ページを表示する」の時はちゃんと意味がでる。



# あとでやる

- 取得データの key 名分かりやすく整理 
- array_get 整理
- Data 整理
  - TABKEY_AMAZON, TABKEY_RAKUTEN → TABKEY['amazon'], TABKEY['rakuten']
  - TABKEY === ajaxアクション名なので、そのあたり統合する？
  - thickboxのアクション名？とかも定数化


# 注意すべきこと

- AmazonのAPIで検索してきたものは、Amazonの商品詳細ページも取得できるが、楽天リンクは検索結果ページしか取得（生成）できない。
- その逆も然り。
- 楽天の商品詳細URLは、APIで取得する時にすでにアフィリンクになってしまう。
  - Amazonみたいに出力時に「後からアフィリンク化」できないので、アフィリエイトIDの設定が変わった時に過去のデータをどうするか考える。
  - 一括で商品リンク再取得できるようなボタンを導入？アフィIDが変わることはほぼないので、とりあえず無視でいいか...？
- 


# コードに関するメモ書き

- アフィリエイト計測リンクにするのは、フロントに出力する時。
  - 多分 generate_amazon_title_link_with_aid() , generate_amazon_link_with_aid() あたりの処理。

- もしもリンクの生成
  - is_moshimo() 判定 -> generate_moshimo_link()

- 楽天リンクで付与される Rinker_i_, Rinker_t_, Rinker_o_ が謎。
  - タイムスタンプ付きなので、何かの計測用か。



https://hb.afl.rakuten.co.jp/hgc/g00r2ri8.13lem44c.g00r2ri8.13lene57/Rinker_t_20200912021732?pc=https%3A%2F%2Fitem.rakuten.co.jp%2Fking-depart%2F2set-tan3tan4%2F&m=http%3A%2F%2Fm.rakuten.co.jp%2Fking-depart%2Fi%2F10002869%2F