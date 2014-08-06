=== Custom Fields Permalink ===

カスタムフィールドの値を使ってパーマリンクを生成できるようにします。


== インストール ==

1. ZIPファイルを解凍して、
   フォルダごと /wp-content/plugins/ へアップロードしてください。

2. アドミンのプラグイン管理ページで Custom Fields Permalink を
   有効にしてください。

3. アドミンの「設定 > パーマリンク設定」のページで、
   「カスタム構造」を再度保存してください。


== 使用方法 ==

タグ:

 - %cfp_a_customfield_name%
 - %cfp_a_customfield_name_or_page_id%
 - %cfp_a_customfield_name_or_pagename%

"a_customfield_name" には任意のカスタムフィールドのフィールド名が入ります。

動作例:

以下のような「カスタム構造」であった場合

 - /%cfp_a_customfield_name%

 "a_customfield_name" というフィールドに値のあるエントリーでは:
  > /a_value_of_customfield ("a_customfield_name" はフィールドの値です。)

 "a_customfield_name" というフィールドに値のないエントリーでは:
  > error!


以下のような「カスタム構造」であった場合

 - /%cfp_a_customfield_name_or_page_id%

 "a_customfield_name" というフィールドに値のあるエントリーでは:
  > /a_value_of_customfield (a value of "a_customfield_name")

 "a_customfield_name" というフィールドに値のないエントリーでは:
  > /1 (page_id)


以下のような「カスタム構造」であった場合

 - /%cfp_a_customfield_name_or_pagename%

 "a_customfield_name" というフィールドに値のあるエントリーでは:
  > /a_value_of_customfield (a value of "a_customfield_name")

 "a_customfield_name" というフィールドに値のないエントリーでは:
  > /as_page_title (pagename)
