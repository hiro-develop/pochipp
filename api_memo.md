# API取得データについて


## エラーを返す時の形式ルール

ajaxでAPIから商品検索時、エラーがあれば以下の形式でデータを返す。
（'searched_items' にエラーデータがそのまま格納されるので、出力時にそれを判別してエラー表示している。)

```php
return [
    'error' => [
        'code' => 'エラーコード',
        'message' => 'エラーの詳細メッセージ'
    ]
];
```


## Pochipp管理で保存されるデータ

▼ 投稿データとして保存されるデータ

- "pid": ポチップ管理ID（投稿ID）
- "title": 商品タイトル（投稿タイトル）

▼ 投稿のメタデータとして保存されるデータ
- "keywords": 検索キーワード
- "searched_at": どのAPIから検索したか
- "info": 商品タイトルの下に表示されるテキスト。（ブランド情報orショップ名or著者情報など。）
    - 検索元APIによって何がセットされるか変わる。
    - Amazonは brand > contributors , 楽天は shopName , Yahooは brand/name > seller/name
- "image_url": 商品画像
- "price": 価格
- "price_at": 価格取得時の時刻(年/月/日 時:分)
- "custom_btn_url": カスタムボタンのURL
- "custom_btn_text": カスタムボタンのテキスト



### Amazonから取得時のみセットされるデータ

- "asin": 商品ID
- "amazon_affi_url": 商品詳細ページのアフィURL


### 楽天から取得時のみセットされるデータ

- "itemcode": 商品ID
- "rakuten_detail_url": 商品詳細ページのURL
- "review_score" : レビューの平均点 -> オフ中（Amazon APIで取れない）

memo: 商品検索APIでは販売ショップ名しか取得できないが、製品詳細API だと「ブランド名」とかもとれるっぽい。
https://webservice.rakuten.co.jp/api/productdetail/


### Yahooから取得時のみセットされるデータ

- "yahoo_itemcode": 商品ID
- "seller_id": 販売ショップID (「商品ID」だけではAPIで個別情報取得できない。)
- "yahoo_detail_url": Yahooショッピング（PayPayモール）の商品詳細ページのURL
- "is_paypay": PayPayモールで出品されている商品かどうか


### ブロックでだけ持つ情報

- "hideInfo": タイトル下情報を非表示にするかどうか ->（ポチップ管理側でも設定できるようにする...？）
- "hidePrice": 価格を非表示にするかどうか ->（ポチップ管理側でも設定できるようにする...？）
- "hideAmazon": Amazonボタンを非表示にするかどうか
- "hideRakuten": Rakutenボタンを非表示にするかどうか
- "hideYahoo": Yahooボタンを非表示にするかどうか
- "hideCustom": カスタムボタンを非表示にするかどうか



## メモ

- 商品詳細ページのURLが取得できるのは、その検索元の情報だけ。（Amazonで検索したら楽天の詳細ページは取得できない）
- Amazonの商品ページURLは、アフィリンクを保存してる。
    - （APIから返ってきたリンク使わないとPA-API実績にならないかもしれないので。）
    - 非アフィリンクは asin 情報を元にして簡単に生成できるため、データとしては asin 情報のみ保存している。
- 楽天の商品詳細URLは、アフィリンクでとるか非アフィリンクでとるかどちらかしかない。
    - 現状、もしもリンクすることを考えて「非アフィリンク」情報を保存している。
    - アフィリンク化は、その情報を元に一応できているが、計測が正常に行えるかはまだ未検証。
