# API取得データについて


## エラーを返す時
ajaxでAPIから商品検索時、エラーがあれば以下の形式でデータを返す。
（'searched_items' にエラーデータがそのまま格納されるので、出力時にそれを判別する。)

```
array(
    'error' => [
        'code' => 'エラーコード',
        'message' => 'エラーの詳細メッセージ'
    ]
)
```


## Amazonから取得時

- keywords: 検索ワード
- searched_at: "amazon"
- asin : Amazon用商品ID
- brand : ブランド名（メーカー名？）
- contributors : 著者情報など。（brandなければこっち出力する）
- amazon_detail_url : Amazon商品詳細ページのURL（アフィリンクじゃない）
- product_group : Amazonの商品カテゴリー名 ?
- price : 値段
- price_at : 値段取得日時
- s_image_url : 画像 src (Sサイズ)
- m_image_url : 画像 src (Mサイズ)
- l_image_url : 画像 src (Lサイズ)


## 楽天

- keywords: 検索ワード
- searched_at: "rakuten"
- itemcode : 楽天用商品ID
- rakuten_detail_url : 楽天商品詳細ページのURL（アフィリンクじゃない）
- brand : （とれない）
- shop_name : ショップ名
- price : 価格
- price_at : 値段取得日時
- s_image_url : 画像 src (Sサイズ)
- m_image_url : 画像 src (Mサイズ)
- l_image_url : 画像 src (Lサイズ) （とれない）

- affiliateRate : アフィリエイトレート いる？
- reviewAverage : レビューの平均点 いる？ -> Amazon APIでは取れない


商品検索API の後に、さらに 製品詳細API で情報とるとブランド名とかもとれるっぽい。

https://webservice.rakuten.co.jp/api/productdetail/



**メモ**
- keyword 保存する？
- 画像ソースはサイズによって取得できないことがあるので注意する。（取得時によしなに）
- 商品詳細ページのURLが取得できるのは、その検索元の情報だけ。（Amazonで検索したら楽天の詳細ページは取得できない）
- 本の時、brandがないので、著者情報も引っ張れるようにする？
- 現状、amazon_kindle_url は、Kindle商品の時だけ存在している。（値が空なだけじゃなく、キーがそもそも存在しない）
- key名は整えたい。（amazon_title_url とか。price_at -> date でよくない？）
- 楽天でもアフィリンクなしの商品詳細URL持たせてよくない？
