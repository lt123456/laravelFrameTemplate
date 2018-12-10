/**

 @Name：layuiAdmin 嘉宾管理
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
  //嘉宾管理
  table.render({
    elem: '#LAY-user-back-manage'
    ,url: layui.setter.public_url+'/guest/list'
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
      ,{field: 'name', title: '嘉宾姓名',width: 100}
      ,{field: 'ename', title: '嘉宾英文名',width: 120}
      ,{field: 'avatar', title: '头像', width: 100, templet: '#imgTpl'}
      ,{field: 'sex', templet: '#sex', align: 'center',title: '性别'}
      ,{field: 'phone', title: '电话',width: 120}
      ,{field: 'email', title: '邮箱',width: 120}
      ,{field: 'age', title: '年龄',width: 120}
      ,{field: 'title', title: '职务',width: 120}
      ,{field: 'unit', title: '单位',width: 120}
      ,{field: 'addtime', title: '新增时间',width:180}
      ,{title: '操作', width: 150, align: 'center', fixed: 'right', toolbar: '#table-guest-admin'}
    ]]
    ,page: true
    ,text: {
    none: '暂无相关数据' //默认：无数据。注：该属性为 layui 2.2.5 开始新增
  }
  });
  //会议管理
  table.render({
    elem: '#LAY-user-back-manages'
    ,url: layui.setter.public_url+'/guest/get_info_guest'
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
      ,{field: 'hid', templet: function(d){return d.info.name},title: '会议名称',width: 120}
      ,{field: 'gid', templet: function(d){return d.guest.name},title: '嘉宾姓名',width: 120}
      ,{field: 'ename', templet: function(d){return d.guest.ename},title: '嘉宾英文名',width: 120}
      ,{field: 'sex', templet: '#sexs', align: 'center',title: '性别'}
    ]]
    ,page: true
    ,text: {
    none: '暂无相关数据' //默认：无数据。注：该属性为 layui 2.2.5 开始新增
  }
  });
  
  //监听工具条
  table.on('tool(LAY-user-back-manage)', function(obj){
    var data = obj.data;
    console.log(data);
    if(obj.event === 'del'){

        layer.confirm('确定删除？', function(index){
           admin.req({
                  url:  layui.setter.public_url+'/guest/del' //实际使用请改成服务端真实接口
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
          view(this.id).render('guest/guestform', data).done(function(){
            layui.use(['layedit'], function(){
              var $ = layui.$
              ,form = layui.form
              ,upload = layui.upload
              ,layedit = layui.layedit;
              
              layedit.build('content'); //建立编辑器
              layedit.build('econtent'); //建立编辑器
          });
            form.render(null, 'layuiadmin-form-guest');
            
            //监听提交
            form.on('submit(LAY-user-back-submit)', function(data){
              var field = data.field; //获取提交的字段
              //提交 Ajax 成功后，关闭当前弹层并重载表格
                  //请求登入接口
                admin.req({
                  url:  layui.setter.public_url+'/guest/edit' //实际使用请改成服务端真实接口
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


  exports('guest', {})
});