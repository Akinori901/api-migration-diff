# デモ: 注文一覧 API の移行検証

旧システム（モノリス）の `order/order_list` を、新システム（切り出した注文サービス）の
`orders/list` に移植した、という想定の小さなコード一式です。**意図的に壊してあります。**

```
legacy-monolith/   旧 API（Laravel 風。実行はしない）
main-service/      新 API（同上）
frontend/          旧 API に合わせて書かれた一覧画面
migration.config.yml
expected-report.md 正しく動いたときに出るはずの差分
```

## 試し方

```bash
cd examples/demo
mkdir -p .claude/skills && cp -r ../../skill/migration-diff .claude/skills/
claude
> /migration-diff orders/list
```

`docs/diff-reports/{日付}/orders-list.md`（ファイル名はモデルが決める）にレポートが出ます。
[`expected-report.md`](expected-report.md) と見比べてください。

実際に Claude Sonnet で実行した出力を [`sample-output.md`](sample-output.md) に置いています
（期待した差分をすべて検出し、使用中の 3 フィールドの参照箇所まで特定できています）。

仕込んである差分: キー名の変更・フィールドの欠損・除外条件の欠落（要修正）、
並び順と日付形式の変化（要確認）、バリデーションの追加（改善）、認証方式の変更（意図的変更）。
コードは読むだけで、PHP も DB も要りません。
