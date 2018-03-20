https://mp.weixin.qq.com/s/xsfpiXC2-i9AsQDdYYvPvw

目录

一、概述
1.1 有关接口
1.1.1接口是纯数据的交互
    APP接口是移动设备和业务之间进行通信的途径。实质就是以特定的规则通过接口直接操作数据库的增删改查。
1.2 接口的分类
1.2.1查询类接口
    查询类接口是指客户端传递一些参数，服务端根据参数依据需求，前往数据库查询需要的结果返回数据的一类接口。
    返回类型一般有两种。第一种是返回一个对象，第二种是返回一个数组对象。
    第一种比如登陆，客户端把用户名密码上传到接口，服务器返回用户的个人信息。
    第二种比如获取客户，客户端把用户的身份信息上传到接口，服务器返回此身份下的所有客户数组集合。

1.2.2 操作类接口
    操作类接口是指，客户端通过接口进行一些增删改的操作。比如新增一个客户，修改客户信息，或者删除一个客户。服务器一般返回执行的状态，有的需要返回执行结果的一些信息，比如新增客户后，返回客户的ID。

1.2.3上传下载类接口
    上传下载类接口是涉及到文件传输的接口。比如上传头像，需要上传图片到服务器，服务端根据需求响应保存并返回结果。比如客户端需要显示用户头像，需要读取网络图片文件，在手机上进行显示。
1.2.4推送类接口
    除了客户端主动去请求服务端，获取需要信息之外。有时候，也存在服务端有消息需要通知客户端的情况，这时候就是服务端向客户端发送消息。这类需求可以通过客户端短时间类循环请求解决，也可以通过第三方专业推送解决。也可以通过自己使用socket或者xmpp等协议进行开发。

二、查询类接口格式规范
2.1获取单条对象信息
2.1.1 请求格式
    |URL	
    |支持格式|JOSN
    |HTTP请求方式|	POST
    |是否登录验证	
    |请求数限制	

    2.1.2参数说明
    |参数名  |	必选 |	类型及范围	|说明
    |xxx	 | true	|   String	  |用户名
						
2.1.3正常返回结果

    返回键	  | 类型	 |返回值	|说明
    ---      |:---:     |---
    result	 |String|结果代码信号	|ok 结果成功， fail结果失败

    Response |	Object  |	        响应体	
    Key1	 |  int     |           响应字段值	
    Key2	 |  String  |           响应字段值	
    Key3	 |  Object  |           响应字段值	可以依然包含对象体
    Key3_Key1|int     |	        响应字段值	  
                
                
    {
        "result":"ok" ,
        "Response": {
            "userName": "Mary",
            "sex": 1,
            "Address": [
                {
                    "city": "JiNan"
                },
                {
                    "county": "LiXia"
                }
            ]
        }
    }

//work
1.登录获取人员基本信息 agent_login_info

    request.parameter

    参数名|类型|说明
    ---|:---:|---
    `mod` | `string` | "Business"
    `platform` | `string` | "gfplay"
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

    response.data.agent_info

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
