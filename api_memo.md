# 取得データについて


## Amazonから取得時

- asin : Amazon用商品ID
- brand : ブランド名（メーカー名？）
- amazon_url : Amazon商品検索結果ページのURL
- rakuten_url : 楽天商品検索結果ページのURL
- yahoo_url : Yahoo商品検索結果ページのURL
- amazon_detail_url : Amazon商品詳細ページのURL（アフィリンクじゃない）
- product_group : Amazonの商品カテゴリー名 ?
- amazon_kindle_url : Kindle商品の場合の商品詳細ページのURL (下の amazon_title_url と同じ値になる)
- amazon_title_url : Amazonの商品詳細ページのURL（アフィリンク）
- price : 値段
- price_at : 値段取得日時
- s_image_url : 画像 src (Sサイズ)
- m_image_url : 画像 src (Mサイズ)
- l_image_url : 画像 src (Lサイズ)


## 楽天
- rakuten_itemcode : 楽天用商品ID
- rakuten_title_url : 楽天商品詳細ページのURL（アフィリンク）
- brand : ブランド名
- price : 価格
- price_at : 値段取得日時
- amazon_url : Amazon商品検索結果ページのURL
- rakuten_url : 楽天商品検索結果ページのURL
- yahoo_url : Yahoo商品検索結果ページのURL
- affiliateRate : アフィリエイトレート？
- reviewAverage : レビューの平均点
- s_image_url : 画像 src (Sサイズ)
- m_image_url : 画像 src (Mサイズ)
- l_image_url : 画像 src (Lサイズ)



**メモ**
- keyword 保存する？
- 画像ソースはサイズによって取得できないことがあるので注意する。（取得時によしなに）
- 商品詳細ページのURLが取得できるのは、その検索元の情報だけ。（Amazonで検索したら楽天の詳細ページは取得できない）
- 本の時、brandがないので、著者情報も引っ張れるようにする？
- 現状、amazon_kindle_url は、Kindle商品の時だけ存在している。（値が空なだけじゃなく、キーがそもそも存在しない）
- key名は整えたい。（amazon_title_url とか。price_at -> date でよくない？）
- 楽天でもアフィリンクなしの商品詳細URL持たせてよくない？
