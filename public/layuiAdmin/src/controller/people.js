/**

 @Name：layuiAdmin 参会人员管理 管理员管理 角色管理
 @Author：star1029
 @Site：http://www.layui.com/admin/
 @License：LPPL
    
 */


layui.define(['table','authtree','form'], function(exports){
  var $ = layui.$
  ,admin = layui.admin
  ,view = layui.view
  ,table = layui.table
  ,form = layui.form;
  var authtree = layui.authtree;
  //管理员管理
  table.render({
    elem: '#LAY-user-back-manage'
    ,url: layui.setter.public_url+'/people/list'
    ,method:'post'
    ,headers: {
    accesstoken: layui.data('layuiAdmin').accesstoken
    }  
    ,done: function(res){
      //如果是异步请求数据方式，res即为你接口返回的信息。
      //如果是直接赋值的方式，res即为：{data: [], count: 99} data为当前页数据、count为数据总长度
      if(res.code !== 0){
        layer.msg(res.message, {
              offset: '30px'
              ,icon: 2
              ,time: 2000
          })
      }
    }
    ,cols: [[
      {type: 'checkbox', fixed: 'left'}
      ,{field: 'id', width: 80, title: 'ID', sort: true}
      ,{field: 'hid', templet: function(d){return d.info.name},title: '会议名称',width: 100}
      ,{field: 'name', title: '姓名'}
      ,{field: 'phone', title: '手机号',width: 100}
      ,{field: 'title', title: '职务',width: 100}
      ,{field: 'unit', title: '单位'}
      ,{field: 'email', title: '邮箱'}
      ,{field: 'card', title: '身份证号码',width: 100}
      ,{field: 'vip',templet: function(d){
        var vip = '';
        if(d.vip == 0) vip = '普通会员';
        if(d.vip == 1) vip = '乐享达人';
        if(d.vip == 2) vip = '阅读达人';
        if(d.vip == 3) vip = '医杰汇会员';
        return vip

      }, title: '会员信息',width: 100}
      // ,{field: 'id', title: '胸卡打印',width: 100}
      ,{field: 'memcard', title: '唯一标识',width: 100}
      // ,{field: 'id', title: '来源'}
      // ,{field: 'id', title: '住宿登记',width: 100}
      ,{field: 'num', title: '单位人数',width: 100}
      // ,{field: 'id', title: '会务费',width: 100}
      // ,{field: 'id', title: '住宿费',width: 100}
      ,{field: 'money', title: '单位总费用',width: 100}
      ,{field: 'number', title: '收据编号',width: 100}
      ,{field: 'pay_type', title: '支付类型',width: 100}
      ,{field: 'typeid', templet: function(d){return d.type.name},title: '参会类型',width: 100}
      // ,{field: 'sid', title: '子会'}
      ,{field: 'is_pay', templet: '#is_pay', align: 'center', title: '支付状态',width: 100}
      // ,{field: 'id', title: '住宿状态',width: 100}
      ,{field: 'is_sign',  templet: '#is_sign', align: 'center',title: '签到状态',width: 100}
      ,{field: 'is_file',  templet: '#is_file', align: 'center',title: '资料领取',width: 100}
      ,{field: 'is_fp',  templet: '#is_fp', align: 'center',title: '发票领取',width: 100}
      // ,{field: 'id', title: '备注'}
      ,{field: 'addtime', title: '新增时间',width:180}
      // ,{field: 'status', title:'状态', templet: '#buttonTpl', minWidth: 80, align: 'center'}
      ,{title: '操作', width: 150, align: 'center', fixed: 'right', toolbar: '#table-people-admin'}
    ]]
    ,page: true
    ,text: {
    none: '暂无相关数据' //默认：无数据。注：该属性为 layui 2.2.5 开始新增
  }
  });
  
  //监听工具条
  table.on('tool(LAY-user-back-manage)', function(obj){
    var data = obj.data;
    if(obj.event === 'del'){

        layer.confirm('确定删除？', function(index){
           admin.req({
                  url:  layui.setter.public_url+'/people/del' //实际使用请改成服务端真实接口
                  ,data: {ids:data.id}
                  ,type:'post'
                  ,done: function(res){
                    obj.del();
                    layer.close(index);
                  }
          }); 
        });
    }else if(obj.event === 'edit'){
      data.is_pay  = data.sign.is_pay;
      data.is_file = data.sign.is_file;
      data.is_fp   = data.sign.is_fp;
      data.is_sign = data.sign.is_sign;
      admin.popup({
        title: '编辑参会人员'
        ,area: ['700px', '750px']
        ,id: 'LAY-popup-useradmin-add'
        ,success: function(layero, index){
          view(this.id).render('people/peopleform', data).done(function(){
            form.render(null, 'layuiadmin-form-people');
            var fp = $('.fp').find('em').text();
           if(fp == '是'){
              $('#fp').show();
            }
            //监听提交
            form.on('submit(LAY-user-back-submit)', function(data){
              var field = data.field; //获取提交的字段
              //提交 Ajax 成功后，关闭当前弹层并重载表格
                  //请求登入接口
                admin.req({
                  url:  layui.setter.public_url+'/people/edit' //实际使用请改成服务端真实接口
                  ,data: field
                  ,type:'post'
                  ,done: function(res){
                    //登入成功的提示与跳转
                    layer.msg('修改成功', {
                      offset: '15px'
                      ,icon: 1
                      ,time: 1000
                    }, function(){
                        layui.table.reload('LAY-user-back-manage'); //重载表格
                        layer.close(index); //执行关闭 
                    });
                  }
                });
                
            });
          });
        }
      });
    }
  });


  exports('people', {})
});