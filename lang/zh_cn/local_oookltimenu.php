<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

$string['pluginname'] = 'OOOK LTI 课程菜单';
$string['menuitemdefault'] = 'OOOK';
$string['targettypeid'] = '目标 LTI 工具 typeid';
$string['targettypeid_desc'] = '菜单启动必填。插件会在每门课自动创建一个隐藏的 LTI 活动，并通过 iframe 启动。';
$string['ltitoolconfig'] = '托管的 LTI 1.3 工具';
$string['ltitoolconfig_desc'] = '配置 OOOK 课程菜单使用的外部 LTI 1.3 工具。保存本页会自动创建或更新 Moodle External tool 配置，并自动保存生成的 deployment id。';
$string['managedtooldescription'] = '由 OOOK LTI 课程菜单插件托管。';
$string['setting_toolname'] = '工具名称';
$string['setting_toolname_desc'] = '插件自动创建的 Moodle External tool 配置名称。';
$string['setting_toolurl'] = '工具启动 URL';
$string['setting_toolurl_desc'] = '外部工具提供的 LTI 1.3 launch URL。';
$string['setting_tooldescription'] = '工具描述';
$string['setting_tooldescription_desc'] = '保存到 Moodle External tool 配置中的描述。';
$string['setting_initiatelogin'] = 'Initiate login URL';
$string['setting_initiatelogin_desc'] = '外部工具提供的 OpenID Connect 登录初始化 URL。';
$string['setting_redirectionuris'] = 'Redirect URI';
$string['setting_redirectionuris_desc'] = '每行一个 redirect URI，必须与外部工具实际使用的 redirect URI 匹配。';
$string['setting_keytype'] = '公钥类型';
$string['setting_keytype_desc'] = '外部工具发布 JWKS 时选择 JWKS URL，否则粘贴 RSA 公钥。';
$string['setting_publickeyset'] = 'JWKS URL';
$string['setting_publickeyset_desc'] = '选择 JWKS URL 公钥类型时必填。';
$string['setting_publickey'] = 'RSA 公钥';
$string['setting_publickey_desc'] = '选择 RSA 公钥类型时必填。';
$string['setting_customparameters'] = '自定义参数';
$string['setting_customparameters_desc'] = '这些 LTI 自定义参数由插件固定写入 Moodle External tool 配置，不需要手动填写。';
$string['setting_coursevisible'] = '工具配置使用方式';
$string['setting_coursevisible_desc'] = '控制这个托管的 Moodle External tool 是否在教师添加外部工具活动时显示。';
$string['setting_launchcontainer'] = '启动容器';
$string['setting_launchcontainer_desc'] = 'Moodle 从隐藏辅助活动打开外部工具的方式。';
$string['setting_navenabled'] = '显示 OOOK 课程菜单';
$string['setting_navenabled_desc'] = '启用后会在课程导航中添加 OOOK 菜单。关闭后，托管的 LTI 工具配置仍保留，但课程导航入口会隐藏。';
$string['setting_sendname'] = '发送用户姓名';
$string['setting_sendname_desc'] = 'LTI launch 时向外部工具发送用户姓名。';
$string['setting_sendemailaddr'] = '发送用户邮箱';
$string['setting_sendemailaddr_desc'] = 'LTI launch 时向外部工具发送用户邮箱。';
$string['keytypekeyset'] = 'JWKS URL';
$string['keytypersa'] = 'RSA 公钥';
$string['coursevisiblehidden'] = '不显示，仅由 OOOK 菜单启动';
$string['coursevisiblepreconfigured'] = '添加外部工具时显示为预配置工具';
$string['coursevisibleactivitychooser'] = '在活动选择器中显示，并显示为预配置工具';
$string['launchcontainerembed'] = '嵌入';
$string['launchcontainerembednoblocks'] = '嵌入，无区块';
$string['launchcontainernewwindow'] = '新窗口';
$string['launchcontainerexistingwindow'] = '当前窗口';
$string['platformdetails'] = '提供给外部工具的 Moodle 平台信息';
$string['platformdetailsmissing'] = '成功保存 LTI 工具配置后，这里会显示平台信息。';
$string['clientidready'] = '需要配置到你的 LTI 工具中的 Client ID';
$string['platform_platformid'] = 'Platform ID';
$string['platform_clientid'] = 'Client ID';
$string['platform_deploymentid'] = 'Deployment ID';
$string['platform_publickeyseturl'] = 'Public keyset URL';
$string['platform_accesstokenurl'] = 'Access token URL';
$string['platform_authrequesturl'] = 'Authentication request URL';
$string['errorinvalidconfig'] = 'LTI 工具配置无效。';
$string['errorrequiredtoolname'] = '工具名称必填。';
$string['errorrequiredtoolurl'] = '必须填写有效的工具启动 URL。';
$string['errorrequiredinitiatelogin'] = '必须填写有效的 initiate login URL。';
$string['errorrequiredredirecturis'] = '至少需要一个有效的 redirect URI。';
$string['errorinvalidredirecturi'] = '无效的 redirect URI：{$a}';
$string['errorrequiredpublickeyset'] = '选择 JWKS URL 时必须填写有效的 JWKS URL。';
$string['errorrequiredpublickey'] = '选择 RSA 公钥时必须填写公钥内容。';
$string['errorsyncfailed'] = '创建或更新 Moodle LTI 工具失败：{$a}';
$string['missingtypeidconfig'] = 'OOOK LTI 菜单未配置目标 LTI 工具 typeid。';
$string['invalidtooltypeid'] = '已配置的 LTI 工具 typeid 无效或不可用。';
$string['autoltiinitrequired'] = '当前课程尚未初始化 OOOK LTI，请让教师先点击一次 OOOK LTI 进行初始化。';
$string['autolticreatefailed'] = '当前课程隐藏 OOOK LTI 活动初始化失败。';
$string['privacy:metadata:externalpurpose'] = '插件会打开为外部工具配置的 Moodle LTI 活动。Moodle core mod_lti 可能根据本插件配置和 Moodle LTI 隐私设置向外部工具发送用户和课程上下文。';
$string['privacy:metadata:userid'] = 'Moodle 用户 ID 可能会发送给外部 LTI 工具。';
$string['privacy:metadata:username'] = 'Moodle 用户名可能会发送给外部 LTI 工具。';
$string['privacy:metadata:fullname'] = '启用姓名共享时，用户全名可能会发送给外部 LTI 工具。';
$string['privacy:metadata:email'] = '启用邮箱共享时，用户邮箱可能会发送给外部 LTI 工具。';
$string['privacy:metadata:role'] = '用户课程角色可能会发送给外部 LTI 工具。';
$string['privacy:metadata:courseid'] = 'Moodle 课程 ID 可能会发送给外部 LTI 工具。';
$string['privacy:metadata:coursefullname'] = '课程全名可能会发送给外部 LTI 工具。';
