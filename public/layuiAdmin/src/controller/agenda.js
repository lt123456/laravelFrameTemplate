/**

 @Name：layuiAdmin 日程管理
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
    ,url: layui.setter.public_url+'/agenda/list'
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
      ,{field: 'title', title: '标题',width: 120}
      ,{field: 'date', title: '活动时间',width: 120}
      ,{field: 'startime', title: '开始时间',width: 120}
      ,{field: 'endtime', title: '结束时间',width: 120}
      ,{field: 'remark', title: '备注',width: 120}
      ,{field: 'sort', width: 80, title: '排序', sort: true}
      ,{field: 'addtime', title: '新增时间',width:180}
      ,{field: 'display', title:'状态', templet: '#buttonTpl', minWidth: 80, align: 'center'}
      ,{title: '操作', width: 150, align: 'center', fixed: 'right', toolbar: '#table-agenda-admin'}
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
                  url:  layui.setter.public_url+'/agenda/del' //实际使用请改成服务端真实接口
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
        ,area: ['850px', '750px']
        ,id: 'LAY-popup-useradmin-add'
        ,success: function(layero, index){
          view(this.id).render('agenda/agendaform', data).done(function(){
             layui.use(['laydate','layedit'], function(){
              var layedit = layui.layedit;
              var laydate = layui.laydate;
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
              laydate.render({
                elem: '#test-laydate-range-time'
                ,type: 'time'
              });
              laydate.render({
                elem: '#test-laydate-range-times'
                ,type: 'time'
              });
            });
            form.render(null, 'layuiadmin-form-agenda');
            
            //监听提交
            form.on('submit(LAY-user-back-submit)', function(data){

              var field = data.field; //获取提交的字段
              //提交 Ajax 成功后，关闭当前弹层并重载表格
                  //请求登入接口
                admin.req({
                  url:  layui.setter.public_url+'/agenda/edit' //实际使用请改成服务端真实接口
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


  exports('agenda', {})
});