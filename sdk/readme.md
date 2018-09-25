# 投标保函服务平台SDK说明文档

# 使用说明

1. 配置好`/config.php`
2. 引入`/api/curl.php`,组织参数,调用已写好的方法即可快速对接
3. 已经写好了调用示例页面,访问`/index.php`即可开始调试

# `curl.php`方法说明

## 我要保函

> 成功则跳转到投标保函服务平台

### 方法`need_guarantee($data)`

### 方法参数`$data`

```
array(
    'yuid':'xx',
    'company_name':'xx'
)
```

## 查询保函

### 方法`search_guarantee($data)`

### 方法参数`$data`

```
array(
    'eno':'806097207781801',
    'pass':'upqA3mhg'
)
```

### 返回

```
单个保函数据
```