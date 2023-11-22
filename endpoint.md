# エンドポイント一覧

1. ユーザー関連
  マイページ情報の取得: GET /api/users/{userId}/mypage
  個人情報の取得: GET /api/users/personal
  個人情報の更新: PATCH /api/users/personal
  プロフィール情報の取得: GET /api/users/profile
  プロフィール情報の更新: PATCH /api/users/profile
  ログイン: POST /api/auth/login
  ログアウト: POST /api/auth/logout
  サインアップ: POST /api/auth/signup
  メールアドレスとパスワードの変更: PATCH /api/auth/update-credentials
2. 配送先関連
  配送先一覧の取得: GET /api/shipping-addresses/list
  配送先の追加: POST /api/shipping-addresses
  配送先の更新: PATCH /api/shipping-addresses/{shippingAddressId}
  配送先の削除: DELETE /api/shipping-addresses/{shippingAddressId}
  アクティブな配送先の取得: GET /api/shipping-address/active
3. 商品関連
  商品詳細の取得: GET /api/products/{productId}
  商品を出品: POST /api/products
  商品の更新: PUT /api/products/{productId}
  商品の削除: DELETE /api/products/{productId}
  商品のレビューの投稿: POST /api/products/{productId}/reviews
4. 検索・閲覧関連
  検索キーワードでの検索: GET /api/search
  過去の検索履歴の取得: GET /api/search/history
  過去の検索履歴に基づく検索: GET /api/search/history/{id}
  検索結果の絞り込み: GET /api/search/filtered
  特定カテゴリに基づく商品検索: GET /api/search/category/{categoryId}
  お気に入り一覧の取得: GET /api/favorites/list
  商品をお気に入りに追加: POST /api/favorites/{productId}
  お気に入りから商品を削除: DELETE /api/favorites/{productId}
  閲覧履歴の取得: GET /api/productviews/history
  商品詳細画面への遷移時の記録: POST /api/productviews/{productId}
5. 取引関連
  取引の開始: POST /api/transactions/start
  取引状況の更新: PATCH /api/transactions/{transactionId}
  取引履歴の取得: GET /api/transactions/history
  取引の評価の投稿: POST /api/transactions/{transactionId}/evaluations
6. ポイント関連
  ユーザーのポイントの取得: GET /api/points/current
  ポイントのチャージ: POST /api/points/charge
  ポイント履歴の取得: GET /api/points/history
7. 通知関連
  ユーザーのお知らせ一覧の取得: GET /api/notices
  未読通知の有無の確認: GET /api/notices/unread-exists
  画面遷移時の未読通知の既読化: PATCH /api/notices/mark-all-read
