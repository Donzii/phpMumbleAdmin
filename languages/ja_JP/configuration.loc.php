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

$TEXT['tab_options'] = 'オプション';
$TEXT['tab_ICE'] = 'ICE';
$TEXT['tab_settings'] = '設定';
$TEXT['tab_users'] = 'ユーザ';

// Tab options
$TEXT['select_lang'] = '言語を選択してください';
$TEXT['select_style'] = 'スタイルを選択してください';
$TEXT['select_time'] = 'ローカル時間を選択してください';
$TEXT['time_format'] = '時間の表示形式';
$TEXT['date_format'] = '日付の表示形式';
$TEXT['select_locales_profile'] = 'ロケール情報を選択して下さい';
$TEXT['uptime_format'] = '稼働時間の表示形式';
$TEXT['conn_login'] = 'バーチャルサーバログイン';
$TEXT['conn_login_info'] = 'この設定でバーチャルサーバへの接続を許可するログイン名を選択できます。';

$TEXT['default_options'] = 'デフォルトのオプション値';
$TEXT['default_lang'] = 'デフォルトの言語';
$TEXT['default_style'] = 'デフォルトのスタイル';
$TEXT['default_time'] = 'デフォルトの現地時間';
$TEXT['default_time_format'] = 'デフォルトの時間形式';
$TEXT['default_date_format'] = 'デフォルトの日付形式';
$TEXT['default_locales'] = 'デフォルトのロケール情報';
$TEXT['add_locales_profile'] = 'ロケール情報プロファイルを追加する';
$TEXT['del_locales_profile'] = 'ロケール情報プロファイルを削除する';

$TEXT['sa_login'] = 'SuperAdminログイン';
$TEXT['change_your_pw'] = 'パスワードを変更';
$TEXT['enter_your_pw'] = 'パスワードを入力';

// Tab ICE
$TEXT['profile_name'] = 'プロファイル名';
$TEXT['ICE_host'] = 'ICEインターフェイスのIP';
$TEXT['ICE_port'] = 'ICEインターフェイスのport';
$TEXT['ICE_timeout'] = 'タイムアウト(秒単位)';
$TEXT['ICE_secret'] = 'ICEパスワード';
$TEXT['slice_profile'] = 'Slice プロファイル';
$TEXT['slice_php_file'] = 'Slice phpファイル';
$TEXT['conn_url'] = '接続URL';
$TEXT['conn_url_info'] = 'PMAは"ホスト"パラメータにIPを設定することでバーチャルサーバに接続するできます。このパラメータはホスト名かIPで上書きできます。';
$TEXT['public_profile'] = 'プロファイルをパブリックに設定';
$TEXT['default_ICE_profile'] = 'デフォルトICEプロファイルに設定';
$TEXT['add_ICE_profile'] = 'ICEプロファイルを追加';
$TEXT['del_profile'] = 'ICEプロファイルを削除';
$TEXT['confirm_del_ICE_profile'] = '選択されたICEプロファイルを本当に削除しますか？';

// Tab settings
$TEXT['site_title'] = 'サイトのタイトル';
$TEXT['site_desc'] = 'サイトの説明';
$TEXT['autologout'] = '自動ログイン ( 5 - 30 )';
$TEXT['autocheck_update'] = '更新の自動確認';
$TEXT['autocheck_update_info'] = '日単位: 0 - 31<br>0 = 更新の自動確認を無効化';
$TEXT['check_update'] = '更新の確認';
$TEXT['inc_murmur_vers'] = '接続URLにmurmurのバージョンを含めますか';
$TEXT['inc_murmur_vers_info'] = '古いmumbleクライアントが接続URLでサーバへ接続できなくなるでしょう';

$TEXT['vservers_logs'] = 'サーバログ';
$TEXT['srv_logs_amount'] = 'PMAが表示するログの行数を設定する';
$TEXT['activate_vservers_logs_for_adm'] = '管理者とSuperUserのログを有効にする';
$TEXT['activate_adm_highlight_logs'] = 'ログを色付けする権限をSuperUserに与える';

$TEXT['pma_logs'] = 'PMAのログ';
$TEXT['logs_sa_actions'] = 'SuperAdminの動作のログ';
$TEXT['pma_logs_clean'] = '何日前までのログをPMAが保持するかを設定してください （ 0 = 全て保存 ）';

$TEXT['tables'] = '表';
$TEXT['overview_table_lines'] = 'バーチャルサーバ表の行数';
$TEXT['users_table_lines'] = '登録ユーザ表の行数';
$TEXT['ban_table_lines'] = 'バン表の行数';
$TEXT['tables_infos'] = '10 - 1000 ( 0 = 表のページ分けを無効化 )';

$TEXT['srv_dropdown_list'] = 'ドロップダウンサーバリスト';
$TEXT['activate_auth_dropdown'] = '認証ページに対してドロップダウンサーバリストのキャッシュを有効にする';
$TEXT['activate_auth_dropdown_info'] = 'すべてのサーバのIDと名前が認証ページのHTMLソースコードに含まれることを意味します。';
$TEXT['refresh_ddl_cache'] = 'ドロップダウンサーバリストのキャッシュが自動的に更新されるまでの時間';

$TEXT['autoban'] = '自動バン';
$TEXT['autoban_attemps'] = '試行回数の制限 ( 0 = 自動バンが無効になります )';
$TEXT['autoban_frame'] = '試行の間隔 ( 秒単位 )';
$TEXT['autoban_duration'] = 'バン時間 ( 秒単位 )';

// Tab users
$TEXT['activate_su_login'] = 'PMAに接続するため権限をSuperUserに与える';
$TEXT['activate_su_modify_pw'] = '登録ユーザのパスワードを変更する権限をSuperUserに与える';
$TEXT['activate_su_vserver_start'] = 'バーチャルサーバを開始/停止する権限をSuperUserに与える';
$TEXT['activate_su_ru'] = 'SuperUser_ruクラスを有効にする';
$TEXT['activate_su_ru_info'] = 'Superユーザの権限を条件を満たす登録ユーザに与えます。（詳細は Readme.txt）';
$TEXT['activate_ru_login'] = 'PMAに接続するため権限を登録ユーザに与える';
$TEXT['activate_ru_del_account'] = '自分のアカウントを削除する権限を登録ユーザに与える';
$TEXT['activate_ru_modify_login'] = 'ログイン資格を変更する権限を登録ユーザに与える';

// Generate password options
$TEXT['activate_pwgen'] = 'Eメールでパスワードの生成を有効にする';
$TEXT['activate_explicite_msg'] = '明らかなエラーを有効化します';
$TEXT['sender_email'] = '送信元に設定するメールアドレス';
$TEXT['pwgen_max_pending'] = 'パスワード生成要求に対するペンディング時間 （1～744時間）';


 ?>
