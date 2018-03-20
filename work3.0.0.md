# APP接口开发规范文档

> @author tangchunxin

> @final  start:20180320

#### 本文介绍了接口设计中常见的规范，以及个人的一些思考与总结


![](https://mmbiz.qpic.cn/mmbiz_png/UHKG18j8iasZcupuQvRSvdG8ZwtFSiciaOdMtFx5jRIzw2viciayKru41FJ0oxYk3l5U6c58xfxuXrq2Eq0iaDdicPrnw/640?wx_fmt=png&tp=webp&wxfrom=5&wx_lazy=1)

* [1.概述](#info)
* [2.通用请求参数](#request)
* [3.接口`parameter`参数](#parameter)
* [4.响应数据](#response)
* [5.命名规范](#name)
* [6.瘦客户端](#client)
* [7.扩展性](#plug)
* [8.兼容性](#compatible)
* [9.扩展性](#plug)

<h3 id="info">1.概述</h3>
## 概述
- 请求格式为 `url?c_version=x.x.x&parameter={}`，`parameter` 中字段 `mod` 和 `platform` 的较为固定，
<font color=red>所以下面只提供 `parameter` 区别传入的参数</font>

``` json
request:
{
  randkey: ""
  , c_version: "3.0.2"
  , parameter:
  {
    mod: "Business"
    , act: "Xxx_xx"
    , platform: "gfplay"	// android: gfplay , ios: gfplay_ios
    ...
  }
}
```

- http 统一的返回格式为

``` json
response:
{
  code: 0 //是否成功 0 成功
  , desc: "XXXXX"	//描述
  , data:{...}
}
```
<h3 id="request">2.通用请求参数</h3>

每个请求都要携带的参数，用于描述每个请求的基本信息，后端可以通过这些字段进行接口统计，或APP终端设备的统计，一般放到url参数中。
```
`url?c_version=x.x.x&parameter={}`
```
参数名|类型|说明
---|:---:|---
`c_version` |`string`| 版本号
`parameter` |`string/null`| 请求参数
`其他`|`string`|其他参数

<h3 id="parameter">3.接口`parameter`参数</h3>
  request.parameter

  参数名|类型|说明
  ---|:---:|---
  `mod` | `string` | "Business"
  `act` | `string` | "agent_login_info"
  `aid` | `number` | 账号id
  `key` | `string` | 登录用的key

<h3 id="response">4.响应数据</h3>
reponse

参数名|类型|说明
---|:---:|---
`code` |`number`| 0 为成功
`desc` |`string/null`| 关于参数 code 的描述信息
`data` |`json/null/not`| 请求返回的信息

**特别重要：** 

* <font color=red>`null` - 代表有字段没有赋值</font>
* <font color=red>`not` - 代表没有该字段</font>
* <font color=red>`*` - 参数名带星号, 客户端可以连字段都不写</font>
* <font color=red>注意同和参考的区别：“同”</font>

##### <font color=red>下面只提供 `parameter` 区别传入的参数；下面只提供 `data` 内的返回说明</font>

<h3 id="name">5.命名规范</h3>
- 统一命名：与后端约定好即可（php和js在命名时一般采用下划线风格，而Java中一般采用的是驼峰法），无绝对标准，不要同时存在驼峰"userName"，下划线"phone_number"两种形式就可以了。

- 避免冗余字段：每次在新增接口字段时，注意是否已经存在同一个含义的字段，保持命名一致，不要同时存在"userName"，"username"，"uName"多种同义字段。

- 注释清晰（重要）：每个接口/字段都需要有详细的描述信息，很多时候接口体现业务逻辑，是团队中很重要的文档沉淀，同时，详细的接口文档，可以帮助新人快速熟悉业务。具体示例如下

> 字段描述：数值要有单位，时间要有格式，状态字段要有状态描述，以及不同状态下对于其他字段返回逻辑的关联关系。


字段类型|	字段名称|	说明
---|:---:|---
Boolean|	isVip|	是否时Vip用户，1：是，0：否
金额|	realPay|	订单实际付款金额，单位：元
时间|	payTime|	订单付款时间，单位：毫秒
日期|	payDate	|订单付款日期，格式"yyyy-MM-dd"
状态|	status|	订单状态，1：进行中（payDate不返回），2：待支付（payDate返回），3：已支付（payDate不返回）；（bool以1/0表示，状态从1+开始）



>浮点型计算可能导致精度丢失，为了避免，可以缩小单位进行存储。例：1.5元，后端会以150分存到数据库


<h3 id="clent">6.瘦客户端</h3>

- 客户端尽量只负责展示逻辑，不处理业务逻辑

- 客户端不处理金额的计算

- 客户端少处理请求参数的校验与约束提示

<h3 id="plug">7.扩展性</h3>

> 接口的设计要具有一定的扩展性，考虑到后续版本变化，对于接口，字段的影响及变化。

> 文案与图片

> 对于界面上的文案，图片，特别是"xxx20分钟之内"，"xxx7天到期"这些带数字的文案，不可能永远不变的，即使和PM确认了打死不变，也最好通过常量配置接口进行下发（未下发时使用APP本地默认文案，下发时使用下发的文案），我们的原则是：变与不变都能支持。

<h3 id="compatible">8.兼容性</h3>

> APP1.0在使用接口A，如果此时在开发1.1的时候修改了接口A的逻辑，在1.1发版的时候线上就会出现2个版本的客户端访问同一个接口A，为了保证1.0客户端调用接口A不会出错，就需要通过version字段或path中的"v1/login"，"v2/login"进行区分，不同版本客户端访问同一接口时处理逻辑要各自独立。

> 接口/字段的删除，修改要谨慎：

> 对于已经存在的接口进行修改，需要考虑对线上版本的影响，尽量是数据含义，和新增字段，而不是去修改。

<h3 id="optimize">9.优化</h3>

- 合并接口

> 为了减少客户端和服务器建立连接和断开连接消耗的时间，资源，电量，尽量避免频繁的间隔网络请求。业务场景允许的情况下，尽量1个页面对应1个接口。原先一个页面要通过多个请求获取多种类型数据的情况，最好能通过一个接口全部获取得到。又如：在调用B接口前需要A接口的前置数据的情况，可以让后端支持下，在调用A接口时直接返回B接口的数据，减少类似这种的连续请求。

- 字段精简
> 定义字段名时，在保证良好可读性的前提下，尽量精简，减少流量的消耗

- 无用字段清理
> 每个版本的接口更新后，需要将无用字段进行清理。或者同个接口不同状态下需要返回的字段各不相同的时候，当次请求不需要的字段需要提醒后端不必下发，避免传输无用数据浪费用户流量。

- 
---
# ps:


* [1 test地址]()
`http://test.linfiy.com/mahjong/game_agent/city_agent/index.php`


## 目录

* [1.1登录获取人员基本信息 agent_login_info](#agent_login_info)

### RCP 接口
* [3.1 远程读取代理信息agent_info_game](#agent_info_game)



<h3 id="agent_login_info">1.1 登录获取人员基本信息 agent_login_info</h3>

  request.parameter

  参数名|类型|说明
  ---|:---:|---
  `mod` | `string` | "Business"
  `act` | `string` | "agent_login_info"
  `aid` | `number` | 账号id
  `key` | `string` | 登录用的key

  response.data

   参数名|类型|说明
  ---|:---:|---
  `encrypt_aid` | `string` | 加密id
  `time` | `number` | 加密时间
  `init_time` | `string` | 已提现范围开始
  `extract_date` | `string` | 已提现范围结束
  `extract_start_date` | `string` | 可提现范围开始
  `extract_end_date` | `string` | 可提现范围结束

  response.data.<font color=red>agent_info</font>

  参数名|类型|说明
  ---|:---:|---
  `aid` | `number` | 账号id
  `wx_id` | `string` | 微信号
  `name` | `string` | 姓名
  `provinces` | `string` | 省份
  `city` | `string` | 城市
  `p_aid` | `number` | 上级客户经理
  `type` | `number` | 身份(9总公司 ,8城市合伙人,1会长,2副会长)
  `opend_status` | `number` | 开通方式 1审核通过 2直接开通
  `status` | `number` | 状态 1审核中  2 审核通过  3审核不通过删除 4 查封中
  `audit_eid` | `number` | 操作人id
  `info` | `string` | 备注
  `last_amount` | `number` | 剩余钻数
  `close_down_info` | `string` | 查封备注
  `close_down_time` | `string` | 查封时间
  `init_time` | `string` | 初始化时间
  `month` | `string` | 月份
 



### RCP 接口

  <h3 id="agent_info_game">3.1 远程读取代理信息 agent_info_game</h3>

 request.parameter

  参数名|类型|说明
  ---|:---:|---
  `mod` | `string` | "Business"
  `platform` | `string` | "gfplay"
  `act` | `string` | "agent_info_game"
  `aid` | `number` | 玩家id



