# OOOK 菜单插件安装说明（`local_oookltimenu`）

本文说明如何在 Moodle 中安装并启用 `local_oookltimenu`，将 LTI 工具以课程顶部菜单 `OOOK` 的方式接入。

## 1. 前置条件

- Moodle 版本：`4.3+`
- Moodle core 的 `External tool / mod_lti` 可用
- 已准备外部 LTI 1.3 Tool 的配置参数：
  - Tool launch URL
  - Initiate login URL
  - Redirect URI
  - JWKS URL 或 RSA 公钥

## 2. 安装插件

1. 进入 Moodle 后台：`Site administration -> Plugins -> Install plugins`
2. 上传安装包：`local_oookltimenu_moodle.zip`
3. 点击安装并完成数据库升级
4. 安装后执行一次：`Site administration -> Development -> Purge all caches`

## 3. 配置插件

1. 进入：`Site administration -> Plugins -> Local plugins -> OOOK LTI course menu`
2. 填写 LTI 1.3 Tool 配置
3. 按需设置 `显示 OOOK 课程菜单` 开关
4. 保存配置
5. 页面会显示 Moodle 侧生成的平台信息：
   - Platform ID
   - Client ID
   - Deployment ID
   - Public keyset URL
   - Access token URL
   - Authentication request URL
6. 将这些平台信息配置到外部 LTI Tool 服务端
7. 再执行一次 `Purge all caches`

插件会固定写入以下 LTI 自定义参数，不需要在配置页手动填写：

```text
course_id=$Context.id
user_id=$User.id
email=$Person.email.primary
course_title=$Context.title
```

## 4. 初始化课程（首次一次）

首次使用某门课程时，需要教师账号进入该课程并点击顶部菜单 `OOOK` 一次。  
插件会自动在课程中创建一个内部使用的 LTI 活动（`[OOOK LTI AUTO]`）用于后续发起标准 LTI 流程。

## 5. 使用方式

教师或学生进入课程后，点击顶部菜单 `OOOK`，即可加载并发起 LTI。  
菜单位置为：`Grades` 后、`More` 前（主题可能有少量样式差异）。

## 6. 验证清单

- 顶部出现菜单 `OOOK`
- 点击 `OOOK` 能正确打开目标 LTI 工具
- 学生账号可正常访问（无需课程内手动添加 LTI 活动）

## 7. 常见问题

### 7.1 菜单不显示

- 检查插件是否安装成功
- 检查插件设置页中的 `显示 OOOK 课程菜单` 是否已启用
- 检查课程页面是否有二级导航
- 执行 `Purge all caches` 并浏览器强刷（`Ctrl + F5`）

### 7.2 点击后提示权限问题

- 先用教师账号在该课程点击一次 `OOOK` 完成初始化
- 确认课程可访问、学生已选课且角色正确

### 7.3 提示找不到工具或无法发起

- 检查插件设置页的 LTI 1.3 配置是否保存成功
- 检查外部 Tool 是否已经配置 Moodle 侧生成的 Client ID 和 Deployment ID
- 检查外部 Tool 的 redirect URI 是否与插件设置页配置一致

---

如需升级插件，重复“安装插件”步骤上传新 ZIP 即可，Moodle 会自动执行升级。
