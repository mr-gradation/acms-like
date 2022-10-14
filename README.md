# acms-like
a-blog cmsのサイトにいいね！ボタンを追加できる拡張アプリです。

## ダウンロード
[acms-like](https://github.com/mr-gradation/acms-like/releases/download/v1.0.2/acms-like-1.0.2.zip)

## インストール
1. config.serve.php を変更し、`define('HOOK_ENABLE', 1);` にしてHOOKを有効にします。
2. ダウンロード後、`extension/plugins/Like` に設置します。（フォルダ名は１文字目が大文字になります）
3. 管理ページ > 拡張アプリのページに移動し、Like をインストールします。

## いいね！ボタンの設置
以下のスクリプトタグを設置してください。

```
<script src="/extension/plugins/Like/assets/like.js"></script>
```

記事詳細ページなどにいいね！ボタンを設置します。 `class="js-like-button"`、`data-like-eid`、`class="count"` は動作に必要です。

```
  <button class="js-like-button" data-like-eid="%{EID}">
    <span class="label">いいね!</span>
    <span class="count"></span>
  </button>
```

※　`data-like-eid` の箇所は、Entry_Bodyモジュールであれば `{entry:loop.eid}` 、 Entry_Summaryモジュールであれば `{eid}` を入れてください。（エントリー画面だけの使用であれば　`%{EID}` で動きます。）

## いいね！ボタンの数の表示

以下のスクリプトタグを設置してください。

```
<script src="/extension/plugins/Like/assets/like.js"></script>
```

記事一覧ページなどにいいね！の数を設置します。 `class="js-like-numbers"`、`data-like-eid`、`class="count"` は動作に必要です。

```
<p class="js-like-numbers" data-like-eid="{eid}">
  <span class="label">いいね!</span>
  <span class="count">0</span>
</p>
```

※　`data-like-eid` の箇所は、Entry_Bodyモジュールであれば `{entry:loop.eid}` 、 Entry_Summaryモジュールであれば `{eid}` を入れてください。（エントリー画面だけの使用であれば　`%{EID}` で動きます。）

## いいね！ボタン押下時

### classの変化

いいね！ボタンを押下すると、0.5秒間 `-active` というclassが付与されます。0.5秒経過するか再度ページを訪問すると `-voted` というclassと、`disabled` 属性が追加されます。これにより、いいね！済みであればスタイルを変える、アニメーションを付けることが可能です。

### いいね！ボタンを押下したユーザー

いいね！ボタンを押下したかどうかは、a-blog cmsにログインしていると、a-blog cmsのユーザID、ログインしていなければブラウザのCookieに保存されます。ブラウザのCookieをリセットするか、シークレットウィンドウなどで開くと、再度いいね！ボタンを押下できますので、ご注意ください。

## オプション

### モジュールでの表示

いいね！数はモジュールでも表示することが可能です。
ただし、キャッシュが有効になるためご注意ください。

```
<!-- BEGIN_MODULE Entry_Summary -->
<table border="1">
  <tr>
    <th>eid</th>
    <th>title</th>
    <th>count</th>
  </tr>
  <!-- BEGIN entry:loop -->
  <tr>
    <td>{eid}</td>
    <td>{title}</td>
    <td><!-- BEGIN_MODULE\ Like ctx="eid/{eid}" -->\{count\}<!--END_MODULE\ Like --></td>
  </tr>
  <!-- END entry:loop -->
</table>
<!-- END_MODULE Entry_Summary -->
```

### 校正オプションでの表示

いいね！数は校正オプションでも表示することが可能です。
ただし、キャッシュが有効になるためご注意ください。

```
<!-- BEGIN_MODULE Entry_Summary -->
<table border="1">
  <tr>
    <th>eid</th>
    <th>title</th>
    <th>count</th>
  </tr>
  <!-- BEGIN entry:loop -->
  <tr>
    <td>{eid}</td>
    <td>{title}</td>
    <td>{eid}[count_like]</td>
  </tr>
  <!-- END entry:loop -->
</table>
<!-- END_MODULE Entry_Summary -->
```

### カテゴリーの指定

`data-like-category` を指定すると、ボタンのカテゴリーを指定することができます。ボタンのカテゴリーは任意の文字を指定することができます。

#### 詳細ページ

```
<button class="js-like-button" data-like-eid="%{EID}" data-like-category="useful">
    <span class="label">役に立った</span>
    <span class="count"></span>
</button>
<button class="js-like-button" data-like-eid="%{EID}" data-like-category="useless">
    <span class="label">役に立たなかった</span>
    <span class="count"></span>
</button>
```

#### 一覧ページ

```
<p class="js-like-numbers" data-like-eid="{eid}" data-like-category="useful">
  <span class="label">役に立った</span>
  <span class="count">0</span>
</p>
<p class="js-like-numbers" data-like-eid="{eid}" data-like-category="useless">
  <span class="label">役に立たなかった</span>
  <span class="count">0</span>
</p>
```

複数のカテゴリーがある場合はまとめて表示することも可能です。

```
<p class="js-like-numbers" data-like-eid="{eid}">
  <span class="label">役に立った!</span>
  <span class="count" data-like-category="useful">0</span>
  <span class="label">役に立たなかった</span>
  <span class="count" data-like-category="useless">0</span>
</p>
```

#### モジュール

モジュールで使用することも可能です。

```
<!-- BEGIN_MODULE Entry_Summary -->
<table border="1">
  <tr>
    <th>eid</th>
    <th>title</th>
    <th>useful</th>
    <th>useless</th>
  </tr>
  <!-- BEGIN entry:loop -->
  <tr>
    <td>{eid}</td>
    <td>{title}</td>
    <td><!-- BEGIN_MODULE\ Like ctx="eid/{eid}" -->\{useful\}<!--END_MODULE\ Like --></td>
    <td><!-- BEGIN_MODULE\ Like ctx="eid/{eid}" -->\{useless\}<!--END_MODULE\ Like --></td>
  </tr>
  <!-- END entry:loop -->
</table>
<!-- END_MODULE Entry_Summary -->
```

#### 校正オプション

校正オプションで使用することも可能です。

```
<!-- BEGIN_MODULE Entry_Summary -->
<table border="1">
  <tr>
    <th>eid</th>
    <th>title</th>
    <th>useful</th>
    <th>useless</th>
  </tr>
  <!-- BEGIN entry:loop -->
  <tr>
    <td>{eid}</td>
    <td>{title}</td>
    <td>{eid}[count_like('useful')</td>
    <td>{eid}[count_like('useless')</td>
  </tr>
  <!-- END entry:loop -->
</table>
<!-- END_MODULE Entry_Summary -->
```
