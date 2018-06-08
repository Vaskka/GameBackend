# 游戏后端接口文档

## LW Project 

### 尝试注册

#### commit 方式
GET
#### 参数
project="projectName" &
status=regist & 
email=邮箱 & 
password=密码 

#### 返回值
验证码发送成功
<code>
code:0,
token:token
</code>

email 已经存在
<code>
code:-1,
token:-1,
msg:Email has been existed, please change a email.
</code>

验证码发送失败
<code>
code:1,
token:"token",
msg:send mail error, please change a email.
</code>

### 验证码验证成功

#### commit 方式
GET

#### 参数
project="projectName" &
status=regist_send_token &
email=邮箱 &
password=密码

#### 返回值
<code>
code:0,
msg:登陆成功,
name:"name"
</code>

<code>
code:1,
msg:登录失败,
name:"-1"

</code>

### 尝试登陆

#### commit 方式
GET

#### 参数
project="projectName" &
status=sign &
email=邮箱 &
password=密码

#### 返回值
<code>
code:0,
msg:登陆成功,
name:"name"
</code>

<code>
code:1,
msg:登录失败,
name:"-1"

</code>

### 请求分配对手

#### commit 方式
GET

#### 参数
project="projectName" &
status=assign_for_another &
email=邮箱 

#### 返回值
'已找到匹配
<code>
code:0,
turn:first,
email:对手的email
name:"name"
</code>

'已发出邀请
<code>
code:1,
turn:first,
email:1,
name:1
</code>

'仍在匹配中
<code>
code:-1,
turn:undefined,
email:1,
name:1
</code>

### 查询对手上次落点

#### commit 方式
GET

#### 参数
project="projectName" &
status=assign_for_another &
email=邮箱 

#### 返回值
<code>
code:0,
point:"point"
</code>


### 更新自身落点位置

#### commit 方式
GET

#### 参数
project="projectName" &
status=assign_for_another &
email=邮箱 &
point="point"

#### 返回值
<code>
code:0,
point:"point"
</code>