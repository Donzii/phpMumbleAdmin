<?php

 /*
 *    phpMumbleAdmin (PMA), web php administration tool for murmur ( mumble server daemon ).
 *    Copyright (C) 2010 - 2013  Dadon David. PMA@ipnoz.net
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// ice errors
$TEXT['ice_module_not_found'] = 'php-ICEモジュールが見つかりません。';
$TEXT['ice_connection_refused'] = '接続が拒否されました。';
$TEXT['ice_connection_timeout'] = '接続がタイムアウトしました。';
$TEXT['ice_slice_profile_not_exists'] = 'Sliceプロファイルが存在しません。';
$TEXT['ice_no_slice_definition_found'] = 'Slice定義がみつかりません。';
$TEXT['ice_invalid_secret'] = 'ICEパスワードが正しくありません。';
$TEXT['ice_invalid_slice_file'] = '無効なSliceファイルです。';
$TEXT['ice_icephp_not_found'] = 'PMAはIce.phpを見つけられません。';
$TEXT['ice_unknown_error'] = '不明なICEエラー';

$TEXT['iceprofiles_admin_none'] = 'あなたは現在どのサーバへのアクセスも許可されていません。このエラーについて管理者に問い合わせて下さい。';

$TEXT['ice_help_common'] = 'PMAはICEインターフェイスに接続できません。';

$TEXT['ice_help_no_slice_definition_found'] = 'Murmur.ice か php-Sliceが読み込まれていないことを意味します。';
$TEXT['ice_help_ice34'] = 'ICE 3.4のREADME.txtを確認して下さい。';
$TEXT['ice_help_unauth'] = 'Php Mumble AdminはICE関連のエラーに遭遇しました。<br>このエラー中のすべての動作は無効です。<br>管理者にこの問題について問い合せてください。';

// Messages
$TEXT['auth_error'] = '認証失敗';
$TEXT['auth_su_disabled'] = 'SuperUserのログインは無効です';
$TEXT['auth_ru_disabled'] = '登録ユーザのログインは無効です';
$TEXT['change_pw_error'] = 'エラー、このパスワードは変更されませんでした。';
$TEXT['change_pw_success'] = 'このパスワードは変更されました。';
$TEXT['invalid_bitmask'] = '無効なマスク';
$TEXT['invalid_channel_name'] = '無効なチャンネル文字';
$TEXT['invalid_username'] = '無効なユーザ名の文字';
$TEXT['invalid_certificate'] = '無効な証明書';
$TEXT['auth_vserver_stopped'] = '今はサーバが停止しています - 後で試してみてください。';
$TEXT['user_already_registered'] = 'このユーザはすでに登録されています - 彼は認証されたユーザの状態を変更するためにサーバに再接続する必要があります。';
$TEXT['InvalidSessionException'] = 'このユーザは接続していないかすでにサーバから切断しています。';
$TEXT['InvalidChannelException'] = 'このチャンネルは存在しないかすでに削除されています。';
$TEXT['InvalidUserException'] = 'この登録情報は存在しないかすでに削除されています。';
$TEXT['children_channel'] = 'あなたは子チャンネルに移動できません。';
$TEXT['ServerBootedException'] = 'バーチャルサーバは更新中に停止しました。あなたの最後のアクションは保存されていません。';
$TEXT['invalid_secret_write'] = 'あなたはICEに対する書き込み権限を持っていません - "icesecretwrite"で設定したパスワードをPMAに設定する必要があります。';
$TEXT['ServerFailureException'] = 'バーチャルサーバを開始できません。バーチャルサーバのログを確認して下さい。';
$TEXT['unknown_murmur_exception'] = '不明なICEのエラーが発生しました。';
$TEXT['vserver_dont_exists'] = 'バーチャルサーバは存在しないか削除されています。';
$TEXT['username_exists'] = 'このユーザ名はすでに存在します。';
$TEXT['gen_pw_mail_sent'] = '確認用のメールがあなたのアドレスに送信されました - 新しいパスワードを生成するために説明に従ってください。';
$TEXT['web_access_disabled'] = 'バーチャルサーバへのアクセスは管理者によって無効化されています。';
$TEXT['vserver_dont_allow_HTML'] = 'バーチャルサーバはHTMLタグを許可していません。';
$TEXT['please_authenticate'] = 'サインインして下さい';
$TEXT['iceProfile_sessionError'] = 'セッション中にエラーが発生しました - セキュリティのために、再びログインしてください。';
$TEXT['gen_pw_authenticated'] = 'パスワード生成要求を続行するためにログアウトする必要があります - ログアウトして再試行してください';
$TEXT['certificate_modified_success'] = '証明書は正常に変更されました。<br />このバーチャルサーバを再起動する必要があります。';
$TEXT['host_modified_success'] = 'IPアドレスは正常に変更されました。<br />このバーチャルサーバを再起動する必要があります。';
$TEXT['port_modified_success'] = 'ポートは正常に変更されました。<br />このバーチャルサーバを再起動する必要があります。';
$TEXT['illegal_operation'] = '不正な操作';
$TEXT['vserver_reset_success'] = '設定は正常にリセットされました';
$TEXT['new_su_pw'] = 'SuperUserの新しいパスワード: %s'; // %s new SuperUser password
$TEXT['registration_deleted_success'] = '登録は正常に削除されました';
$TEXT['gen_pw_error'] = 'エラーが発生し、PMAはパスワードの生成要求を処理できません。';
$TEXT['gen_pw_invalid_server_id'] = 'サーバIDが正しくないので、PMAはパスワードの生成要求を処理できません。';
$TEXT['gen_pw_invalid_username'] = 'このユーザ名は存在しないので、PMAはパスワードの生成要求を処理できません。';
$TEXT['gen_pw_su_denied'] = 'SuperUserのパスワードの生成要求はできません';
$TEXT['gen_pw_empty_email'] = 'あなたのアカウントに登録されているメールアドレスは無効なので、PMAはパスワードの生成要求を処理できません。';
$TEXT['new_pma_version'] = 'PhpMumbleAdmin %s がリリースされています';  // %s = new PMA version
$TEXT['no_update_found'] = '更新は見つかりません';
$TEXT['registration_created_success'] = '登録が作成されました';
$TEXT['iceMemoryLimitException'] = 'ICEのメモリ制限（ICE MEMORY LIMIT）に達しました';
$TEXT['iceMemoryLimitException_logs'] = 'ログが大きすぎます。行数は自動的に100に設定されます。';


?>