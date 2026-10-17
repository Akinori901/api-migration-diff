# 移行差分レポート: orders/list

> `examples/demo/` で `/migration-diff orders/list` を Claude Sonnet で実行した実際の出力（2026-10-17。この注記以外は無編集）。

比較日: 2026-10-17

## 対象

| 項目 | 旧システム | 新システム |
|------|-----------|-----------|
| パス | `POST order/order_list` | `POST orders/list` |
| サービス | legacy-monolith | main-service |
| Controller | `OrderController::order_list()` | `OrderListController::__invoke()` |

## 差分サマリ

| カテゴリ | 件数 |
|---------|------|
| 意図的変更 | 4 |
| 改善 | 2 |
| 要修正 | 4 |
| 要確認 | 2 |

## レスポンス構造

| 項目 | 件数 |
|------|------|
| 旧システムのフィールド数（per order） | 6 |
| 新システムのフィールド数（per order） | 4 |
| 欠損 | 2（33%） |
| キー名変更（フロントから見れば欠損） | 1（`order_no` → `orderNo`） |
| うちフロントエンドが使用中 | 3（欠損2件 + キー名変更1件、すべて使用中） |

判定: ❌ 要修正

---

## 差分詳細

### 要修正

| # | 項目 | 旧 | 新 | 影響 |
|---|------|---|---|------|
| 1 | `order_no` キー名変更 | `order_no` | `orderNo` | `OrderList.tsx:10-11` が `row.order_no` で参照。`undefined` になり、`<tr key>` が壊れ、注文番号セルも空欄になる |
| 2 | `delivery_date` フィールド欠損 | レスポンスに含まれる | レスポンスに含まれない | `OrderList.tsx:14` が `row.delivery_date ?? "-"` で参照。`?? "-"` で表示はハードクラッシュしないが、納期列が常に「-」になる |
| 3 | `remarks` フィールド欠損 | レスポンスに含まれる | レスポンスに含まれない | `OrderList.tsx:15` が `title={row.remarks}` で参照。`undefined` になり、ツールチップが消える |
| 4 | キャンセル済みフィルターの欠落 | `where('status', '!=', 'cancelled')` | フィルターなし | キャンセル済み注文が一覧に表示される。業務フローへの影響大 |

### 要確認

| # | 項目 | 内容 | 判断が必要な理由 |
|---|------|------|----------------|
| 1 | ソート順の変更 | 旧: `orderBy('created_at', 'desc')` → 新: `orderBy('id')` | ID と作成日時は通常一致するが、レコード移行・インポート等で乖離する可能性がある。「新しい順」という業務要件が変わっていないか確認が必要 |
| 2 | `created_at` フォーマット変更 | 旧: `Y/m/d H:i`（例: `2026/10/17 14:30`）→ 新: ISO 8601（例: `2026-10-17T14:30:00.000000Z`） | `OrderList.tsx:15` が文字列としてそのまま表示。UIで見える形式が変わるが、業務上問題ないか確認が必要 |

### 改善

- `per_page` パラメータを新規サポート。ページサイズを 1〜100 の範囲で指定可能になった（デフォルト 50 は維持）
- バリデーション強化: `page: min:1`、`per_page: min:1, max:100` の下限・上限チェックを追加

### 意図的変更

- 認証方式: `auth`（セッション）→ `auth:sanctum`（Bearer トークン）。マイクロサービス化に伴う正当な変更
- Controller を単一アクションクラス（`__invoke`）に変更。FW設計方針の変更
- ルートパス: `order/order_list` → `orders/list`。REST 設計に合わせた正規化
- FormRequest への分離（`OrderListRequest`）。バリデーションロジックの責務分離

---

## フロントエンド互換性

- レスポンス構造: **非互換あり**
- 影響ファイル: `frontend/src/pages/OrderList.tsx`

| フィールド | フロント参照箇所 | 状態 |
|-----------|---------------|------|
| `order_no` | `OrderList.tsx:10,11`（key + 表示） | ❌ `orderNo` に改名。undefined になる |
| `customer_name` | `OrderList.tsx:12` | ✅ 変更なし |
| `total` | `OrderList.tsx:13` | ✅ 変更なし |
| `delivery_date` | `OrderList.tsx:14` | ❌ 欠損。常に「-」表示 |
| `remarks` | `OrderList.tsx:15`（title属性） | ❌ 欠損。ツールチップなし |
| `created_at` | `OrderList.tsx:15` | ⚠️ フォーマット変更（要確認） |
