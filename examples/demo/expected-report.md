# 期待されるレポート（デモの正解）

`examples/demo/` で `/migration-diff orders/list` を実行したとき、
レポートに**最低限これが出ていれば正しく動いている**、という基準です。
文章や表の細部はモデルによって変わります。

## 対象

| 項目 | 旧システム | 新システム |
|------|-----------|-----------|
| パス | POST `order/order_list` | POST `orders/list` |
| サービス | legacy-monolith | main-service |
| Controller | OrderController::order_list | OrderListController::__invoke |

## レスポンス構造（`orders[]` の要素）

| 項目 | 件数 |
|------|------|
| 旧システムのフィールド数 | 6 |
| 新システムのフィールド数 | 4 |
| 欠損（キー名の変更を含む） | 3: `order_no`（→ `orderNo`）・`delivery_date`・`remarks` |
| うちフロントエンドが使用中 | 3（すべて `frontend/src/pages/OrderList.tsx`） |

判定: ❌要修正

「欠損 2 ＋ キー名変更 1」と分けて書かれていても同じ内容です（合計 3、使用中 3）。

## 分類

| 分類 | 期待される差分 |
|---|---|
| **要修正** | `order_no` → `orderNo` のキー名変更（一覧の行キーと注文番号列が空になる） |
| **要修正** | `delivery_date` の欠損（納期列が「-」になる） |
| **要修正** | `remarks` の欠損（備考のツールチップが消える） |
| **要修正** | キャンセル済みの除外（`status != 'cancelled'`）が無い（件数が実態と合わない） |
| **要確認** | 並び順が `created_at` 降順 → `id` 昇順 |
| **要確認** | `created_at` の形式が `Y/m/d H:i` → ISO 8601（画面はそのまま表示しているので見え方が変わる） |
| **改善** | `page` に `min:1`、`per_page` に上限 100 のバリデーションが追加された |
| **意図的変更** | 認証がセッション（`auth`）から Bearer トークン（`auth:sanctum`）へ |
| **意図的変更** | パスと Controller 名の変更 |

`created_at` の形式変更は「改善」とも読めますが、フロントが文字列をそのまま
表示しているため、見え方が変わる点を人間が判断する必要があります（要確認が妥当）。
