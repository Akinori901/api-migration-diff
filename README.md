# api-migration-diff

**モノリスから切り出した API が、本当に同じように動くかを検証する。**

<p>
  <img alt="Claude Code" src="https://img.shields.io/badge/Claude_Code-skill-D97757">
  <img alt="対象" src="https://img.shields.io/badge/対象-API移行の検品-2ea44f">
  <img alt="framework-agnostic" src="https://img.shields.io/badge/framework-Laravel_/_Django_/_Rails-6E56CF">
  <img alt="外部依存" src="https://img.shields.io/badge/実行-コード読取のみ（旧環境不要）-555">
  <img alt="License" src="https://img.shields.io/badge/License-MIT-green">
</p>

移行作業で一番怖いのは、移植したつもりのエンドポイントが**静かに壊れている**ことです。
レスポンスのキー名が変わった、フィールドが落ちた、除外条件が抜けた
— どれもエラーにならず、画面が空欄になって初めて気づきます。

このリポジトリは、旧システムと新システムの同一エンドポイントを読み比べ、
**差分を「対応が要るもの／要らないもの」に分類してレポートする**ための
AI スキルと、その設定・出力テンプレートです。

- [スキル本体（skill/migration-diff/SKILL.md）](skill/migration-diff/SKILL.md)
- [設定例（examples/migration.config.yml）](examples/migration.config.yml)
- [レポート記入例（examples/sample-report.md）](examples/sample-report.md)

実際のマイクロサービス移行案件（Laravel・4サービス）で 100 本以上のレポートを
出力しながら育てた手順を、案件固有の情報を除いて一般化したものです。

## 位置づけ

移行には3つの段階があり、これは**最後の検品**にあたります。

| 段階 | 問い |
|---|---|
| 棚卸し | 何がまだ移植されていないか |
| 移植 | 移植する |
| **検証（これ）** | **移植したものは正しいか** |

## 使い方

### 1. スキルを入れる

`skill/migration-diff/` を、検証したいプロジェクトの `.claude/skills/` に置きます
（全プロジェクトで使うなら `~/.claude/skills/`）。テンプレートも同じディレクトリに入っています。

```bash
git clone https://github.com/Akinori901/api-migration-diff
mkdir -p <対象プロジェクト>/.claude/skills
cp -r api-migration-diff/skill/migration-diff <対象プロジェクト>/.claude/skills/
# 更新を追いたいならコピーの代わりにシンボリックリンクでもよい
```

### 2. 設定を書いて起動する

プロジェクトのルートの `migration.config.yml` に旧・新システムの構成を書き、
エンドポイントを指定してスキルを起動します。

```bash
cp api-migration-diff/examples/migration.config.yml <対象プロジェクト>/migration.config.yml
# 自社の構成に書き換える

# Claude Code から
/migration-diff orders/list
```

`docs/diff-reports/{日付}/{対象}.md` にレポートが出ます。

## 設計判断

このスキルが他の「差分を出すツール」と違うのは、次の3点です。

### 差分を4つに分類する

意図的変更 / 改善 / **要修正** / 要確認。

差分をただ列挙すると「多すぎて読まれないレポート」になります。
FW移行に伴う正当な変更と、意図せず壊れた箇所を混ぜないことで、
**次に何をすべきかが決まる**レポートになります。

### レスポンス構造を最優先で見る

フロントエンドの互換性に直結するためです。
ロジックの細かな差異より、まずフィールドの欠損を潰します。

欠損は必ず定量化します。「一部欠損」ではなく
**「48 → 31（17欠損・35%）、うちフロントが使用中 6件」**と書く。
規模と実害が分からないと優先度が決まりません。

### 旧システムが動かなくても成立する

コード読み取りのみで比較する前提です。
移行案件では「旧環境はもう動かせない」ことが珍しくないため、
**実行して突き合わせる方式では検証そのものができません。**

## 想定していること

- 設定ファイルの `routes` を起点にルート定義を辿るため、
  フレームワークは問いません（Laravel / Django / Rails 等）
- マイクロサービス構成にも、モジュラモノリスにも使えます
- `frontend.path` を設定すると、欠損フィールドが実際に使われているかまで確認できます

## 関連

- [clean-arch-laravel](https://github.com/Akinori901/clean-arch-laravel) — 移植先の層構成を deptrac で強制する規約

## ライセンス

MIT
