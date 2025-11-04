🐳 sns-app

Laravel × Vue（Nuxt 構成）で構築した SNS 風アプリケーションです。
ユーザー登録・ログイン・投稿・コメント・いいね機能を実装しています。

📦 セットアップ手順

1️⃣ クローン
git clone git@github.com:KOUSEI-dot/sns-app.git

cd sns-app/backend

2️⃣ 依存関係インストール（バックエンド）

Laravel 実行に必要なパッケージをインストールします。

composer install

3️⃣ 環境変数設定

.env.example をコピーして .env を作成します。

cp .env.example .env

4️⃣ アプリケーションキー生成

php artisan key:generate

5️⃣ マイグレーション & シーディング

データベースを初期化します。

php artisan migrate --seed

6️⃣ サーバー起動

バックエンドを Docker で立ち上げます。

docker compose up -d

7️⃣ 依存関係インストール（フロントエンド）

フロントエンドをセットアップします。

cd ../frontend

npm install

8️⃣ フロントエンド開発サーバー起動
npm run dev

ブラウザで以下にアクセス 👇
🔗 http://localhost:5173/register

🔗 http://localhost:5173/login

🔐 認証仕様（Sanctum）

ログイン方式：Laravel Sanctum

トークン認証（/api/login → トークン返却）

ログアウト時には、currentAccessToken() が存在する場合のみ削除

🧩 API 一覧
機能 メソッド エンドポイント 認証 説明
ユーザー登録 POST /api/register 不要 name, email, password
ログイン POST /api/login 不要 email, password
ログアウト POST /api/logout 必要 トークン削除
投稿一覧 GET /api/posts 不要 全投稿取得
投稿作成 POST /api/posts 必要 text
投稿削除 DELETE /api/posts/{id} 必要 自分の投稿のみ削除
コメント投稿 POST /api/posts/{id}/comments 必要 text（DB 上は content）
コメント削除 DELETE /api/comments/{id} 必要 自分のコメントのみ削除
いいね追加/解除 POST /api/posts/{id}/like 必要 トグル動作
🧪 テスト実行

すべての Feature テストが通過済みです ✅

php artisan test

結果例：

Tests: 17 passed (100%)

🗂 ディレクトリ構成
backend/
├── app/
│ ├── Http/Controllers/Api/
│ │ ├── AuthController.php
│ │ ├── PostController.php
│ │ └── CommentController.php
│ ├── Models/
│ │ ├── User.php
│ │ ├── Post.php
│ │ └── Comment.php
│ └── ...
├── database/
│ ├── factories/
│ ├── migrations/
│ └── seeders/
├── tests/
│ └── Feature/
│ ├── AuthTest.php
│ ├── PostTest.php
│ └── CommentTest.php
└── ...

frontend/
├── src/
│ ├── components/
│ ├── views/
│ ├── router/
│ └── validation/
└── ...

🧱 開発メモ

Sanctum 使用時、SPA 認証では TransientToken が発行されるため
logout() では method_exists() ガードを実装済み。

コメント投稿は text を受け取り、content カラムに保存。

全 API は JSON レスポンスで返却され、フロント通信に対応。

👨‍💻 作者
名前 役割
シマタニ コウセイ Laravel API 設計・テスト作成・Vue 連携実装
