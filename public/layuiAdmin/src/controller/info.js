/**

 @Name：layuiAdmin 会议管理
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
    ,url: layui.setter.public_url+'/info/list'
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
      ,{field: 'type', templet: function(d){return d.type.name},title: '会议类别',width: 100}
      ,{field: 'name', title: '会议名称',width: 100}
      ,{field: 'startime', title: '开始时间',width: 120}
      ,{field: 'endtime', title: '结束时间',width: 120}
      ,{field: 'scale', title: '规模'}
      ,{field: 'money', title: '费用'}
      ,{field: 'is_xcx', templet: '#is_xcx', align: 'center',title: '小程序状态',width: 120}
      ,{field: 'is_fx', templet: '#is_fx', align: 'center',title: '分销状态',width: 100}
      ,{field: 'addtime', title: '新增时间',width:180}
      ,{field: 'status', title:'状态', templet: '#buttonTpl', minWidth: 80, align: 'center'}
      ,{title: '操作', width: 150, align: 'center', fixed: 'right', toolbar: '#table-info-admin'}
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
                  url:  layui.setter.public_url+'/info/del' //实际使用请改成服务端真实接口
                  ,data: {ids:data.id}
                  ,type:'post'
                  ,done: function(res){
                    obj.del();
                    layer.close(index);
                  }
          }); 
        });
    }else if(obj.event === 'edit'){
      admin.popup({
        title: '编辑参会人员'
        ,area: ['700px', '750px']
        ,id: 'LAY-popup-useradmin-add'
        ,success: function(layero, index){
          view(this.id).render('info/infoform', data).done(function(){
             layui.use(['laydate','layedit'], function(){
              var layedit = layui.layedit;
              var laydate = layui.laydate;
              var index = layedit.build('content'); //建立编辑器
              var index1 = layedit.build('address_content'); //建立编辑器
              //开始日期
              var insStart = laydate.render({
                elem: '#test-laydate-start'
                ,min: 0
                ,done: function(value, date){
                  //更新结束日期的最小日期
                  insEnd.config.min = lay.extend({}, date, {
                    month: date.month - 1
                  });
                  
                  //自动弹出结束日期的选择器
                 insEnd.config.elem[0].focus();
                }
              });
              
              //结束日期
              var insEnd = laydate.render({
                elem: '#test-laydate-end'
                ,min: 0
                ,done: function(value, date){
                  //更新开始日期的最大日期
                  insStart.config.max = lay.extend({}, date, {
                    month: date.month - 1
                  });
                }
              });
            });
            form.render(null, 'layuiadmin-form-info');
            
            //监听提交
            form.on('submit(LAY-user-back-submit)', function(data){

              var field = data.field; //获取提交的字段
              //提交 Ajax 成功后，关闭当前弹层并重载表格
                  //请求登入接口
                admin.req({
                  url:  layui.setter.public_url+'/info/edit' //实际使用请改成服务端真实接口
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


  exports('info', {})
});