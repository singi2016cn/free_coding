<? defined('INDEX') || exit(); ?>
<html>
<head>
    <meta charset="UTF-8" >
    <script src="//www.layuicdn.com/layui/layui.js"></script>
    <link rel="stylesheet" type="text/css" href="//www.layuicdn.com/layui/css/layui.css" />
    <style>
        *{padding:0;margin:0;}
        /*box-header*/
        .input-group label{width: 120px;}
        .group-line{float: left;display: flex;margin:10px 0;}
        .compare{background: #3c8dbc;margin:0 50px;}
        .copy-all{margin-right:30px;}
        .option{float: left;margin-left: 100px;margin:20px 0;}
        .option > div{float: left;width:200px;height:30px;}

        .table td{position:relative}
        .pre-diff{padding:10px;max-height: 40px;overflow: hidden;font-size:12px;box-sizing: border-box;}
        #table-diff .op-box{top: 95px;right: 20px;position: absolute}
        pre {
            display: block;
            padding: 9.5px;
            font-size: 13px;
            line-height: 1.42857143;
            color: #333;
            word-break: break-all;
            word-wrap: break-word;
            background-color: #f5f5f5;
            border: 1px solid #ccc;
            border-radius: 4px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
<!-- Content Header (Page header) -->

    <!-- Main content -->
    <div class="content" style="padding: 30px;max-height:100vh">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header" style="padding-top: 1px;">
                        <form class="layui-form" action="">
                            <div class="input-group group-line">
                                <label class="layui-form-label">源库</label>
                                <select name="db_source" class="form-control select2">
                                    <option value="0">请选择</option>
                                    <? foreach ($dir as $file){ ?>
                                    <option value="<?=$file?>"><?=$file?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="input-group group-line">
                                <label class="layui-form-label">目标库</label>
                                <select name="db_target" class="form-control select2">
                                    <option value="0">请选择</option>
                                    <? foreach ($dir as $file){ ?>
                                        <option value="<?=$file?>"><?=$file?></option>
                                    <? } ?>
                                </select>
                                <a class="layui-btn compare" href="javascript:;">对比</a>
                                <a class="layui-btn copy-all" href="javascript:;">复制所有</a>
                            </div>

                            <div class="input-group option" id="option"></div>
                        </form>

                    </div>
                    <!-- /.box-header -->
                    <div class="box-body" style="margin-top:60px;">
                        <table class="layui-table table-bordered table-hover">
                            <colgroup>
                                <col width="100">
                                <col width="100">
                                <col width="600">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>差异</th>
                                    <th>表名</th>
                                    <th>差异sql</th>
                                </tr>
                            </thead>
                            <tbody id="table-diff"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
<script>
    window.onload = function() {
        layui.use(['layer','form'], function () {
            var $ = layui.$;
            var current_option = [];
            var diff_data = [];
            var diff_type = {};
            var diff_type_default = {

                SetDBCharset: {name:'数据库字符集差异'},
                SetDBCollation: {name:'数据库排序规则差异'},
                AlterTableEngine: {name:'数据库引擎差异'},

                AddTable: {name:'缺少表'},
                DropTable: {name:'表多余'},
                AlterTableCollation: {name:'表字符集差异'},

                AlterTableAddColumn: {name:'缺少字段'},
                AlterTableDropColumn: {name:'字段多余'},
                AlterTableChangeColumn: {name:'字段信息差异'},

                AlterTableAddConstraint: {name:'缺少表约束'},
                AlterTableDropConstraint: {name:'表约束多余'},
                AlterTableChangeConstraint: {name:'表约束差异'},

                AlterTableAddKey: {name:'缺少KEY'},
                AlterTableDropKey: {name:'KEY多余'},
                AlterTableChangeKey: {name:'KEY差异'},

                InsertData: {name:'缺少数据'},
                DeleteData: {name:'数据多余'},
                UpdateData: {name:'数据差异'}
            };
            //获取差异
            $('.compare').click(function () {
                var data = $(this).parents('form').serialize();

                layer.load();
                $.post('?op=get_diff', data, (res) => {
                    layer.closeAll();
                    if (res.code == 1) {
                        diff_data = res.data;
                        diff(true);
                    } else {
                        layer.msg(res.msg || '对比失败');
                    }
                });
            });

            //单项复制
            $('#table-diff').on('click', '.copy', function () {
                copy_text($(this).parent().prev().text());

            }).on('click', '.open', function () {//展开|收起
                if ($(this).hasClass('layui-icon-down')) {
                    $(this).removeClass('layui-icon-down').addClass('layui-icon-up').parent().prev().css('max-height', 'none');
                } else {
                    $(this).removeClass('layui-icon-up').addClass('layui-icon-down').parent().prev().css('max-height', '40px');
                }
            })

            //选择显示条件选项
            $('#option').click(function(){
                refresh_option();
                setTimeout(diff,100);
            });

            //复制所有
            $('.copy-all').click(function(){
                refresh_option();
                if(current_option.length == 0){
                    layer.msg('当前没有数据！');
                    return false;
                }
                var text = '';
                $(diff_data).each(function () {
                    if($.inArray(this.type, current_option) < 0){
                        return;
                    }
                    text += this.sql + "\n";
                })
                copy_text(text);
            });

            //显示差异列表
            function diff(is_first=false) {
                diff_type = JSON.parse(JSON.stringify(diff_type_default));
                var html = '';
                $(diff_data).each(function () {
                    if(!is_first && $.inArray(this.type, current_option) < 0){
                        return;
                    }
                    var info = get_diff_info(this);
                    html += "<tr>" +
                        "<td>" + info.diffName + "</td>" +
                        "<td>" + info.tableName + "</td>" +
                        "<td>" +
                        "<pre class='pre-diff'>  源库：" + info.newValue + "</pre>" +
                        "<pre class='pre-diff'>目标库：" + info.oldValue + "</pre>" +
                        "<pre class='pre-diff'>" + this.sql + "</pre>" +
                        "<div class='op-box'><i class='layui-icon layui-icon-down layui-btn layui-btn-xs layui-btn-primary open'></i><input type='button' value='复制' class='layui-btn layui-btn-xs layui-btn-primary copy' /></div>" +
                        "</td>" +
                        "</tr>";
                });
                if (!html) {
                    html = "<tr><td colspan='3' style='text-align: center;'>无差异</td></tr>"
                }
                $('#table-diff').html(html);

                if(is_first){
                    var option_html = '';
                    for (index in diff_type){
                        var type = diff_type[index];
                        if(type.num>0){
                            option_html += '<div><input type="checkbox" value="'+index+'" name="types[]" title="'+type.name+'('+type.num+')" lay-skin="primary" checked></div>'
                        }
                    }
                    $('#option').html(option_html);

                    layui.form.render();
                }
            }

            //获取差异信息
            function get_diff_info(diff) {
                var info = {};

                info.tableName = diff.table || '';
                if(diff_type[diff.type]){
                    info.diffName = diff_type[diff.type].name;
                    diff_type[diff.type].num ? diff_type[diff.type].num++ : diff_type[diff.type].num = 1;
                }else{
                    info.diffName = '未知差异';
                }

                if (diff.diff && diff.diff.newValue) {
                    info.newValue = diff.diff.newValue;
                    info.oldValue = diff.diff.oldValue;
                } else if (diff.type == 'SetDBCharset') {
                    info.newValue = diff.charset;
                    info.oldValue = diff.prevCharset;
                } else if (diff.type == 'SetDBCollation') {
                    info.newValue = diff.collation;
                    info.oldValue = diff.prevCollation;
                } else if (diff.type == 'AlterTableEngine') {
                    info.newValue = diff.engine;
                    info.oldValue = diff.prevEngine;
                } else if (diff.type.search(/Add|Insert/) > -1) {
                    info.newValue = get_value(diff);
                    info.oldValue = '';
                } else if (diff.type.search(/Drop|Delete/) > -1) {
                    info.newValue = '';
                    info.oldValue = get_value(diff);
                }

                return info;
            }
            function get_value(diff) {
                return diff.column || diff.key || diff.table || '';
            }
            //刷新条件选项
            function refresh_option(){
                current_option = [];
                $("input[name='types[]']:checked").each(function(){
                    current_option.push($(this).val());
                });
            }
            //复制
            function copy_text(text){
                var input = document.createElement("textarea");
                input.value = text;
                document.body.appendChild(input);
                input.select();
                document.execCommand("Copy");
                input.parentNode.removeChild(input);
                layer.msg('复制成功');
                return true;
            }

        });
    }

</script>
</html>