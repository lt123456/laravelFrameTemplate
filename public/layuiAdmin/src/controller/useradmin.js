/**

 @Name：layuiAdmin 用户管理 管理员管理 角色管理
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

  //用户管理
  table.render({
    elem: '#LAY-user-manage'
    ,url: './layuiAdmin/start/json/useradmin/webuser.js' //模拟接口
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
      ,{field: 'id', width: 100, title: 'ID', sort: true}
      ,{field: 'username', title: '用户名', minWidth: 100}
      ,{field: 'avatar', title: '头像', width: 100, templet: '#imgTpl'}
      ,{field: 'phone', title: '手机'}
      ,{field: 'email', title: '邮箱'}
      ,{field: 'sex', width: 80, title: '性别'}
      ,{field: 'ip', title: 'IP'}
      ,{field: 'jointime', title: '加入时间', sort: true}
      ,{title: '操作', width: 150, align:'center', fixed: 'right', toolbar: '#table-useradmin-webuser'}
    ]]
    ,page: true
    ,limit: 30
    ,height: 'full-320'
    ,text: {
    none: '暂无相关数据' //默认：无数据。注：该属性为 layui 2.2.5 开始新增
  }
  });
  
  //监听工具条
  table.on('tool(LAY-user-manage)', function(obj){
    var data = obj.data;
    if(obj.event === 'del'){
      layer.prompt({
        formType: 1
        ,title: '敏感操作，请验证口令'
      }, function(value, index){
        layer.close(index);
        
        layer.confirm('真的删除行么', function(index){
          obj.del();
          layer.close(index);
          
        });
      });
    } else if(obj.event === 'edit'){
      admin.popup({
        title: '编辑用户'
        ,area: ['500px', '450px']
        ,id: 'LAY-popup-user-add'
        ,success: function(layero, index){
          view(this.id).render('user/user/userform', data).done(function(){
            form.render(null, 'layuiadmin-form-useradmin');
            
            //监听提交
            form.on('submit(LAY-user-front-submit)', function(data){
              var field = data.field; //获取提交的字段

              //提交 Ajax 成功后，关闭当前弹层并重载表格
              //$.ajax({});
              layui.table.reload('LAY-user-manage'); //重载表格
              layer.close(index); //执行关闭 
            });
          });
        }
      });
    }
  });

  //管理员管理
  table.render({
    elem: '#LAY-user-back-manage'
    ,url: layui.setter.public_url+'/user/list'
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
      ,{field: 'username', title: '登录名'}
      ,{field: 'nickname', title: '昵称'}
      ,{field: 'remark', title: '备注'}
      ,{field: 'rule', title: '角色'}
      ,{field: 'loginfailure', title: '登陆失败次数', sort: true}
      ,{field: 'logintime', title: '最后登陆时间',width:180}
      ,{field: 'addtime', title: '新增时间',width:180}
      ,{field: 'status', title:'状态', templet: '#buttonTpl', minWidth: 80, align: 'center'}
      ,{title: '操作', width: 150, align: 'center', fixed: 'right', toolbar: '#table-useradmin-admin'}
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

        layer.confirm('确定删除此管理员？', function(index){
           admin.req({
                  url:  layui.setter.public_url+'/user/del' //实际使用请改成服务端真实接口
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
        title: '编辑管理员'
        ,area: ['420px', '450px']
        ,id: 'LAY-popup-user-add'
        ,success: function(layero, index){
          view(this.id).render('auth/adminform', data).done(function(){
            form.render(null, 'layuiadmin-form-admin');
            
            //监听提交
            form.on('submit(LAY-user-back-submit)', function(data){
              var field = data.field; //获取提交的字段
              //提交 Ajax 成功后，关闭当前弹层并重载表格
                  //请求登入接口
                admin.req({
                  url:  layui.setter.public_url+'/user/edit' //实际使用请改成服务端真实接口
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

  //角色管理
  table.render({
    elem: '#LAY-user-back-role'
    ,url: layui.setter.public_url+'/auth/rule' 
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
      ,{field: 'pid', width: 80, title: '父ID'}
      ,{field: 'name',  templet: function(d){return d.level +' '+ d.name},title: '角色名'}
      ,{field: 'remark', title: '具体描述'}
      ,{field: 'status', title:'状态', templet: '#buttonTpl', minWidth: 80, align: 'center'}
      ,{title: '操作', width: 150, align: 'center', fixed: 'right', toolbar: '#table-useradmin-admin'}
    ]]
    ,text: {
    none: '暂无相关数据' //默认：无数据。注：该属性为 layui 2.2.5 开始新增
  }
  });
  
  //监听工具条
  table.on('tool(LAY-user-back-role)', function(obj){
    var data = obj.data;
    if(obj.event === 'del'){
        layer.confirm('确定删除此管理员？', function(index){
           admin.req({
                  url:  layui.setter.public_url+'/auth/groupdel' //实际使用请改成服务端真实接口
                  ,data: {ids:data.id}
                  ,type:'post'
                  ,done: function(res){
                    layui.table.reload('LAY-user-back-role'); //重载表格
                    layer.close(index);
                  }
          }); 
        });
    }else if(obj.event === 'edit'){
      admin.popup({
        title: '编辑角色'
        ,area: ['500px', '480px']
        ,id: 'LAY-popup-user-add'
        ,success: function(layero, index){
          view(this.id).render('auth/groupfrom', data).done(function(){
            form.render(null, 'layuiadmin-form-role');
                 form.on('submit(LAY-user-role-submit)', function(obj){
                    var authids = authtree.getAll('#LAY-auth-tree-index');
                    obj.field.authids = authids;
                    admin.req({
                            url:  layui.setter.public_url+'/auth/groupedit' //实际使用请改成服务端真实接口
                            ,data: obj.field
                            ,type:'post'
                            ,done: function(res){
                               //登入成功的提示与跳转
                                  layer.msg('修改成功', {
                                    offset: '15px'
                                    ,icon: 1
                                    ,time: 1000
                                  }, function(){
                                      layui.table.reload('LAY-user-back-role'); //重载表格
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

    //菜单管理
  table.render({
    elem: '#LAY-user-back-rule'
    ,url: layui.setter.public_url+'/rule/list' 
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
      // ,{field: 'pid', width: 80, title: '父ID'}
      ,{field: 'name',  templet: function(d){return d.level +' '+ d.name},title: '菜单名称'}
      // ,{field: 'icon', title: '图标',templet: function (d) {
      //   return "<i class='layui-icon "+d.icon+"'></i>";
      //      }
      // }
      ,{field: 'url', title: '规则'}
      ,{field: 'remark', title: '具体描述'}
      ,{field: 'weight', title: '权重'}
      ,{field: 'ismenu', title:'是否菜单', templet: '#buttonMenu', minWidth: 80, align: 'center'}
      ,{field: 'status', title:'状态', templet: '#buttonTpl', minWidth: 80, align: 'center'}
      ,{title: '操作', width: 150, align: 'center', fixed: 'right', toolbar: '#table-useradmin-admin'}
    ]]
    ,text: {
    none: '暂无相关数据' //默认：无数据。注：该属性为 layui 2.2.5 开始新增
  }
  });
  
  //监听工具条
  table.on('tool(LAY-user-back-rule)', function(obj){
    var data = obj.data;
    if(obj.event === 'del'){
        layer.confirm('确定删除此管理员？', function(index){
           admin.req({
                  url:  layui.setter.public_url+'/rule/del' //实际使用请改成服务端真实接口
                  ,data: {ids:data.id}
                  ,type:'post'
                  ,done: function(res){
                    layui.table.reload('LAY-user-back-rule'); //重载表格
                    layer.close(index);
                  }
          }); 
        });
    }else if(obj.event === 'edit'){
      admin.popup({
        title: '编辑菜单'
        ,area: ['500px', '480px']
        ,id: 'LAY-popup-user-add'
        ,success: function(layero, index){
          view(this.id).render('auth/ruleform', data).done(function(){
            form.render(null, 'layuiadmin-form-rule');
               form.on('submit(LAY-user-back-submit)', function(data){
                 var field = data.field; //获取提交的字段
                //提交 Ajax 成功后，关闭当前弹层并重载表格
                    //请求登入接口
                  admin.req({
                    url:  layui.setter.public_url+'/rule/edit' //实际使用请改成服务端真实接口
                    ,data: field
                    ,type:'post'
                    ,done: function(res){
                      //登入成功的提示与跳转
                      layer.msg('修改成功', {
                        offset: '15px'
                        ,icon: 1
                        ,time: 1000
                      }, function(){
                          layui.table.reload('LAY-user-back-rule'); //重载表格
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

  exports('useradmin', {})
});